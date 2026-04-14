<?php
/**
 * SimpleXlsx — Gerçek .xlsx dosyası üretici (ZipArchive tabanlı, sıfır bağımlılık)
 *
 * Kullanım:
 *   $xlsx = new SimpleXlsx('Sayfa Adı');
 *   $xlsx->addRow(['Başlık1','Başlık2'], SimpleXlsx::STYLE_HEADER);
 *   $xlsx->addRow(['Veri',  42],        SimpleXlsx::STYLE_NORMAL);
 *   $xlsx->download('dosya.xlsx');   // tarayıcıya gönder
 *   $xlsx->save('/tam/yol.xlsx');    // diske kaydet
 */
class SimpleXlsx
{
    /* ---- Hazır stiller ---- */
    const STYLE_NORMAL   = 0;   // Varsayılan
    const STYLE_HEADER   = 1;   // Koyu mavi zemin, beyaz kalın metin
    const STYLE_BOLD     = 2;   // Kalın metin
    const STYLE_CENTER   = 3;   // Ortalanmış
    const STYLE_GREEN    = 4;   // Yeşil zemin (doğru/geçti)
    const STYLE_RED      = 5;   // Kırmızı zemin (yanlış/kaldı)
    const STYLE_SUBHEAD  = 6;   // Açık mavi zemin (alt başlık)
    const STYLE_TITLE    = 7;   // Büyük kalın başlık
    const STYLE_MUTED    = 8;   // Gri metin
    const STYLE_NUM      = 9;   // Sayı, ortalanmış
    const STYLE_BOLD_CTR = 10;  // Kalın + ortalanmış
    const STYLE_WARN     = 11;  // Turuncu zemin (bekliyor)

    private string $sheetName;
    private array  $rows    = [];   // [cells[], styleId, rowHeight?]
    private array  $colWidths = []; // sütun indeksi => genişlik
    private array  $merges  = [];   // "A1:D1" birleştirme aralıkları

    public function __construct(string $sheetName = 'Sayfa1')
    {
        $this->sheetName = $sheetName;
    }

    /** Satır ekle: $cells = [değer, değer, ...], her hücre [değer, stil] veya sadece değer olabilir */
    public function addRow(array $cells, int $defaultStyle = self::STYLE_NORMAL, float $height = 0): void
    {
        $this->rows[] = [$cells, $defaultStyle, $height];
    }

    /** Boş satır */
    public function addEmptyRow(): void
    {
        $this->rows[] = [[], self::STYLE_NORMAL, 0];
    }

    /** Sütun genişliği ayarla (0-tabanlı indeks) */
    public function setColWidth(int $colIdx, float $width): void
    {
        $this->colWidths[$colIdx] = $width;
    }

    /** Otomatik sütun genişliği (tüm içeriklere göre tahmin) */
    public function autoColWidths(int $minWidth = 8, int $maxWidth = 50): void
    {
        $maxLen = [];
        foreach ($this->rows as [$cells]) {
            foreach ($cells as $ci => $cell) {
                $val = is_array($cell) ? $cell[0] : $cell;
                $len = mb_strlen((string)$val);
                if (!isset($maxLen[$ci]) || $len > $maxLen[$ci]) {
                    $maxLen[$ci] = $len;
                }
            }
        }
        foreach ($maxLen as $ci => $len) {
            $this->colWidths[$ci] = max($minWidth, min($maxWidth, $len + 2));
        }
    }

    // ============================================================
    //  DOSYA OLUŞTUR
    // ============================================================

    /** Tarayıcıya indir */
    public function download(string $filename): void
    {
        $data    = $this->build();
        // RFC 5987 — UTF-8 dosya adı desteği (ş, ç, ğ vb. korunur)
        $ascii   = self::toAsciiFilename($filename);
        $encoded = rawurlencode($filename);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $ascii . '"; filename*=UTF-8\'\'' . $encoded);
        header('Content-Length: ' . strlen($data));
        header('Cache-Control: no-cache, no-store');
        header('Pragma: no-cache');
        echo $data;
        exit;
    }

    /** Türkçe karakterleri ASCII'ye dönüştür (fallback dosya adı için) */
    private static function toAsciiFilename(string $name): string
    {
        $tr = ['ş'=>'s','Ş'=>'S','ç'=>'c','Ç'=>'C','ğ'=>'g','Ğ'=>'G',
               'ı'=>'i','İ'=>'I','ö'=>'o','Ö'=>'O','ü'=>'u','Ü'=>'U'];
        return preg_replace('/[^A-Za-z0-9_.\-]/', '_', strtr($name, $tr));
    }

    /** Diske kaydet */
    public function save(string $path): void
    {
        file_put_contents($path, $this->build());
    }

    // ============================================================
    //  İÇ YAPI
    // ============================================================

    private function build(): string
    {
        // Paylaşılan diziler (sharedStrings)
        $sst    = [];    // string → indeks
        $sstArr = [];    // indeks → string

        // Hücre verisini topla
        $compiledRows = [];
        foreach ($this->rows as [$cells, $defaultStyle, $height]) {
            $compiled = [];
            foreach ($cells as $ci => $cell) {
                if (is_array($cell)) {
                    [$val, $style] = [$cell[0], $cell[1] ?? $defaultStyle];
                } else {
                    [$val, $style] = [$cell, $defaultStyle];
                }

                if (is_numeric($val) && $val !== '') {
                    $compiled[] = ['n', $val, $style];
                } elseif (is_string($val) && str_starts_with($val, '=')) {
                    $compiled[] = ['f', $val, $style];
                } elseif ($val === null || $val === '') {
                    $compiled[] = ['e', '', $style];
                } else {
                    $s = (string)$val;
                    if (!isset($sst[$s])) {
                        $sst[$s]      = count($sstArr);
                        $sstArr[]     = $s;
                    }
                    $compiled[] = ['s', $sst[$s], $style];
                }
            }
            $compiledRows[] = [$compiled, $height];
        }

        // XML parçaları
        $sheetXml      = $this->buildSheet($compiledRows);
        $stylesXml     = $this->buildStyles();
        $sstXml        = $this->buildSST($sstArr);
        $workbookXml   = $this->buildWorkbook();
        $wbRelsXml     = $this->buildWbRels();
        $relsXml       = $this->buildRels();
        $contentTypes  = $this->buildContentTypes();
        $appXml        = $this->buildApp();
        $coreXml       = $this->buildCore();

        // ZIP oluştur (ZipArchive gerekmez — saf PHP ile)
        $files = [
            '[Content_Types].xml'           => $contentTypes,
            '_rels/.rels'                   => $relsXml,
            'xl/workbook.xml'               => $workbookXml,
            'xl/_rels/workbook.xml.rels'    => $wbRelsXml,
            'xl/worksheets/sheet1.xml'      => $sheetXml,
            'xl/styles.xml'                 => $stylesXml,
            'xl/sharedStrings.xml'          => $sstXml,
            'docProps/app.xml'              => $appXml,
            'docProps/core.xml'             => $coreXml,
        ];
        return self::buildZip($files);
    }

    // ---- Sütun harfi ----
    private static function colLetter(int $n): string  // 0-tabanlı
    {
        $l = '';
        $n++;
        while ($n > 0) {
            $n--;
            $l = chr(65 + $n % 26) . $l;
            $n = intdiv($n, 26);
        }
        return $l;
    }

    // ---- Sayfa XML ----
    private function buildSheet(array $compiledRows): string
    {
        $colDefs = '';
        if ($this->colWidths) {
            $colDefs = '<cols>';
            foreach ($this->colWidths as $ci => $w) {
                $col = $ci + 1;
                $colDefs .= sprintf('<col min="%d" max="%d" width="%.2f" customWidth="1"/>', $col, $col, $w);
            }
            $colDefs .= '</cols>';
        }

        $rows = '';
        foreach ($compiledRows as $ri => [$cells, $height]) {
            $rowNum  = $ri + 1;
            $hAttr   = $height > 0 ? sprintf(' ht="%.1f" customHeight="1"', $height) : '';
            $rowXml  = sprintf('<row r="%d"%s>', $rowNum, $hAttr);

            foreach ($cells as $ci => [$type, $val, $style]) {
                $addr = self::colLetter($ci) . $rowNum;
                if ($type === 'e') {
                    $rowXml .= sprintf('<c r="%s" s="%d"/>', $addr, $style);
                } elseif ($type === 'n') {
                    $rowXml .= sprintf('<c r="%s" t="n" s="%d"><v>%s</v></c>', $addr, $style, htmlspecialchars((string)$val, ENT_XML1));
                } elseif ($type === 'f') {
                    $rowXml .= sprintf('<c r="%s" s="%d"><f>%s</f></c>', $addr, $style, htmlspecialchars(ltrim($val,'='), ENT_XML1));
                } else { // 's' = sharedString
                    $rowXml .= sprintf('<c r="%s" t="s" s="%d"><v>%d</v></c>', $addr, $style, $val);
                }
            }
            $rowXml .= '</row>';
            $rows   .= $rowXml;
        }

        $mergeXml = '';
        if ($this->merges) {
            $mergeXml = '<mergeCells count="' . count($this->merges) . '">';
            foreach ($this->merges as $m) {
                $mergeXml .= '<mergeCell ref="' . $m . '"/>';
            }
            $mergeXml .= '</mergeCells>';
        }

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"
           xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
<sheetData>' . $colDefs . $rows . '</sheetData>' . $mergeXml . '
<pageSetup orientation="landscape" fitToPage="1" fitToWidth="1" fitToHeight="0"/>
</worksheet>';
    }

    // ---- Stiller XML ----
    private function buildStyles(): string
    {
        // Yazı tipleri
        $fonts = [
            '<font><sz val="10"/><name val="Calibri"/></font>',                                         // 0 normal
            '<font><b/><sz val="10"/><color rgb="FFFFFFFF"/><name val="Calibri"/></font>',              // 1 header (beyaz kalın)
            '<font><b/><sz val="10"/><name val="Calibri"/></font>',                                     // 2 kalın
            '<font><sz val="10"/><name val="Calibri"/></font>',                                         // 3 center/num
            '<font><b/><sz val="14"/><name val="Calibri"/></font>',                                     // 4 büyük başlık
            '<font><sz val="10"/><color rgb="FF64748B"/><name val="Calibri"/></font>',                  // 5 gri/muted
            '<font><b/><sz val="10"/><color rgb="FF065F46"/><name val="Calibri"/></font>',              // 6 koyu yeşil kalın
            '<font><b/><sz val="10"/><color rgb="FF991B1B"/><name val="Calibri"/></font>',              // 7 koyu kırmızı kalın
        ];

        // Dolgular (0,1 boş olmak zorunda OpenXML spesifikasyonunda)
        $fills = [
            '<fill><patternFill patternType="none"/></fill>',                                           // 0 yok
            '<fill><patternFill patternType="gray125"/></fill>',                                        // 1 gray125 (zorunlu)
            '<fill><patternFill patternType="solid"><fgColor rgb="FF1D4ED8"/></patternFill></fill>',    // 2 koyu mavi (header)
            '<fill><patternFill patternType="solid"><fgColor rgb="FFD1FAE5"/></patternFill></fill>',    // 3 açık yeşil
            '<fill><patternFill patternType="solid"><fgColor rgb="FFFEE2E2"/></patternFill></fill>',    // 4 açık kırmızı
            '<fill><patternFill patternType="solid"><fgColor rgb="FFDBEAFE"/></patternFill></fill>',    // 5 açık mavi (subhead)
            '<fill><patternFill patternType="solid"><fgColor rgb="FFEFF6FF"/></patternFill></fill>',    // 6 çok açık mavi (title)
            '<fill><patternFill patternType="solid"><fgColor rgb="FFFEF3C7"/></patternFill></fill>',    // 7 sarı/turuncu (warn)
        ];

        // Kenarlıklar
        $borders = [
            '<border><left/><right/><top/><bottom/><diagonal/></border>',                               // 0 yok
            '<border>
                <left style="thin"><color rgb="FFD0D0D0"/></left>
                <right style="thin"><color rgb="FFD0D0D0"/></right>
                <top style="thin"><color rgb="FFD0D0D0"/></top>
                <bottom style="thin"><color rgb="FFD0D0D0"/></bottom>
            </border>',                                                                                  // 1 ince gri
            '<border>
                <left style="medium"><color rgb="FF1D4ED8"/></left>
                <right style="medium"><color rgb="FF1D4ED8"/></right>
                <top style="medium"><color rgb="FF1D4ED8"/></top>
                <bottom style="medium"><color rgb="FF1D4ED8"/></bottom>
            </border>',                                                                                  // 2 mavi (header)
        ];

        // Sayı biçimleri (164+ özel)
        $numFmts = '<numFmts count="2">
            <numFmt numFmtId="164" formatCode="0.0"/>
            <numFmt numFmtId="165" formatCode="0.0&quot;%&quot;"/>
        </numFmts>';

        // xf (hücre biçim) tablosu:
        // [fontId, fillId, borderId, numFmtId, hAlign, wrapText]
        $xfs = [
            [0, 0, 1, 0,   'left',   false],  // 0 NORMAL
            [1, 2, 2, 0,   'center', false],  // 1 HEADER
            [2, 0, 1, 0,   'left',   false],  // 2 BOLD
            [0, 0, 1, 0,   'center', false],  // 3 CENTER
            [6, 3, 1, 0,   'center', false],  // 4 GREEN
            [7, 4, 1, 0,   'center', false],  // 5 RED
            [2, 5, 1, 0,   'left',   false],  // 6 SUBHEAD
            [4, 6, 0, 0,   'left',   false],  // 7 TITLE
            [5, 0, 0, 0,   'left',   false],  // 8 MUTED
            [0, 0, 1, 0,   'center', false],  // 9 NUM
            [2, 0, 1, 0,   'center', false],  // 10 BOLD_CTR
            [2, 7, 1, 0,   'center', false],  // 11 WARN
        ];

        $fontXml   = '<fonts count="' . count($fonts) . '">' . implode('', $fonts) . '</fonts>';
        $fillXml   = '<fills count="' . count($fills) . '">' . implode('', $fills) . '</fills>';
        $borderXml = '<borders count="' . count($borders) . '">' . implode('', $borders) . '</borders>';

        $cellXfsXml = '<cellXfs count="' . count($xfs) . '">';
        foreach ($xfs as $xf) {
            [$fi, $fli, $bi, $nf, $ha, $wrap] = $xf;
            $applyFill   = $fli > 0 ? ' applyFill="1"' : '';
            $applyFont   = $fi  > 0 ? ' applyFont="1"' : '';
            $applyBorder = $bi  > 0 ? ' applyBorder="1"' : '';
            $applyNum    = $nf  > 0 ? ' applyNumberFormat="1"' : '';
            $wrapAttr    = $wrap ? ' wrapText="1"' : '';
            $cellXfsXml .= sprintf(
                '<xf numFmtId="%d" fontId="%d" fillId="%d" borderId="%d" xfId="0"%s%s%s%s applyAlignment="1">
                    <alignment horizontal="%s" vertical="center"%s/>
                </xf>',
                $nf, $fi, $fli, $bi, $applyNum, $applyFont, $applyFill, $applyBorder,
                $ha, $wrapAttr
            );
        }
        $cellXfsXml .= '</cellXfs>';

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
' . $numFmts . $fontXml . $fillXml . $borderXml . '
<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>
' . $cellXfsXml . '
</styleSheet>';
    }

    // ---- Paylaşılan diziler ----
    private function buildSST(array $strings): string
    {
        $count = count($strings);
        $items = '';
        foreach ($strings as $s) {
            $items .= '<si><t xml:space="preserve">' . htmlspecialchars($s, ENT_XML1 | ENT_QUOTES, 'UTF-8') . '</t></si>';
        }
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<sst xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" count="' . $count . '" uniqueCount="' . $count . '">'
            . $items . '</sst>';
    }

    // ---- Çalışma kitabı ----
    private function buildWorkbook(): string
    {
        $name = htmlspecialchars($this->sheetName, ENT_XML1);
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"
          xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
<sheets><sheet name="' . $name . '" sheetId="1" r:id="rId1"/></sheets>
</workbook>';
    }

    private function buildWbRels(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>
<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles"    Target="styles.xml"/>
<Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/sharedStrings" Target="sharedStrings.xml"/>
</Relationships>';
    }

    private function buildRels(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>
<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/package/2006/relationships/metadata/core-properties" Target="docProps/core.xml"/>
<Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/extended-properties" Target="docProps/app.xml"/>
</Relationships>';
    }

    private function buildContentTypes(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
<Default Extension="xml"  ContentType="application/xml"/>
<Override PartName="/xl/workbook.xml"            ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>
<Override PartName="/xl/worksheets/sheet1.xml"   ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>
<Override PartName="/xl/styles.xml"              ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>
<Override PartName="/xl/sharedStrings.xml"       ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sharedStrings+xml"/>
<Override PartName="/docProps/core.xml"          ContentType="application/vnd.openxmlformats-package.core-properties+xml"/>
<Override PartName="/docProps/app.xml"           ContentType="application/vnd.openxmlformats-officedocument.extended-properties+xml"/>
</Types>';
    }

    private function buildApp(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Properties xmlns="http://schemas.openxmlformats.org/officeDocument/2006/extended-properties">
<Application>SınıfPro</Application>
</Properties>';
    }

    private function buildCore(): string
    {
        $dt = date('Y-m-d\TH:i:s\Z');
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<cp:coreProperties xmlns:cp="http://schemas.openxmlformats.org/package/2006/metadata/core-properties"
                   xmlns:dc="http://purl.org/dc/elements/1.1/"
                   xmlns:dcterms="http://purl.org/dc/terms/">
<dc:creator>SınıfPro</dc:creator>
<dcterms:created xsi:type="dcterms:W3CDTF" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">' . $dt . '</dcterms:created>
</cp:coreProperties>';
    }

    // ============================================================
    //  SAF PHP ZIP YAZICI (ZipArchive eklentisi gerekmez)
    //  ZIP 2.0 spec: local headers + central directory + EOCD
    // ============================================================
    private static function buildZip(array $files): string
    {
        $localHeaders   = '';
        $centralDir     = '';
        $offset         = 0;
        [$dosTime, $dosDate] = self::dosDateTime();

        foreach ($files as $name => $data) {
            $nameBytes  = $name;
            $nameLen    = strlen($nameBytes);
            $dataLen    = strlen($data);
            $crc        = crc32($data);

            // Raw deflate — ZIP method 8 için zlib sarmalı OLMAMALI (ZLIB_ENCODING_RAW)
            $compressed = zlib_encode($data, ZLIB_ENCODING_RAW, 6);
            if ($compressed === false || strlen($compressed) >= $dataLen) {
                // Sıkıştırma faydasız, stored kullan
                $method   = 0;   // STORED
                $compData = $data;
                $compLen  = $dataLen;
            } else {
                $method   = 8;   // DEFLATED
                $compData = $compressed;
                $compLen  = strlen($compressed);
            }

            // Local file header (30 bayt sabit kısım)
            // sig(4) verNeeded(2) flags(2) method(2) time(2) date(2) crc(4) compLen(4) dataLen(4) nameLen(2) extraLen(2)
            $localHeader = pack('VvvvvvVVVvv',
                0x04034b50,  // signature
                20,          // version needed (2.0)
                0,           // general purpose bit flag
                $method,     // compression method
                $dosTime,    // last mod file time  (16-bit)
                $dosDate,    // last mod file date  (16-bit)
                $crc,        // CRC-32
                $compLen,    // compressed size     (32-bit)
                $dataLen,    // uncompressed size   (32-bit)
                $nameLen,    // filename length     (16-bit)
                0            // extra field length  (16-bit)
            ) . $nameBytes . $compData;

            // Central directory entry (46 bayt sabit kısım)
            // sig(4) verBy(2) verNeeded(2) flags(2) method(2) time(2) date(2)
            // crc(4) compLen(4) dataLen(4) nameLen(2) extraLen(2) commentLen(2)
            // diskStart(2) intAttr(2) extAttr(4) offset(4)
            $centralDir .= pack('VvvvvvvVVVvvvvvVV',
                0x02014b50,  // signature
                20,          // version made by
                20,          // version needed
                0,           // flags
                $method,     // compression method
                $dosTime,    // last mod file time  (16-bit)
                $dosDate,    // last mod file date  (16-bit)
                $crc,        // CRC-32              (32-bit)
                $compLen,    // compressed size     (32-bit)
                $dataLen,    // uncompressed size   (32-bit)
                $nameLen,    // filename length     (16-bit)
                0,           // extra field length  (16-bit)
                0,           // file comment length (16-bit)
                0,           // disk number start   (16-bit)
                0,           // internal attributes (16-bit)
                0,           // external attributes (32-bit)
                $offset      // local header offset (32-bit)
            ) . $nameBytes;

            $offset       += strlen($localHeader);
            $localHeaders .= $localHeader;
        }

        $cdSize    = strlen($centralDir);
        $fileCount = count($files);

        // End of central directory record (22 bayt)
        // sig(4) diskNo(2) diskCD(2) entriesDisk(2) entriesTotal(2) cdSize(4) cdOffset(4) commentLen(2)
        $eocd = pack('VvvvvVVv',
            0x06054b50,  // signature
            0,           // disk number
            0,           // disk with start of CD
            $fileCount,  // entries on this disk
            $fileCount,  // total entries
            $cdSize,     // size of central directory
            $offset,     // offset of start of CD
            0            // comment length
        );

        return $localHeaders . $centralDir . $eocd;
    }

    /**
     * DOS tarih/saat formatı — ZIP spec'e göre iki ayrı 16-bit değer döner.
     * @return array{int,int}  [time_word, date_word]
     */
    private static function dosDateTime(): array
    {
        $t    = getdate();
        $time = ($t['hours'] << 11) | ($t['minutes'] << 5) | ($t['seconds'] >> 1);
        $date = (($t['year'] - 1980) << 9) | ($t['mon'] << 5) | $t['mday'];
        return [$time, $date];
    }
}
