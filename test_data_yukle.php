<?php
/**
 * SınıfPro - Test Veri Kurulum Scripti
 * Tarayıcıdan çalıştır: http://localhost/ogretmen_app/test_data_yukle.php
 * Bitti mi? Bu dosyayı sil.
 */

require_once __DIR__ . '/includes/db.php';
$db = getDB();
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$uid    = 4;
$errors = [];
$log    = [];

function ins($db, $sql, $params=[]) {
    $st = $db->prepare($sql);
    $st->execute($params);
    return $db->lastInsertId();
}

try {
    $db->beginTransaction();

    // ── KONTROL ──────────────────────────────────────────
    $u = $db->query("SELECT id FROM users WHERE id=$uid")->fetch();
    if (!$u) throw new Exception("id=$uid olan öğretmen bulunamadı!");

    // ── SINIFLAR ─────────────────────────────────────────
    $log[] = "Sınıflar ekleniyor...";
    $classIds = []; // index => db id
    $classIds[0] = ins($db, "INSERT INTO `classes` (`user_id`,`name`,`grade`) VALUES (?,?,?)", [$uid, '5. Sınıf Matematik (5-A)', '5']);
    $classIds[1] = ins($db, "INSERT INTO `classes` (`user_id`,`name`,`grade`) VALUES (?,?,?)", [$uid, '5. Sınıf Matematik (5-B)', '5']);
    $classIds[2] = ins($db, "INSERT INTO `classes` (`user_id`,`name`,`grade`) VALUES (?,?,?)", [$uid, '6. Sınıf Matematik (6-A)', '6']);
    $classIds[3] = ins($db, "INSERT INTO `classes` (`user_id`,`name`,`grade`) VALUES (?,?,?)", [$uid, '6. Sınıf Matematik (6-B)', '6']);
    $classIds[4] = ins($db, "INSERT INTO `classes` (`user_id`,`name`,`grade`) VALUES (?,?,?)", [$uid, '7. Sınıf Matematik (7-A)', '7']);
    $classIds[5] = ins($db, "INSERT INTO `classes` (`user_id`,`name`,`grade`) VALUES (?,?,?)", [$uid, '8. Sınıf Matematik (8-A)', '8']);
    $classIds[6] = ins($db, "INSERT INTO `classes` (`user_id`,`name`,`grade`) VALUES (?,?,?)", [$uid, '5. Sınıf Fen Bilimleri (5-A)', '5']);
    $classIds[7] = ins($db, "INSERT INTO `classes` (`user_id`,`name`,`grade`) VALUES (?,?,?)", [$uid, '6. Sınıf Fen Bilimleri (6-A)', '6']);
    $classIds[8] = ins($db, "INSERT INTO `classes` (`user_id`,`name`,`grade`) VALUES (?,?,?)", [$uid, '7. Sınıf Fen Bilimleri (7-A)', '7']);
    $classIds[9] = ins($db, "INSERT INTO `classes` (`user_id`,`name`,`grade`) VALUES (?,?,?)", [$uid, '7. Sınıf Fen Bilimleri (7-B)', '7']);
    $classIds[10] = ins($db, "INSERT INTO `classes` (`user_id`,`name`,`grade`) VALUES (?,?,?)", [$uid, '8. Sınıf Fen Bilimleri (8-A)', '8']);
    $classIds[11] = ins($db, "INSERT INTO `classes` (`user_id`,`name`,`grade`) VALUES (?,?,?)", [$uid, '5. Sınıf Türkçe (5-A)', '5']);
    $classIds[12] = ins($db, "INSERT INTO `classes` (`user_id`,`name`,`grade`) VALUES (?,?,?)", [$uid, '5. Sınıf Türkçe (5-B)', '5']);
    $classIds[13] = ins($db, "INSERT INTO `classes` (`user_id`,`name`,`grade`) VALUES (?,?,?)", [$uid, '6. Sınıf Türkçe (6-A)', '6']);
    $classIds[14] = ins($db, "INSERT INTO `classes` (`user_id`,`name`,`grade`) VALUES (?,?,?)", [$uid, '6. Sınıf Türkçe (6-B)', '6']);
    $classIds[15] = ins($db, "INSERT INTO `classes` (`user_id`,`name`,`grade`) VALUES (?,?,?)", [$uid, '5. Sınıf Sosyal Bilgiler (5-A)', '5']);
    $classIds[16] = ins($db, "INSERT INTO `classes` (`user_id`,`name`,`grade`) VALUES (?,?,?)", [$uid, '6. Sınıf Sosyal Bilgiler (6-A)', '6']);
    $classIds[17] = ins($db, "INSERT INTO `classes` (`user_id`,`name`,`grade`) VALUES (?,?,?)", [$uid, '7. Sınıf Sosyal Bilgiler (7-A)', '7']);
    $classIds[18] = ins($db, "INSERT INTO `classes` (`user_id`,`name`,`grade`) VALUES (?,?,?)", [$uid, '8. Sınıf Sosyal Bilgiler (8-A)', '8']);
    $classIds[19] = ins($db, "INSERT INTO `classes` (`user_id`,`name`,`grade`) VALUES (?,?,?)", [$uid, '5. Sınıf İngilizce (5-A)', '5']);
    $classIds[20] = ins($db, "INSERT INTO `classes` (`user_id`,`name`,`grade`) VALUES (?,?,?)", [$uid, '6. Sınıf İngilizce (6-A)', '6']);
    $classIds[21] = ins($db, "INSERT INTO `classes` (`user_id`,`name`,`grade`) VALUES (?,?,?)", [$uid, '7. Sınıf İngilizce (7-A)', '7']);
    $classIds[22] = ins($db, "INSERT INTO `classes` (`user_id`,`name`,`grade`) VALUES (?,?,?)", [$uid, '8. Sınıf İngilizce (8-A)', '8']);

    $log[] = count($classIds) . " sınıf eklendi.";

    // ── ÜNİTELER ─────────────────────────────────────────
    $log[] = "Üniteler ekleniyor...";
    $unitIds = []; // global_index => db id
    $unitDers = []; // global_index => ders adı
    $unitByClass = []; // class_index => [global_unit_index, ...]
    $unitIds[0] = ins($db, "INSERT INTO `units` (`user_id`,`class_id`,`name`,`order_num`) VALUES (?,?,?,?)", [$uid, $classIds[0], 'Kesirler', 1]);
    $unitDers[0] = 'Matematik';
    $unitByClass[0][] = 0;
    $unitIds[1] = ins($db, "INSERT INTO `units` (`user_id`,`class_id`,`name`,`order_num`) VALUES (?,?,?,?)", [$uid, $classIds[0], 'Doğal Sayılar', 2]);
    $unitDers[1] = 'Matematik';
    $unitByClass[0][] = 1;
    $unitIds[2] = ins($db, "INSERT INTO `units` (`user_id`,`class_id`,`name`,`order_num`) VALUES (?,?,?,?)", [$uid, $classIds[0], 'Ölçme', 3]);
    $unitDers[2] = 'Matematik';
    $unitByClass[0][] = 2;
    $unitIds[3] = ins($db, "INSERT INTO `units` (`user_id`,`class_id`,`name`,`order_num`) VALUES (?,?,?,?)", [$uid, $classIds[0], 'Geometri Temelleri', 4]);
    $unitDers[3] = 'Matematik';
    $unitByClass[0][] = 3;
    $unitIds[4] = ins($db, "INSERT INTO `units` (`user_id`,`class_id`,`name`,`order_num`) VALUES (?,?,?,?)", [$uid, $classIds[0], 'Orantı', 5]);
    $unitDers[4] = 'Matematik';
    $unitByClass[0][] = 4;
    $unitIds[5] = ins($db, "INSERT INTO `units` (`user_id`,`class_id`,`name`,`order_num`) VALUES (?,?,?,?)", [$uid, $classIds[0], 'Cebirsel İfadeler', 6]);
    $unitDers[5] = 'Matematik';
    $unitByClass[0][] = 5;
    $unitIds[6] = ins($db, "INSERT INTO `units` (`user_id`,`class_id`,`name`,`order_num`) VALUES (?,?,?,?)", [$uid, $classIds[1], 'Kesirler', 1]);
    $unitDers[6] = 'Matematik';
    $unitByClass[1][] = 6;
    $unitIds[7] = ins($db, "INSERT INTO `units` (`user_id`,`class_id`,`name`,`order_num`) VALUES (?,?,?,?)", [$uid, $classIds[1], 'Çarpanlar', 2]);
    $unitDers[7] = 'Matematik';
    $unitByClass[1][] = 7;
    $unitIds[8] = ins($db, "INSERT INTO `units` (`user_id`,`class_id`,`name`,`order_num`) VALUES (?,?,?,?)", [$uid, $classIds[1], 'Orantı', 3]);
    $unitDers[8] = 'Matematik';
    $unitByClass[1][] = 8;
    $unitIds[9] = ins($db, "INSERT INTO `units` (`user_id`,`class_id`,`name`,`order_num`) VALUES (?,?,?,?)", [$uid, $classIds[1], 'Cebirsel İfadeler', 4]);
    $unitDers[9] = 'Matematik';
    $unitByClass[1][] = 9;
    $unitIds[10] = ins($db, "INSERT INTO `units` (`user_id`,`class_id`,`name`,`order_num`) VALUES (?,?,?,?)", [$uid, $classIds[1], 'Tam Sayılar', 5]);
    $unitDers[10] = 'Matematik';
    $unitByClass[1][] = 10;
    $unitIds[11] = ins($db, "INSERT INTO `units` (`user_id`,`class_id`,`name`,`order_num`) VALUES (?,?,?,?)", [$uid, $classIds[1], 'Doğal Sayılar', 6]);
    $unitDers[11] = 'Matematik';
    $unitByClass[1][] = 11;
    $unitIds[12] = ins($db, "INSERT INTO `units` (`user_id`,`class_id`,`name`,`order_num`) VALUES (?,?,?,?)", [$uid, $classIds[2], 'Kesirler', 1]);
    $unitDers[12] = 'Matematik';
    $unitByClass[2][] = 12;
    $unitIds[13] = ins($db, "INSERT INTO `units` (`user_id`,`class_id`,`name`,`order_num`) VALUES (?,?,?,?)", [$uid, $classIds[2], 'Geometri Temelleri', 2]);
    $unitDers[13] = 'Matematik';
    $unitByClass[2][] = 13;
    $unitIds[14] = ins($db, "INSERT INTO `units` (`user_id`,`class_id`,`name`,`order_num`) VALUES (?,?,?,?)", [$uid, $classIds[2], 'Çarpanlar', 3]);
    $unitDers[14] = 'Matematik';
    $unitByClass[2][] = 14;
    $unitIds[15] = ins($db, "INSERT INTO `units` (`user_id`,`class_id`,`name`,`order_num`) VALUES (?,?,?,?)", [$uid, $classIds[2], 'Orantı', 4]);
    $unitDers[15] = 'Matematik';
    $unitByClass[2][] = 15;
    $unitIds[16] = ins($db, "INSERT INTO `units` (`user_id`,`class_id`,`name`,`order_num`) VALUES (?,?,?,?)", [$uid, $classIds[3], 'Doğal Sayılar', 1]);
    $unitDers[16] = 'Matematik';
    $unitByClass[3][] = 16;
    $unitIds[17] = ins($db, "INSERT INTO `units` (`user_id`,`class_id`,`name`,`order_num`) VALUES (?,?,?,?)", [$uid, $classIds[3], 'Orantı', 2]);
    $unitDers[17] = 'Matematik';
    $unitByClass[3][] = 17;
    $unitIds[18] = ins($db, "INSERT INTO `units` (`user_id`,`class_id`,`name`,`order_num`) VALUES (?,?,?,?)", [$uid, $classIds[3], 'Geometri Temelleri', 3]);
    $unitDers[18] = 'Matematik';
    $unitByClass[3][] = 18;
    $unitIds[19] = ins($db, "INSERT INTO `units` (`user_id`,`class_id`,`name`,`order_num`) VALUES (?,?,?,?)", [$uid, $classIds[3], 'Çarpanlar', 4]);
    $unitDers[19] = 'Matematik';
    $unitByClass[3][] = 19;
    $unitIds[20] = ins($db, "INSERT INTO `units` (`user_id`,`class_id`,`name`,`order_num`) VALUES (?,?,?,?)", [$uid, $classIds[3], 'Tam Sayılar', 5]);
    $unitDers[20] = 'Matematik';
    $unitByClass[3][] = 20;
    $unitIds[21] = ins($db, "INSERT INTO `units` (`user_id`,`class_id`,`name`,`order_num`) VALUES (?,?,?,?)", [$uid, $classIds[3], 'Kesirler', 6]);
    $unitDers[21] = 'Matematik';
    $unitByClass[3][] = 21;
    $unitIds[22] = ins($db, "INSERT INTO `units` (`user_id`,`class_id`,`name`,`order_num`) VALUES (?,?,?,?)", [$uid, $classIds[4], 'Denklemler', 1]);
    $unitDers[22] = 'Matematik';
    $unitByClass[4][] = 22;
    $unitIds[23] = ins($db, "INSERT INTO `units` (`user_id`,`class_id`,`name`,`order_num`) VALUES (?,?,?,?)", [$uid, $classIds[4], 'Ölçme', 2]);
    $unitDers[23] = 'Matematik';
    $unitByClass[4][] = 23;
    $unitIds[24] = ins($db, "INSERT INTO `units` (`user_id`,`class_id`,`name`,`order_num`) VALUES (?,?,?,?)", [$uid, $classIds[4], 'Doğal Sayılar', 3]);
    $unitDers[24] = 'Matematik';
    $unitByClass[4][] = 24;
    $unitIds[25] = ins($db, "INSERT INTO `units` (`user_id`,`class_id`,`name`,`order_num`) VALUES (?,?,?,?)", [$uid, $classIds[4], 'Ondalık Sayılar', 4]);
    $unitDers[25] = 'Matematik';
    $unitByClass[4][] = 25;
    $unitIds[26] = ins($db, "INSERT INTO `units` (`user_id`,`class_id`,`name`,`order_num`) VALUES (?,?,?,?)", [$uid, $classIds[4], 'Tam Sayılar', 5]);
    $unitDers[26] = 'Matematik';
    $unitByClass[4][] = 26;
    $unitIds[27] = ins($db, "INSERT INTO `units` (`user_id`,`class_id`,`name`,`order_num`) VALUES (?,?,?,?)", [$uid, $classIds[5], 'Ölçme', 1]);
    $unitDers[27] = 'Matematik';
    $unitByClass[5][] = 27;
    $unitIds[28] = ins($db, "INSERT INTO `units` (`user_id`,`class_id`,`name`,`order_num`) VALUES (?,?,?,?)", [$uid, $classIds[5], 'Ondalık Sayılar', 2]);
    $unitDers[28] = 'Matematik';
    $unitByClass[5][] = 28;
    $unitIds[29] = ins($db, "INSERT INTO `units` (`user_id`,`class_id`,`name`,`order_num`) VALUES (?,?,?,?)", [$uid, $classIds[5], 'Geometri Temelleri', 3]);
    $unitDers[29] = 'Matematik';
    $unitByClass[5][] = 29;
    $unitIds[30] = ins($db, "INSERT INTO `units` (`user_id`,`class_id`,`name`,`order_num`) VALUES (?,?,?,?)", [$uid, $classIds[5], 'Veri Analizi', 4]);
    $unitDers[30] = 'Matematik';
    $unitByClass[5][] = 30;
    $unitIds[31] = ins($db, "INSERT INTO `units` (`user_id`,`class_id`,`name`,`order_num`) VALUES (?,?,?,?)", [$uid, $classIds[5], 'Kesirler', 5]);
    $unitDers[31] = 'Matematik';
    $unitByClass[5][] = 31;
    $unitIds[32] = ins($db, "INSERT INTO `units` (`user_id`,`class_id`,`name`,`order_num`) VALUES (?,?,?,?)", [$uid, $classIds[6], 'Ekosistem', 1]);
    $unitDers[32] = 'Fen Bilimleri';
    $unitByClass[6][] = 32;
    $unitIds[33] = ins($db, "INSERT INTO `units` (`user_id`,`class_id`,`name`,`order_num`) VALUES (?,?,?,?)", [$uid, $classIds[6], 'Kuvvet ve Hareket', 2]);
    $unitDers[33] = 'Fen Bilimleri';
    $unitByClass[6][] = 33;
    $unitIds[34] = ins($db, "INSERT INTO `units` (`user_id`,`class_id`,`name`,`order_num`) VALUES (?,?,?,?)", [$uid, $classIds[6], 'Elektrik', 3]);
    $unitDers[34] = 'Fen Bilimleri';
    $unitByClass[6][] = 34;
    $unitIds[35] = ins($db, "INSERT INTO `units` (`user_id`,`class_id`,`name`,`order_num`) VALUES (?,?,?,?)", [$uid, $classIds[6], 'Basınç', 4]);
    $unitDers[35] = 'Fen Bilimleri';
    $unitByClass[6][] = 35;
    $unitIds[36] = ins($db, "INSERT INTO `units` (`user_id`,`class_id`,`name`,`order_num`) VALUES (?,?,?,?)", [$uid, $classIds[7], 'Canlılar', 1]);
    $unitDers[36] = 'Fen Bilimleri';
    $unitByClass[7][] = 36;
    $unitIds[37] = ins($db, "INSERT INTO `units` (`user_id`,`class_id`,`name`,`order_num`) VALUES (?,?,?,?)", [$uid, $classIds[7], 'Hücre', 2]);
    $unitDers[37] = 'Fen Bilimleri';
    $unitByClass[7][] = 37;
    $unitIds[38] = ins($db, "INSERT INTO `units` (`user_id`,`class_id`,`name`,`order_num`) VALUES (?,?,?,?)", [$uid, $classIds[7], 'Isı ve Sıcaklık', 3]);
    $unitDers[38] = 'Fen Bilimleri';
    $unitByClass[7][] = 38;
    $unitIds[39] = ins($db, "INSERT INTO `units` (`user_id`,`class_id`,`name`,`order_num`) VALUES (?,?,?,?)", [$uid, $classIds[7], 'Kuvvet ve Hareket', 4]);
    $unitDers[39] = 'Fen Bilimleri';
    $unitByClass[7][] = 39;
    $unitIds[40] = ins($db, "INSERT INTO `units` (`user_id`,`class_id`,`name`,`order_num`) VALUES (?,?,?,?)", [$uid, $classIds[7], 'Işık ve Ses', 5]);
    $unitDers[40] = 'Fen Bilimleri';
    $unitByClass[7][] = 40;
    $unitIds[41] = ins($db, "INSERT INTO `units` (`user_id`,`class_id`,`name`,`order_num`) VALUES (?,?,?,?)", [$uid, $classIds[7], 'Sindirim Sistemi', 6]);
    $unitDers[41] = 'Fen Bilimleri';
    $unitByClass[7][] = 41;
    $unitIds[42] = ins($db, "INSERT INTO `units` (`user_id`,`class_id`,`name`,`order_num`) VALUES (?,?,?,?)", [$uid, $classIds[8], 'Canlılar', 1]);
    $unitDers[42] = 'Fen Bilimleri';
    $unitByClass[8][] = 42;
    $unitIds[43] = ins($db, "INSERT INTO `units` (`user_id`,`class_id`,`name`,`order_num`) VALUES (?,?,?,?)", [$uid, $classIds[8], 'Sindirim Sistemi', 2]);
    $unitDers[43] = 'Fen Bilimleri';
    $unitByClass[8][] = 43;
    $unitIds[44] = ins($db, "INSERT INTO `units` (`user_id`,`class_id`,`name`,`order_num`) VALUES (?,?,?,?)", [$uid, $classIds[8], 'Elektrik', 3]);
    $unitDers[44] = 'Fen Bilimleri';
    $unitByClass[8][] = 44;
    $unitIds[45] = ins($db, "INSERT INTO `units` (`user_id`,`class_id`,`name`,`order_num`) VALUES (?,?,?,?)", [$uid, $classIds[8], 'Işık ve Ses', 4]);
    $unitDers[45] = 'Fen Bilimleri';
    $unitByClass[8][] = 45;
    $unitIds[46] = ins($db, "INSERT INTO `units` (`user_id`,`class_id`,`name`,`order_num`) VALUES (?,?,?,?)", [$uid, $classIds[8], 'Basınç', 5]);
    $unitDers[46] = 'Fen Bilimleri';
    $unitByClass[8][] = 46;
    $unitIds[47] = ins($db, "INSERT INTO `units` (`user_id`,`class_id`,`name`,`order_num`) VALUES (?,?,?,?)", [$uid, $classIds[8], 'Hücre', 6]);
    $unitDers[47] = 'Fen Bilimleri';
    $unitByClass[8][] = 47;
    $unitIds[48] = ins($db, "INSERT INTO `units` (`user_id`,`class_id`,`name`,`order_num`) VALUES (?,?,?,?)", [$uid, $classIds[9], 'Üreme', 1]);
    $unitDers[48] = 'Fen Bilimleri';
    $unitByClass[9][] = 48;
    $unitIds[49] = ins($db, "INSERT INTO `units` (`user_id`,`class_id`,`name`,`order_num`) VALUES (?,?,?,?)", [$uid, $classIds[9], 'Işık ve Ses', 2]);
    $unitDers[49] = 'Fen Bilimleri';
    $unitByClass[9][] = 49;
    $unitIds[50] = ins($db, "INSERT INTO `units` (`user_id`,`class_id`,`name`,`order_num`) VALUES (?,?,?,?)", [$uid, $classIds[9], 'Canlılar', 3]);
    $unitDers[50] = 'Fen Bilimleri';
    $unitByClass[9][] = 50;
    $unitIds[51] = ins($db, "INSERT INTO `units` (`user_id`,`class_id`,`name`,`order_num`) VALUES (?,?,?,?)", [$uid, $classIds[9], 'Kuvvet ve Hareket', 4]);
    $unitDers[51] = 'Fen Bilimleri';
    $unitByClass[9][] = 51;
    $unitIds[52] = ins($db, "INSERT INTO `units` (`user_id`,`class_id`,`name`,`order_num`) VALUES (?,?,?,?)", [$uid, $classIds[10], 'Kuvvet ve Hareket', 1]);
    $unitDers[52] = 'Fen Bilimleri';
    $unitByClass[10][] = 52;
    $unitIds[53] = ins($db, "INSERT INTO `units` (`user_id`,`class_id`,`name`,`order_num`) VALUES (?,?,?,?)", [$uid, $classIds[10], 'Ekosistem', 2]);
    $unitDers[53] = 'Fen Bilimleri';
    $unitByClass[10][] = 53;
    $unitIds[54] = ins($db, "INSERT INTO `units` (`user_id`,`class_id`,`name`,`order_num`) VALUES (?,?,?,?)", [$uid, $classIds[10], 'Canlılar', 3]);
    $unitDers[54] = 'Fen Bilimleri';
    $unitByClass[10][] = 54;
    $unitIds[55] = ins($db, "INSERT INTO `units` (`user_id`,`class_id`,`name`,`order_num`) VALUES (?,?,?,?)", [$uid, $classIds[10], 'Isı ve Sıcaklık', 4]);
    $unitDers[55] = 'Fen Bilimleri';
    $unitByClass[10][] = 55;
    $unitIds[56] = ins($db, "INSERT INTO `units` (`user_id`,`class_id`,`name`,`order_num`) VALUES (?,?,?,?)", [$uid, $classIds[11], 'Söz Sanatları', 1]);
    $unitDers[56] = 'Türkçe';
    $unitByClass[11][] = 56;
    $unitIds[57] = ins($db, "INSERT INTO `units` (`user_id`,`class_id`,`name`,`order_num`) VALUES (?,?,?,?)", [$uid, $classIds[11], 'Cümle Bilgisi', 2]);
    $unitDers[57] = 'Türkçe';
    $unitByClass[11][] = 57;
    $unitIds[58] = ins($db, "INSERT INTO `units` (`user_id`,`class_id`,`name`,`order_num`) VALUES (?,?,?,?)", [$uid, $classIds[11], 'Yazım Kuralları', 3]);
    $unitDers[58] = 'Türkçe';
    $unitByClass[11][] = 58;
    $unitIds[59] = ins($db, "INSERT INTO `units` (`user_id`,`class_id`,`name`,`order_num`) VALUES (?,?,?,?)", [$uid, $classIds[11], 'Paragraf', 4]);
    $unitDers[59] = 'Türkçe';
    $unitByClass[11][] = 59;
    $unitIds[60] = ins($db, "INSERT INTO `units` (`user_id`,`class_id`,`name`,`order_num`) VALUES (?,?,?,?)", [$uid, $classIds[11], 'Fiil Çekimi', 5]);
    $unitDers[60] = 'Türkçe';
    $unitByClass[11][] = 60;
    $unitIds[61] = ins($db, "INSERT INTO `units` (`user_id`,`class_id`,`name`,`order_num`) VALUES (?,?,?,?)", [$uid, $classIds[11], 'Metin Türleri', 6]);
    $unitDers[61] = 'Türkçe';
    $unitByClass[11][] = 61;
    $unitIds[62] = ins($db, "INSERT INTO `units` (`user_id`,`class_id`,`name`,`order_num`) VALUES (?,?,?,?)", [$uid, $classIds[12], 'Cümle Bilgisi', 1]);
    $unitDers[62] = 'Türkçe';
    $unitByClass[12][] = 62;
    $unitIds[63] = ins($db, "INSERT INTO `units` (`user_id`,`class_id`,`name`,`order_num`) VALUES (?,?,?,?)", [$uid, $classIds[12], 'Metin Türleri', 2]);
    $unitDers[63] = 'Türkçe';
    $unitByClass[12][] = 63;
    $unitIds[64] = ins($db, "INSERT INTO `units` (`user_id`,`class_id`,`name`,`order_num`) VALUES (?,?,?,?)", [$uid, $classIds[12], 'Söz Sanatları', 3]);
    $unitDers[64] = 'Türkçe';
    $unitByClass[12][] = 64;
    $unitIds[65] = ins($db, "INSERT INTO `units` (`user_id`,`class_id`,`name`,`order_num`) VALUES (?,?,?,?)", [$uid, $classIds[12], 'Paragraf', 4]);
    $unitDers[65] = 'Türkçe';
    $unitByClass[12][] = 65;
    $unitIds[66] = ins($db, "INSERT INTO `units` (`user_id`,`class_id`,`name`,`order_num`) VALUES (?,?,?,?)", [$uid, $classIds[12], 'Fiil Çekimi', 5]);
    $unitDers[66] = 'Türkçe';
    $unitByClass[12][] = 66;
    $unitIds[67] = ins($db, "INSERT INTO `units` (`user_id`,`class_id`,`name`,`order_num`) VALUES (?,?,?,?)", [$uid, $classIds[13], 'Paragraf', 1]);
    $unitDers[67] = 'Türkçe';
    $unitByClass[13][] = 67;
    $unitIds[68] = ins($db, "INSERT INTO `units` (`user_id`,`class_id`,`name`,`order_num`) VALUES (?,?,?,?)", [$uid, $classIds[13], 'Noktalama', 2]);
    $unitDers[68] = 'Türkçe';
    $unitByClass[13][] = 68;
    $unitIds[69] = ins($db, "INSERT INTO `units` (`user_id`,`class_id`,`name`,`order_num`) VALUES (?,?,?,?)", [$uid, $classIds[13], 'Yazım Kuralları', 3]);
    $unitDers[69] = 'Türkçe';
    $unitByClass[13][] = 69;
    $unitIds[70] = ins($db, "INSERT INTO `units` (`user_id`,`class_id`,`name`,`order_num`) VALUES (?,?,?,?)", [$uid, $classIds[13], 'Metin Türleri', 4]);
    $unitDers[70] = 'Türkçe';
    $unitByClass[13][] = 70;
    $unitIds[71] = ins($db, "INSERT INTO `units` (`user_id`,`class_id`,`name`,`order_num`) VALUES (?,?,?,?)", [$uid, $classIds[14], 'Söz Sanatları', 1]);
    $unitDers[71] = 'Türkçe';
    $unitByClass[14][] = 71;
    $unitIds[72] = ins($db, "INSERT INTO `units` (`user_id`,`class_id`,`name`,`order_num`) VALUES (?,?,?,?)", [$uid, $classIds[14], 'Fiil Çekimi', 2]);
    $unitDers[72] = 'Türkçe';
    $unitByClass[14][] = 72;
    $unitIds[73] = ins($db, "INSERT INTO `units` (`user_id`,`class_id`,`name`,`order_num`) VALUES (?,?,?,?)", [$uid, $classIds[14], 'Sözcükte Anlam', 3]);
    $unitDers[73] = 'Türkçe';
    $unitByClass[14][] = 73;
    $unitIds[74] = ins($db, "INSERT INTO `units` (`user_id`,`class_id`,`name`,`order_num`) VALUES (?,?,?,?)", [$uid, $classIds[14], 'Cümle Bilgisi', 4]);
    $unitDers[74] = 'Türkçe';
    $unitByClass[14][] = 74;
    $unitIds[75] = ins($db, "INSERT INTO `units` (`user_id`,`class_id`,`name`,`order_num`) VALUES (?,?,?,?)", [$uid, $classIds[15], 'Dünya', 1]);
    $unitDers[75] = 'Sosyal Bilgiler';
    $unitByClass[15][] = 75;
    $unitIds[76] = ins($db, "INSERT INTO `units` (`user_id`,`class_id`,`name`,`order_num`) VALUES (?,?,?,?)", [$uid, $classIds[15], 'Cumhuriyet', 2]);
    $unitDers[76] = 'Sosyal Bilgiler';
    $unitByClass[15][] = 76;
    $unitIds[77] = ins($db, "INSERT INTO `units` (`user_id`,`class_id`,`name`,`order_num`) VALUES (?,?,?,?)", [$uid, $classIds[15], 'Osmanlı Dönemi', 3]);
    $unitDers[77] = 'Sosyal Bilgiler';
    $unitByClass[15][] = 77;
    $unitIds[78] = ins($db, "INSERT INTO `units` (`user_id`,`class_id`,`name`,`order_num`) VALUES (?,?,?,?)", [$uid, $classIds[15], 'Coğrafya', 4]);
    $unitDers[78] = 'Sosyal Bilgiler';
    $unitByClass[15][] = 78;
    $unitIds[79] = ins($db, "INSERT INTO `units` (`user_id`,`class_id`,`name`,`order_num`) VALUES (?,?,?,?)", [$uid, $classIds[16], 'Dünya', 1]);
    $unitDers[79] = 'Sosyal Bilgiler';
    $unitByClass[16][] = 79;
    $unitIds[80] = ins($db, "INSERT INTO `units` (`user_id`,`class_id`,`name`,`order_num`) VALUES (?,?,?,?)", [$uid, $classIds[16], 'Tarih Öncesi', 2]);
    $unitDers[80] = 'Sosyal Bilgiler';
    $unitByClass[16][] = 80;
    $unitIds[81] = ins($db, "INSERT INTO `units` (`user_id`,`class_id`,`name`,`order_num`) VALUES (?,?,?,?)", [$uid, $classIds[16], 'Kültür ve Miras', 3]);
    $unitDers[81] = 'Sosyal Bilgiler';
    $unitByClass[16][] = 81;
    $unitIds[82] = ins($db, "INSERT INTO `units` (`user_id`,`class_id`,`name`,`order_num`) VALUES (?,?,?,?)", [$uid, $classIds[16], 'Cumhuriyet', 4]);
    $unitDers[82] = 'Sosyal Bilgiler';
    $unitByClass[16][] = 82;
    $unitIds[83] = ins($db, "INSERT INTO `units` (`user_id`,`class_id`,`name`,`order_num`) VALUES (?,?,?,?)", [$uid, $classIds[17], 'Kültür ve Miras', 1]);
    $unitDers[83] = 'Sosyal Bilgiler';
    $unitByClass[17][] = 83;
    $unitIds[84] = ins($db, "INSERT INTO `units` (`user_id`,`class_id`,`name`,`order_num`) VALUES (?,?,?,?)", [$uid, $classIds[17], 'Tarih Öncesi', 2]);
    $unitDers[84] = 'Sosyal Bilgiler';
    $unitByClass[17][] = 84;
    $unitIds[85] = ins($db, "INSERT INTO `units` (`user_id`,`class_id`,`name`,`order_num`) VALUES (?,?,?,?)", [$uid, $classIds[17], 'Osmanlı Dönemi', 3]);
    $unitDers[85] = 'Sosyal Bilgiler';
    $unitByClass[17][] = 85;
    $unitIds[86] = ins($db, "INSERT INTO `units` (`user_id`,`class_id`,`name`,`order_num`) VALUES (?,?,?,?)", [$uid, $classIds[17], 'İklim', 4]);
    $unitDers[86] = 'Sosyal Bilgiler';
    $unitByClass[17][] = 86;
    $unitIds[87] = ins($db, "INSERT INTO `units` (`user_id`,`class_id`,`name`,`order_num`) VALUES (?,?,?,?)", [$uid, $classIds[17], 'Ülkemiz', 5]);
    $unitDers[87] = 'Sosyal Bilgiler';
    $unitByClass[17][] = 87;
    $unitIds[88] = ins($db, "INSERT INTO `units` (`user_id`,`class_id`,`name`,`order_num`) VALUES (?,?,?,?)", [$uid, $classIds[18], 'Ülkemiz', 1]);
    $unitDers[88] = 'Sosyal Bilgiler';
    $unitByClass[18][] = 88;
    $unitIds[89] = ins($db, "INSERT INTO `units` (`user_id`,`class_id`,`name`,`order_num`) VALUES (?,?,?,?)", [$uid, $classIds[18], 'Dünya', 2]);
    $unitDers[89] = 'Sosyal Bilgiler';
    $unitByClass[18][] = 89;
    $unitIds[90] = ins($db, "INSERT INTO `units` (`user_id`,`class_id`,`name`,`order_num`) VALUES (?,?,?,?)", [$uid, $classIds[18], 'Kültür ve Miras', 3]);
    $unitDers[90] = 'Sosyal Bilgiler';
    $unitByClass[18][] = 90;
    $unitIds[91] = ins($db, "INSERT INTO `units` (`user_id`,`class_id`,`name`,`order_num`) VALUES (?,?,?,?)", [$uid, $classIds[18], 'Cumhuriyet', 4]);
    $unitDers[91] = 'Sosyal Bilgiler';
    $unitByClass[18][] = 91;
    $unitIds[92] = ins($db, "INSERT INTO `units` (`user_id`,`class_id`,`name`,`order_num`) VALUES (?,?,?,?)", [$uid, $classIds[18], 'İklim', 5]);
    $unitDers[92] = 'Sosyal Bilgiler';
    $unitByClass[18][] = 92;
    $unitIds[93] = ins($db, "INSERT INTO `units` (`user_id`,`class_id`,`name`,`order_num`) VALUES (?,?,?,?)", [$uid, $classIds[18], 'Tarih Öncesi', 6]);
    $unitDers[93] = 'Sosyal Bilgiler';
    $unitByClass[18][] = 93;
    $unitIds[94] = ins($db, "INSERT INTO `units` (`user_id`,`class_id`,`name`,`order_num`) VALUES (?,?,?,?)", [$uid, $classIds[19], 'Past Tense', 1]);
    $unitDers[94] = 'İngilizce';
    $unitByClass[19][] = 94;
    $unitIds[95] = ins($db, "INSERT INTO `units` (`user_id`,`class_id`,`name`,`order_num`) VALUES (?,?,?,?)", [$uid, $classIds[19], 'Reading', 2]);
    $unitDers[95] = 'İngilizce';
    $unitByClass[19][] = 95;
    $unitIds[96] = ins($db, "INSERT INTO `units` (`user_id`,`class_id`,`name`,`order_num`) VALUES (?,?,?,?)", [$uid, $classIds[19], 'Vocabulary', 3]);
    $unitDers[96] = 'İngilizce';
    $unitByClass[19][] = 96;
    $unitIds[97] = ins($db, "INSERT INTO `units` (`user_id`,`class_id`,`name`,`order_num`) VALUES (?,?,?,?)", [$uid, $classIds[19], 'Greetings', 4]);
    $unitDers[97] = 'İngilizce';
    $unitByClass[19][] = 97;
    $unitIds[98] = ins($db, "INSERT INTO `units` (`user_id`,`class_id`,`name`,`order_num`) VALUES (?,?,?,?)", [$uid, $classIds[20], 'Present Tense', 1]);
    $unitDers[98] = 'İngilizce';
    $unitByClass[20][] = 98;
    $unitIds[99] = ins($db, "INSERT INTO `units` (`user_id`,`class_id`,`name`,`order_num`) VALUES (?,?,?,?)", [$uid, $classIds[20], 'Future Tense', 2]);
    $unitDers[99] = 'İngilizce';
    $unitByClass[20][] = 99;
    $unitIds[100] = ins($db, "INSERT INTO `units` (`user_id`,`class_id`,`name`,`order_num`) VALUES (?,?,?,?)", [$uid, $classIds[20], 'Listening', 3]);
    $unitDers[100] = 'İngilizce';
    $unitByClass[20][] = 100;
    $unitIds[101] = ins($db, "INSERT INTO `units` (`user_id`,`class_id`,`name`,`order_num`) VALUES (?,?,?,?)", [$uid, $classIds[20], 'Writing', 4]);
    $unitDers[101] = 'İngilizce';
    $unitByClass[20][] = 101;
    $unitIds[102] = ins($db, "INSERT INTO `units` (`user_id`,`class_id`,`name`,`order_num`) VALUES (?,?,?,?)", [$uid, $classIds[21], 'Writing', 1]);
    $unitDers[102] = 'İngilizce';
    $unitByClass[21][] = 102;
    $unitIds[103] = ins($db, "INSERT INTO `units` (`user_id`,`class_id`,`name`,`order_num`) VALUES (?,?,?,?)", [$uid, $classIds[21], 'Reading', 2]);
    $unitDers[103] = 'İngilizce';
    $unitByClass[21][] = 103;
    $unitIds[104] = ins($db, "INSERT INTO `units` (`user_id`,`class_id`,`name`,`order_num`) VALUES (?,?,?,?)", [$uid, $classIds[21], 'Greetings', 3]);
    $unitDers[104] = 'İngilizce';
    $unitByClass[21][] = 104;
    $unitIds[105] = ins($db, "INSERT INTO `units` (`user_id`,`class_id`,`name`,`order_num`) VALUES (?,?,?,?)", [$uid, $classIds[21], 'Vocabulary', 4]);
    $unitDers[105] = 'İngilizce';
    $unitByClass[21][] = 105;
    $unitIds[106] = ins($db, "INSERT INTO `units` (`user_id`,`class_id`,`name`,`order_num`) VALUES (?,?,?,?)", [$uid, $classIds[21], 'Future Tense', 5]);
    $unitDers[106] = 'İngilizce';
    $unitByClass[21][] = 106;
    $unitIds[107] = ins($db, "INSERT INTO `units` (`user_id`,`class_id`,`name`,`order_num`) VALUES (?,?,?,?)", [$uid, $classIds[21], 'Past Tense', 6]);
    $unitDers[107] = 'İngilizce';
    $unitByClass[21][] = 107;
    $unitIds[108] = ins($db, "INSERT INTO `units` (`user_id`,`class_id`,`name`,`order_num`) VALUES (?,?,?,?)", [$uid, $classIds[22], 'Reading', 1]);
    $unitDers[108] = 'İngilizce';
    $unitByClass[22][] = 108;
    $unitIds[109] = ins($db, "INSERT INTO `units` (`user_id`,`class_id`,`name`,`order_num`) VALUES (?,?,?,?)", [$uid, $classIds[22], 'Future Tense', 2]);
    $unitDers[109] = 'İngilizce';
    $unitByClass[22][] = 109;
    $unitIds[110] = ins($db, "INSERT INTO `units` (`user_id`,`class_id`,`name`,`order_num`) VALUES (?,?,?,?)", [$uid, $classIds[22], 'Greetings', 3]);
    $unitDers[110] = 'İngilizce';
    $unitByClass[22][] = 110;
    $unitIds[111] = ins($db, "INSERT INTO `units` (`user_id`,`class_id`,`name`,`order_num`) VALUES (?,?,?,?)", [$uid, $classIds[22], 'Listening', 4]);
    $unitDers[111] = 'İngilizce';
    $unitByClass[22][] = 111;
    $unitIds[112] = ins($db, "INSERT INTO `units` (`user_id`,`class_id`,`name`,`order_num`) VALUES (?,?,?,?)", [$uid, $classIds[22], 'Past Tense', 5]);
    $unitDers[112] = 'İngilizce';
    $unitByClass[22][] = 112;

    $log[] = count($unitIds) . " ünite eklendi.";

    // ── SORULAR (1000 adet) ───────────────────────────────
    $log[] = "Sorular ekleniyor...";
    $questionIds = []; // index => [db_id, unit_global_idx, type]
    $qIdx = 0;
    // Chunk 1/20
    $qBatch = [
        ['test', 0, '100\'ün %30\'u kaçtır?', '100\'ün %30\'u kaçtır?', '10', '20', '30', '40', '50', 'C', NULL, 10],
        ['test', 2, 'En küçük asal sayı hangisidir?', 'En küçük asal sayı hangisidir?', '0', '1', '2', '3', '4', 'C', NULL, 20],
        ['klasik', 2, 'Bir çemberin çapı 14 cm ise çevresi kaçtır? (π=3,14)', 'Bir çemberin çapı 14 cm ise çevresi kaçtır? (π=3,14)', NULL, NULL, NULL, NULL, NULL, NULL, 'Çevre = π × d = 3,14 × 14 = 43,96 cm', 15],
        ['test', 3, 'En küçük asal sayı hangisidir?', 'En küçük asal sayı hangisidir?', '0', '1', '2', '3', '4', 'C', NULL, 5],
        ['test', 4, '15 + 27 işleminin sonucu nedir?', '15 + 27 işleminin sonucu nedir?', '40', '41', '42', '43', '44', 'C', NULL, 10],
        ['test', 5, 'Aşağıdakilerden hangisi asal sayıdır?', 'Aşağıdakilerden hangisi asal sayıdır?', '12', '17', '21', '35', '9', 'B', NULL, 20],
        ['test', 9, 'Bir karenin çevresi 20 cm ise alanı kaçtır?', 'Bir karenin çevresi 20 cm ise alanı kaçtır?', '5', '10', '25', '20', '50', 'C', NULL, 15],
        ['klasik', 9, '24 ile 36\'nın EBOB ve EKOK\'unu bulunuz.', '24 ile 36\'nın EBOB ve EKOK\'unu bulunuz.', NULL, NULL, NULL, NULL, NULL, NULL, 'EBOB = 12, EKOK = 72', 5],
        ['klasik', 9, 'Bir dikdörtgenin uzun kenarı 12 cm, kısa kenarı 8 cm\'dir. Çevresini ve alanını hesaplayınız.', 'Bir dikdörtgenin uzun kenarı 12 cm, kısa kenarı 8 cm\'dir. Çevresini ve alanını hesaplayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Çevre = 2×(12+8) = 40 cm. Alan = 12×8 = 96 cm²', 10],
        ['test', 11, 'Bir üçgenin iç açıları toplamı kaç derecedir?', 'Bir üçgenin iç açıları toplamı kaç derecedir?', '90', '120', '180', '270', '360', 'C', NULL, 10],
        ['test', 10, '3/4 + 1/4 işleminin sonucu nedir?', '3/4 + 1/4 işleminin sonucu nedir?', '1/2', '3/8', '1', '2', '4', 'C', NULL, 5],
        ['klasik', 13, 'Bir dikdörtgenin uzun kenarı 12 cm, kısa kenarı 8 cm\'dir. Çevresini ve alanını hesaplayınız.', 'Bir dikdörtgenin uzun kenarı 12 cm, kısa kenarı 8 cm\'dir. Çevresini ve alanını hesaplayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Çevre = 2×(12+8) = 40 cm. Alan = 12×8 = 96 cm²', 15],
        ['klasik', 14, 'Bir dikdörtgenin uzun kenarı 12 cm, kısa kenarı 8 cm\'dir. Çevresini ve alanını hesaplayınız.', 'Bir dikdörtgenin uzun kenarı 12 cm, kısa kenarı 8 cm\'dir. Çevresini ve alanını hesaplayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Çevre = 2×(12+8) = 40 cm. Alan = 12×8 = 96 cm²', 20],
        ['test', 16, 'En küçük asal sayı hangisidir?', 'En küçük asal sayı hangisidir?', '0', '1', '2', '3', '4', 'C', NULL, 10],
        ['test', 14, 'En büyük iki basamaklı sayı hangisidir?', 'En büyük iki basamaklı sayı hangisidir?', '89', '90', '98', '99', '100', 'D', NULL, 20],
        ['test', 17, '9\'un karekökü kaçtır?', '9\'un karekökü kaçtır?', '2', '3', '4', '6', '81', 'B', NULL, 20],
        ['test', 16, 'Bir üçgenin iç açıları toplamı kaç derecedir?', 'Bir üçgenin iç açıları toplamı kaç derecedir?', '90', '120', '180', '270', '360', 'C', NULL, 10],
        ['klasik', 19, '5 kişilik grubun not ortalaması 72\'dir. Toplamları kaçtır?', '5 kişilik grubun not ortalaması 72\'dir. Toplamları kaçtır?', NULL, NULL, NULL, NULL, NULL, NULL, '5 × 72 = 360', 10],
        ['test', 18, 'En küçük asal sayı hangisidir?', 'En küçük asal sayı hangisidir?', '0', '1', '2', '3', '4', 'C', NULL, 10],
        ['test', 21, 'En büyük iki basamaklı sayı hangisidir?', 'En büyük iki basamaklı sayı hangisidir?', '89', '90', '98', '99', '100', 'D', NULL, 10],
        ['klasik', 21, '2/3 + 3/4 - 1/6 işlemini yapınız.', '2/3 + 3/4 - 1/6 işlemini yapınız.', NULL, NULL, NULL, NULL, NULL, NULL, '8/12 + 9/12 - 2/12 = 15/12 = 5/4 = 1,25', 20],
        ['klasik', 21, '2/3 + 3/4 - 1/6 işlemini yapınız.', '2/3 + 3/4 - 1/6 işlemini yapınız.', NULL, NULL, NULL, NULL, NULL, NULL, '8/12 + 9/12 - 2/12 = 15/12 = 5/4 = 1,25', 10],
        ['test', 23, '15 + 27 işleminin sonucu nedir?', '15 + 27 işleminin sonucu nedir?', '40', '41', '42', '43', '44', 'C', NULL, 10],
        ['klasik', 25, '5 kişilik grubun not ortalaması 72\'dir. Toplamları kaçtır?', '5 kişilik grubun not ortalaması 72\'dir. Toplamları kaçtır?', NULL, NULL, NULL, NULL, NULL, NULL, '5 × 72 = 360', 20],
        ['klasik', 25, '5 kişilik grubun not ortalaması 72\'dir. Toplamları kaçtır?', '5 kişilik grubun not ortalaması 72\'dir. Toplamları kaçtır?', NULL, NULL, NULL, NULL, NULL, NULL, '5 × 72 = 360', 5],
        ['klasik', 26, 'Kapalı bir kutunun kenarları 3, 4 ve 5 cm\'dir. Hacmini bulunuz.', 'Kapalı bir kutunun kenarları 3, 4 ve 5 cm\'dir. Hacmini bulunuz.', NULL, NULL, NULL, NULL, NULL, NULL, 'Hacim = 3 × 4 × 5 = 60 cm³', 10],
        ['test', 29, '100\'ün %30\'u kaçtır?', '100\'ün %30\'u kaçtır?', '10', '20', '30', '40', '50', 'C', NULL, 10],
        ['test', 29, 'Bir üçgenin iç açıları toplamı kaç derecedir?', 'Bir üçgenin iç açıları toplamı kaç derecedir?', '90', '120', '180', '270', '360', 'C', NULL, 10],
        ['klasik', 31, '24 ile 36\'nın EBOB ve EKOK\'unu bulunuz.', '24 ile 36\'nın EBOB ve EKOK\'unu bulunuz.', NULL, NULL, NULL, NULL, NULL, NULL, 'EBOB = 12, EKOK = 72', 20],
        ['test', 32, 'Suyun kimyasal formülü nedir?', 'Suyun kimyasal formülü nedir?', 'CO2', 'H2O', 'O2', 'NaCl', 'CH4', 'B', NULL, 10],
        ['klasik', 31, '24 ile 36\'nın EBOB ve EKOK\'unu bulunuz.', '24 ile 36\'nın EBOB ve EKOK\'unu bulunuz.', NULL, NULL, NULL, NULL, NULL, NULL, 'EBOB = 12, EKOK = 72', 15],
        ['klasik', 33, 'Ekosistemdeki enerji akışını açıklayınız.', 'Ekosistemdeki enerji akışını açıklayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Güneş → Üreticiler → Birincil tüketiciler → İkincil tüketiciler → Ayrıştırıcılar', 10],
        ['test', 35, 'Kan hücresi olmayan kan bileşeni hangisidir?', 'Kan hücresi olmayan kan bileşeni hangisidir?', 'Alyuvar', 'Akyuvar', 'Trombosit', 'Plazma', 'Hemoglobin', 'D', NULL, 10],
        ['test', 36, 'Newton\'un kuvvet birimi hangisidir?', 'Newton\'un kuvvet birimi hangisidir?', 'Joule', 'Watt', 'Newton', 'Pascal', 'Ohm', 'C', NULL, 5],
        ['test', 34, 'Deprem hangi alet ile ölçülür?', 'Deprem hangi alet ile ölçülür?', 'Termometre', 'Barometre', 'Sismograf', 'Higrومetre', 'Manyetometre', 'C', NULL, 10],
        ['test', 38, 'Ses hangi ortamda yayılmaz?', 'Ses hangi ortamda yayılmaz?', 'Katı', 'Sıvı', 'Gaz', 'Vakum', 'Su', 'D', NULL, 10],
        ['test', 39, 'Isıyı iyi ileten madde hangisidir?', 'Isıyı iyi ileten madde hangisidir?', 'Tahta', 'Plastik', 'Cam', 'Demir', 'Kauçuk', 'D', NULL, 10],
        ['klasik', 39, 'Yer çekimi kuvvetini açıklayınız.', 'Yer çekimi kuvvetini açıklayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Kütleli cisimler birbirini çeker. Dünya yüzeyi 9,8 m/s² ivme uygular.', 5],
        ['klasik', 38, 'Bitkisel ve hayvansal hücreler arasındaki farkları yazınız.', 'Bitkisel ve hayvansal hücreler arasındaki farkları yazınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Bitki hücresinde hücre duvarı ve kloroplast bulunur; hayvan hücresinde bunlar yoktur.', 15],
        ['klasik', 41, 'Elektrik devresinde seri ve paralel bağlamayı açıklayınız.', 'Elektrik devresinde seri ve paralel bağlamayı açıklayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Seri: tek yol, akım sabit. Paralel: çok yol, gerilim sabit.', 10],
        ['test', 40, 'En hafif element hangisidir?', 'En hafif element hangisidir?', 'Helyum', 'Hidrojen', 'Lityum', 'Oksijen', 'Karbon', 'B', NULL, 10],
        ['test', 44, 'Deprem hangi alet ile ölçülür?', 'Deprem hangi alet ile ölçülür?', 'Termometre', 'Barometre', 'Sismograf', 'Higrومetre', 'Manyetometre', 'C', NULL, 20],
        ['klasik', 44, 'Kuvvet ve hareket arasındaki ilişkiyi örnekle açıklayınız.', 'Kuvvet ve hareket arasındaki ilişkiyi örnekle açıklayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Net kuvvet uygulandığında cisim hareket eder veya hızı değişir. Örnek: itilen araba.', 10],
        ['klasik', 43, 'Besin zinciri nedir? Bir örnek vererek açıklayınız.', 'Besin zinciri nedir? Bir örnek vererek açıklayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Enerji üreticiden tüketiciye aktarılır. Örnek: Ot → Tavşan → Tilki → Bakteri', 10],
        ['test', 45, 'Beyin hangi sisteme aittir?', 'Beyin hangi sisteme aittir?', 'Sindirim', 'Dolaşım', 'Sinir', 'Boşaltım', 'Solunum', 'C', NULL, 10],
        ['test', 45, 'Omurgasız bir hayvan hangisidir?', 'Omurgasız bir hayvan hangisidir?', 'Balık', 'Kurbağa', 'Ahtapot', 'Yılan', 'Kuş', 'C', NULL, 10],
        ['test', 46, 'Beyin hangi sisteme aittir?', 'Beyin hangi sisteme aittir?', 'Sindirim', 'Dolaşım', 'Sinir', 'Boşaltım', 'Solunum', 'C', NULL, 10],
        ['test', 49, 'Güneş sistemi kaç gezegenden oluşur?', 'Güneş sistemi kaç gezegenden oluşur?', '6', '7', '8', '9', '10', 'C', NULL, 10],
        ['test', 48, 'Newton\'un kuvvet birimi hangisidir?', 'Newton\'un kuvvet birimi hangisidir?', 'Joule', 'Watt', 'Newton', 'Pascal', 'Ohm', 'C', NULL, 10],
        ['klasik', 49, 'Fotosentez olayını kendi cümlelerinizle açıklayınız.', 'Fotosentez olayını kendi cümlelerinizle açıklayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Bitkiler güneş enerjisini kullanarak CO₂ ve suyu glikoz ve O₂\'ye dönüştürür.', 15],
    ];
    foreach ($qBatch as $q) {
        $qid = ins($db, "INSERT INTO `questions` (`type`,`user_id`,`unit_id`,`title`,`content`,`option_a`,`option_b`,`option_c`,`option_d`,`option_e`,`correct_opt`,`answer`,`points`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)",
            [$q[0], $uid, $unitIds[$q[1]], $q[2], $q[3], $q[4], $q[5], $q[6], $q[7], $q[8], $q[9], $q[10], $q[11]]);
        $questionIds[$qIdx] = ['id'=>$qid, 'ui'=>$q[1], 'type'=>$q[0]];
        $qIdx++;
    }
    // Chunk 2/20
    $qBatch = [
        ['test', 51, 'Newton\'un kuvvet birimi hangisidir?', 'Newton\'un kuvvet birimi hangisidir?', 'Joule', 'Watt', 'Newton', 'Pascal', 'Ohm', 'C', NULL, 10],
        ['test', 52, 'Fotosentez nerede gerçekleşir?', 'Fotosentez nerede gerçekleşir?', 'Mitokondri', 'Çekirdek', 'Kloroplast', 'Ribozom', 'Hücre zarı', 'C', NULL, 15],
        ['klasik', 55, 'Bitkisel ve hayvansal hücreler arasındaki farkları yazınız.', 'Bitkisel ve hayvansal hücreler arasındaki farkları yazınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Bitki hücresinde hücre duvarı ve kloroplast bulunur; hayvan hücresinde bunlar yoktur.', 15],
        ['klasik', 54, 'Kuvvet ve hareket arasındaki ilişkiyi örnekle açıklayınız.', 'Kuvvet ve hareket arasındaki ilişkiyi örnekle açıklayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Net kuvvet uygulandığında cisim hareket eder veya hızı değişir. Örnek: itilen araba.', 10],
        ['test', 56, '\'Ağaç\' sözcüğünde kaç hece vardır?', '\'Ağaç\' sözcüğünde kaç hece vardır?', '1', '2', '3', '4', '5', 'B', NULL, 15],
        ['test', 58, 'Aşağıdakilerden hangisi bağlaçtır?', 'Aşağıdakilerden hangisi bağlaçtır?', 'Çok', 'Ve', 'Güzel', 'Hızla', 'Ama', 'B', NULL, 10],
        ['test', 56, 'Aşağıdakilerden hangisi bağlaçtır?', 'Aşağıdakilerden hangisi bağlaçtır?', 'Çok', 'Ve', 'Güzel', 'Hızla', 'Ama', 'B', NULL, 15],
        ['test', 58, '\'Koşmak\' fiilinin emir kipi hangisidir?', '\'Koşmak\' fiilinin emir kipi hangisidir?', 'Koşar', 'Koşacak', 'Koş', 'Koştu', 'Koşuyor', 'C', NULL, 5],
        ['klasik', 58, 'Aşağıdaki fiillerin istek kipini yazınız.', 'Aşağıdaki fiillerin istek kipini yazınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'İstek kipi -e/-a eki ile yapılır: gide, yaza, söyle.', 5],
        ['test', 60, '\'Okul\' sözcüğü hangi türden isimdir?', '\'Okul\' sözcüğü hangi türden isimdir?', 'Özel', 'Soyut', 'Somut', 'Topluluk', 'Eylem', 'C', NULL, 5],
        ['test', 62, 'Hangi cümle soru cümlesidir?', 'Hangi cümle soru cümlesidir?', 'Gel buraya.', 'Ne zaman geldin?', 'Çok güzeldi.', 'Hadi gidelim.', 'Dur orada.', 'B', NULL, 10],
        ['klasik', 63, 'Aşağıdaki cümleleri olumludan olumsuza çeviriniz.', 'Aşağıdaki cümleleri olumludan olumsuza çeviriniz.', NULL, NULL, NULL, NULL, NULL, NULL, 'Fiil olumsuzluk eki (-me/-ma) eklenerek yapılır.', 10],
        ['klasik', 63, '\'Sevgi\' temalı 5-6 cümlelik kısa bir metin yazınız.', '\'Sevgi\' temalı 5-6 cümlelik kısa bir metin yazınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Öğrenci özgün metin üretmelidir.', 15],
        ['test', 66, '\'Koşmak\' fiilinin emir kipi hangisidir?', '\'Koşmak\' fiilinin emir kipi hangisidir?', 'Koşar', 'Koşacak', 'Koş', 'Koştu', 'Koşuyor', 'C', NULL, 10],
        ['test', 67, 'Hangi cümle soru cümlesidir?', 'Hangi cümle soru cümlesidir?', 'Gel buraya.', 'Ne zaman geldin?', 'Çok güzeldi.', 'Hadi gidelim.', 'Dur orada.', 'B', NULL, 5],
        ['test', 68, '\'Büyük\' sözcüğünün zıt anlamlısı nedir?', '\'Büyük\' sözcüğünün zıt anlamlısı nedir?', 'Ufak', 'Uzun', 'Dar', 'Kalın', 'Ağır', 'A', NULL, 10],
        ['klasik', 69, 'Verilen metindeki sıfatları bulunuz ve türlerini belirtiniz.', 'Verilen metindeki sıfatları bulunuz ve türlerini belirtiniz.', NULL, NULL, NULL, NULL, NULL, NULL, 'Niteleme sıfatı: güzel. Sayı sıfatı: üç. Belgisiz sıfat: bazı.', 5],
        ['test', 67, '\'Büyük\' sözcüğünün zıt anlamlısı nedir?', '\'Büyük\' sözcüğünün zıt anlamlısı nedir?', 'Ufak', 'Uzun', 'Dar', 'Kalın', 'Ağır', 'A', NULL, 20],
        ['klasik', 70, 'Aşağıdaki sözcükleri alfabetik sıraya koyunuz.', 'Aşağıdaki sözcükleri alfabetik sıraya koyunuz.', NULL, NULL, NULL, NULL, NULL, NULL, 'Türkçe alfabesine göre a,b,c,ç,d... sırası izlenir.', 5],
        ['test', 70, '\'Gelmek\' fiilinin geniş zamanı hangisidir?', '\'Gelmek\' fiilinin geniş zamanı hangisidir?', 'Geldi', 'Gelecek', 'Gelir', 'Gelsin', 'Gelse', 'C', NULL, 15],
        ['test', 73, 'Hangi sözcük zarf değildir?', 'Hangi sözcük zarf değildir?', 'Hızlı', 'Çabuk', 'Çok', 'Kitap', 'Yavaş', 'D', NULL, 10],
        ['test', 73, 'Hangi sözcük zarf değildir?', 'Hangi sözcük zarf değildir?', 'Hızlı', 'Çabuk', 'Çok', 'Kitap', 'Yavaş', 'D', NULL, 5],
        ['klasik', 75, 'İpek Yolu\'nun tarihsel önemini açıklayınız.', 'İpek Yolu\'nun tarihsel önemini açıklayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Doğu-Batı ticareti, kültür ve bilgi alışverişi için köprü görevi gördü.', 15],
        ['test', 73, '\'Büyük\' sözcüğünün zıt anlamlısı nedir?', '\'Büyük\' sözcüğünün zıt anlamlısı nedir?', 'Ufak', 'Uzun', 'Dar', 'Kalın', 'Ağır', 'A', NULL, 15],
        ['klasik', 74, 'Metindeki noktalama hatalarını bulunuz ve düzeltiniz.', 'Metindeki noktalama hatalarını bulunuz ve düzeltiniz.', NULL, NULL, NULL, NULL, NULL, NULL, 'Yanlış noktalama işaretleri tespit edilip düzeltilmelidir.', 10],
        ['test', 76, 'En uzun ırmak hangisidir?', 'En uzun ırmak hangisidir?', 'Amazon', 'Nil', 'Mississippi', 'Yangtze', 'Volga', 'B', NULL, 10],
        ['klasik', 77, 'İpek Yolu\'nun tarihsel önemini açıklayınız.', 'İpek Yolu\'nun tarihsel önemini açıklayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Doğu-Batı ticareti, kültür ve bilgi alışverişi için köprü görevi gördü.', 5],
        ['test', 78, 'Cumhuriyet hangi tarihte ilan edilmiştir?', 'Cumhuriyet hangi tarihte ilan edilmiştir?', '19 Mayıs', '29 Ekim', '30 Ağustos', '23 Nisan', '10 Kasım', 'B', NULL, 10],
        ['klasik', 80, 'Osmanlı Devleti\'nin yıkılış nedenlerini açıklayınız.', 'Osmanlı Devleti\'nin yıkılış nedenlerini açıklayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Savaş yenilgileri, azınlık isyanları, ekonomik çöküş, Batı\'nın müdahalesi.', 10],
        ['test', 81, 'Türkiye\'nin en uzun nehri hangisidir?', 'Türkiye\'nin en uzun nehri hangisidir?', 'Dicle', 'Fırat', 'Kızılırmak', 'Yeşilırmak', 'Sakarya', 'C', NULL, 20],
        ['test', 83, 'Hangi ülke en fazla nüfusa sahiptir?', 'Hangi ülke en fazla nüfusa sahiptir?', 'Hindistan', 'ABD', 'Rusya', 'Çin', 'Brezilya', 'D', NULL, 10],
        ['klasik', 82, 'Türkiye\'nin coğrafi konumunun avantajlarını yazınız.', 'Türkiye\'nin coğrafi konumunun avantajlarını yazınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Üç tarafı denizle çevrili, ticaret yolları üzerinde, farklı iklimlere sahip.', 15],
        ['klasik', 82, 'Atatürk\'ün gerçekleştirdiği inkılapları sıralayınız.', 'Atatürk\'ün gerçekleştirdiği inkılapları sıralayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Harf, takvim, kıyafet, hukuk, eğitim, kadın hakları inkılapları.', 5],
        ['test', 83, 'Türkiye\'nin en uzun nehri hangisidir?', 'Türkiye\'nin en uzun nehri hangisidir?', 'Dicle', 'Fırat', 'Kızılırmak', 'Yeşilırmak', 'Sakarya', 'C', NULL, 15],
        ['test', 85, 'Dünya\'nın en büyük kıtası hangisidir?', 'Dünya\'nın en büyük kıtası hangisidir?', 'Afrika', 'Amerika', 'Avrupa', 'Asya', 'Avustralya', 'D', NULL, 15],
        ['klasik', 85, 'İpek Yolu\'nun tarihsel önemini açıklayınız.', 'İpek Yolu\'nun tarihsel önemini açıklayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Doğu-Batı ticareti, kültür ve bilgi alışverişi için köprü görevi gördü.', 5],
        ['klasik', 86, 'Tarih öncesi dönemleri özelliklerine göre karşılaştırınız.', 'Tarih öncesi dönemleri özelliklerine göre karşılaştırınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Taş, Maden ve Tunç Çağları; yazı ve metal kullanımına göre ayrılır.', 10],
        ['test', 90, 'Türkiye\'nin başkenti neresidir?', 'Türkiye\'nin başkenti neresidir?', 'İstanbul', 'İzmir', 'Ankara', 'Bursa', 'Antalya', 'C', NULL, 15],
        ['test', 89, 'Osmanlı Devleti hangi yıl kurulmuştur?', 'Osmanlı Devleti hangi yıl kurulmuştur?', '1299', '1400', '1453', '1517', '1683', 'A', NULL, 5],
        ['test', 89, 'Cumhuriyet hangi tarihte ilan edilmiştir?', 'Cumhuriyet hangi tarihte ilan edilmiştir?', '19 Mayıs', '29 Ekim', '30 Ağustos', '23 Nisan', '10 Kasım', 'B', NULL, 15],
        ['klasik', 92, 'Türkiye\'nin komşu ülkelerini ve bu ülkelerle ilişkilerini açıklayınız.', 'Türkiye\'nin komşu ülkelerini ve bu ülkelerle ilişkilerini açıklayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Yunanistan, Bulgaristan, Gürcistan, Ermenistan, İran, Irak, Suriye, Azerbaycan.', 10],
        ['test', 93, 'Hangi şehir iki kıta üzerindedir?', 'Hangi şehir iki kıta üzerindedir?', 'Ankara', 'İzmir', 'İstanbul', 'Bursa', 'Antalya', 'C', NULL, 10],
        ['klasik', 93, 'Osmanlı Devleti\'nin yıkılış nedenlerini açıklayınız.', 'Osmanlı Devleti\'nin yıkılış nedenlerini açıklayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Savaş yenilgileri, azınlık isyanları, ekonomik çöküş, Batı\'nın müdahalesi.', 10],
        ['test', 96, 'Opposite of \'hot\' is:', 'Opposite of \'hot\' is:', 'Warm', 'Cold', 'Cool', 'Mild', 'Humid', 'B', NULL, 5],
        ['test', 94, 'Opposite of \'hot\' is:', 'Opposite of \'hot\' is:', 'Warm', 'Cold', 'Cool', 'Mild', 'Humid', 'B', NULL, 15],
        ['test', 95, 'Which is a greeting?', 'Which is a greeting?', 'Goodbye', 'Thank you', 'Hello', 'Sorry', 'Please', 'C', NULL, 10],
        ['test', 98, 'How many letters are in the English alphabet?', 'How many letters are in the English alphabet?', '24', '25', '26', '27', '28', 'C', NULL, 10],
        ['klasik', 97, 'Write 5 sentences about your daily routine using Present Simple Tense.', 'Write 5 sentences about your daily routine using Present Simple Tense.', NULL, NULL, NULL, NULL, NULL, NULL, 'I wake up at 7. I eat breakfast. I go to school. I study. I sleep at 10.', 10],
        ['test', 100, 'Opposite of \'hot\' is:', 'Opposite of \'hot\' is:', 'Warm', 'Cold', 'Cool', 'Mild', 'Humid', 'B', NULL, 15],
        ['klasik', 101, 'Write 5 sentences about things you will do next weekend.', 'Write 5 sentences about things you will do next weekend.', NULL, NULL, NULL, NULL, NULL, NULL, 'Next weekend I will... I am going to... We will visit... She will help... They will play...', 20],
    ];
    foreach ($qBatch as $q) {
        $qid = ins($db, "INSERT INTO `questions` (`type`,`user_id`,`unit_id`,`title`,`content`,`option_a`,`option_b`,`option_c`,`option_d`,`option_e`,`correct_opt`,`answer`,`points`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)",
            [$q[0], $uid, $unitIds[$q[1]], $q[2], $q[3], $q[4], $q[5], $q[6], $q[7], $q[8], $q[9], $q[10], $q[11]]);
        $questionIds[$qIdx] = ['id'=>$qid, 'ui'=>$q[1], 'type'=>$q[0]];
        $qIdx++;
    }
    // Chunk 3/20
    $qBatch = [
        ['klasik', 100, 'Write a short paragraph about your favourite season.', 'Write a short paragraph about your favourite season.', NULL, NULL, NULL, NULL, NULL, NULL, 'My favourite season is ... because ... The weather is ... I enjoy ...', 15],
        ['klasik', 103, 'Describe your school in 5 sentences.', 'Describe your school in 5 sentences.', NULL, NULL, NULL, NULL, NULL, NULL, 'My school is... It has... The teachers are... I study... My favourite class is...', 5],
        ['test', 103, '\'I ___ a student.\' Fill in the blank.', '\'I ___ a student.\' Fill in the blank.', 'am', 'is', 'are', 'be', 'been', 'A', NULL, 5],
        ['test', 104, 'Which is correct?', 'Which is correct?', 'She don\'t', 'She doesn\'t', 'She not', 'She isn\'t run', 'She no go', 'B', NULL, 15],
        ['klasik', 105, 'Describe your best friend in 4-5 sentences.', 'Describe your best friend in 4-5 sentences.', NULL, NULL, NULL, NULL, NULL, NULL, 'My best friend is ... He/She is ... We like to ... We have been friends since...', 10],
        ['test', 107, '\'We ___ to school yesterday.\' Fill in.', '\'We ___ to school yesterday.\' Fill in.', 'go', 'goes', 'gone', 'went', 'going', 'D', NULL, 10],
        ['test', 106, '\'They ___ playing football.\' Fill in.', '\'They ___ playing football.\' Fill in.', 'is', 'am', 'are', 'was', 'were', 'C', NULL, 10],
        ['test', 109, 'What is the past tense of \'eat\'?', 'What is the past tense of \'eat\'?', 'Eated', 'Ate', 'Eaten', 'Eats', 'Eating', 'B', NULL, 10],
        ['klasik', 109, 'Describe your best friend in 4-5 sentences.', 'Describe your best friend in 4-5 sentences.', NULL, NULL, NULL, NULL, NULL, NULL, 'My best friend is ... He/She is ... We like to ... We have been friends since...', 10],
        ['klasik', 110, 'Make 5 sentences using the Past Simple Tense.', 'Make 5 sentences using the Past Simple Tense.', NULL, NULL, NULL, NULL, NULL, NULL, 'Yesterday I went... Last week she visited... They played... He cooked... We watched...', 15],
        ['test', 0, '5! (5 faktöriyel) kaçtır?', '5! (5 faktöriyel) kaçtır?', '20', '60', '100', '120', '240', 'D', NULL, 5],
        ['klasik', 111, 'Compare summer and winter using at least 5 adjectives.', 'Compare summer and winter using at least 5 adjectives.', NULL, NULL, NULL, NULL, NULL, NULL, 'Summer: hot, sunny, long, fun, bright. Winter: cold, dark, short, snowy, quiet.', 20],
        ['test', 0, 'Aşağıdakilerden hangisi asal sayıdır?', 'Aşağıdakilerden hangisi asal sayıdır?', '12', '17', '21', '35', '9', 'B', NULL, 10],
        ['test', 1, 'Aşağıdakilerden hangisi asal sayıdır?', 'Aşağıdakilerden hangisi asal sayıdır?', '12', '17', '21', '35', '9', 'B', NULL, 5],
        ['test', 3, 'Bir dikdörtgenin alanı 48 cm², kısa kenarı 6 cm ise uzun kenarı kaçtır?', 'Bir dikdörtgenin alanı 48 cm², kısa kenarı 6 cm ise uzun kenarı kaçtır?', '6', '7', '8', '9', '10', 'C', NULL, 5],
        ['test', 4, 'Bir dikdörtgenin alanı 48 cm², kısa kenarı 6 cm ise uzun kenarı kaçtır?', 'Bir dikdörtgenin alanı 48 cm², kısa kenarı 6 cm ise uzun kenarı kaçtır?', '6', '7', '8', '9', '10', 'C', NULL, 10],
        ['test', 3, 'Aşağıdakilerden hangisi asal sayıdır?', 'Aşağıdakilerden hangisi asal sayıdır?', '12', '17', '21', '35', '9', 'B', NULL, 15],
        ['klasik', 7, 'Bir sayının %25\'i 15 ise, bu sayı kaçtır?', 'Bir sayının %25\'i 15 ise, bu sayı kaçtır?', NULL, NULL, NULL, NULL, NULL, NULL, 'x × 0,25 = 15 → x = 60', 20],
        ['klasik', 6, 'Bir dikdörtgenin uzun kenarı 12 cm, kısa kenarı 8 cm\'dir. Çevresini ve alanını hesaplayınız.', 'Bir dikdörtgenin uzun kenarı 12 cm, kısa kenarı 8 cm\'dir. Çevresini ve alanını hesaplayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Çevre = 2×(12+8) = 40 cm. Alan = 12×8 = 96 cm²', 10],
        ['klasik', 7, 'Bir çemberin çapı 14 cm ise çevresi kaçtır? (π=3,14)', 'Bir çemberin çapı 14 cm ise çevresi kaçtır? (π=3,14)', NULL, NULL, NULL, NULL, NULL, NULL, 'Çevre = π × d = 3,14 × 14 = 43,96 cm', 10],
        ['test', 10, '2^5 işleminin sonucu kaçtır?', '2^5 işleminin sonucu kaçtır?', '8', '16', '32', '64', '128', 'C', NULL, 20],
        ['klasik', 10, '2/3 + 3/4 - 1/6 işlemini yapınız.', '2/3 + 3/4 - 1/6 işlemini yapınız.', NULL, NULL, NULL, NULL, NULL, NULL, '8/12 + 9/12 - 2/12 = 15/12 = 5/4 = 1,25', 10],
        ['test', 10, '0,25\'in kesir karşılığı hangisidir?', '0,25\'in kesir karşılığı hangisidir?', '1/2', '1/4', '1/3', '2/5', '3/4', 'B', NULL, 20],
        ['test', 10, '100\'ün %30\'u kaçtır?', '100\'ün %30\'u kaçtır?', '10', '20', '30', '40', '50', 'C', NULL, 5],
        ['test', 14, '100\'ün %30\'u kaçtır?', '100\'ün %30\'u kaçtır?', '10', '20', '30', '40', '50', 'C', NULL, 10],
        ['test', 14, '0,25\'in kesir karşılığı hangisidir?', '0,25\'in kesir karşılığı hangisidir?', '1/2', '1/4', '1/3', '2/5', '3/4', 'B', NULL, 10],
        ['test', 14, 'Bir dikdörtgenin alanı 48 cm², kısa kenarı 6 cm ise uzun kenarı kaçtır?', 'Bir dikdörtgenin alanı 48 cm², kısa kenarı 6 cm ise uzun kenarı kaçtır?', '6', '7', '8', '9', '10', 'C', NULL, 10],
        ['test', 17, 'Aşağıdakilerden hangisi asal sayıdır?', 'Aşağıdakilerden hangisi asal sayıdır?', '12', '17', '21', '35', '9', 'B', NULL, 5],
        ['test', 17, '9\'un karekökü kaçtır?', '9\'un karekökü kaçtır?', '2', '3', '4', '6', '81', 'B', NULL, 15],
        ['klasik', 19, '2/3 + 3/4 - 1/6 işlemini yapınız.', '2/3 + 3/4 - 1/6 işlemini yapınız.', NULL, NULL, NULL, NULL, NULL, NULL, '8/12 + 9/12 - 2/12 = 15/12 = 5/4 = 1,25', 15],
        ['klasik', 19, 'Bir sayının %25\'i 15 ise, bu sayı kaçtır?', 'Bir sayının %25\'i 15 ise, bu sayı kaçtır?', NULL, NULL, NULL, NULL, NULL, NULL, 'x × 0,25 = 15 → x = 60', 5],
        ['test', 20, '2^5 işleminin sonucu kaçtır?', '2^5 işleminin sonucu kaçtır?', '8', '16', '32', '64', '128', 'C', NULL, 10],
        ['test', 19, '3/4 + 1/4 işleminin sonucu nedir?', '3/4 + 1/4 işleminin sonucu nedir?', '1/2', '3/8', '1', '2', '4', 'C', NULL, 10],
        ['klasik', 22, '2/3 + 3/4 - 1/6 işlemini yapınız.', '2/3 + 3/4 - 1/6 işlemini yapınız.', NULL, NULL, NULL, NULL, NULL, NULL, '8/12 + 9/12 - 2/12 = 15/12 = 5/4 = 1,25', 10],
        ['klasik', 24, 'Bir mağaza %20 indirim uyguluyor. 150 TL\'lik ürünün fiyatı kaç TL olur?', 'Bir mağaza %20 indirim uyguluyor. 150 TL\'lik ürünün fiyatı kaç TL olur?', NULL, NULL, NULL, NULL, NULL, NULL, '150 × 0,80 = 120 TL', 15],
        ['test', 22, 'Bir üçgenin iç açıları toplamı kaç derecedir?', 'Bir üçgenin iç açıları toplamı kaç derecedir?', '90', '120', '180', '270', '360', 'C', NULL, 15],
        ['test', 24, 'Bir dikdörtgenin alanı 48 cm², kısa kenarı 6 cm ise uzun kenarı kaçtır?', 'Bir dikdörtgenin alanı 48 cm², kısa kenarı 6 cm ise uzun kenarı kaçtır?', '6', '7', '8', '9', '10', 'C', NULL, 20],
        ['test', 27, '15 + 27 işleminin sonucu nedir?', '15 + 27 işleminin sonucu nedir?', '40', '41', '42', '43', '44', 'C', NULL, 15],
        ['test', 26, 'Bir karenin çevresi 20 cm ise alanı kaçtır?', 'Bir karenin çevresi 20 cm ise alanı kaçtır?', '5', '10', '25', '20', '50', 'C', NULL, 5],
        ['klasik', 28, 'Bir mağaza %20 indirim uyguluyor. 150 TL\'lik ürünün fiyatı kaç TL olur?', 'Bir mağaza %20 indirim uyguluyor. 150 TL\'lik ürünün fiyatı kaç TL olur?', NULL, NULL, NULL, NULL, NULL, NULL, '150 × 0,80 = 120 TL', 20],
        ['klasik', 27, '24 ile 36\'nın EBOB ve EKOK\'unu bulunuz.', '24 ile 36\'nın EBOB ve EKOK\'unu bulunuz.', NULL, NULL, NULL, NULL, NULL, NULL, 'EBOB = 12, EKOK = 72', 10],
        ['klasik', 30, '5 kişilik grubun not ortalaması 72\'dir. Toplamları kaçtır?', '5 kişilik grubun not ortalaması 72\'dir. Toplamları kaçtır?', NULL, NULL, NULL, NULL, NULL, NULL, '5 × 72 = 360', 10],
        ['klasik', 32, 'Fotosentez olayını kendi cümlelerinizle açıklayınız.', 'Fotosentez olayını kendi cümlelerinizle açıklayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Bitkiler güneş enerjisini kullanarak CO₂ ve suyu glikoz ve O₂\'ye dönüştürür.', 15],
        ['test', 31, '100\'ün %30\'u kaçtır?', '100\'ün %30\'u kaçtır?', '10', '20', '30', '40', '50', 'C', NULL, 20],
        ['klasik', 34, 'Maddenin üç halini karşılaştırınız.', 'Maddenin üç halini karşılaştırınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Katı: sabit şekil/hacim. Sıvı: sabit hacim, değişken şekil. Gaz: değişken şekil/hacim.', 10],
        ['test', 34, 'Deprem hangi alet ile ölçülür?', 'Deprem hangi alet ile ölçülür?', 'Termometre', 'Barometre', 'Sismograf', 'Higrومetre', 'Manyetometre', 'C', NULL, 5],
        ['test', 35, 'Isıyı iyi ileten madde hangisidir?', 'Isıyı iyi ileten madde hangisidir?', 'Tahta', 'Plastik', 'Cam', 'Demir', 'Kauçuk', 'D', NULL, 10],
        ['test', 36, 'Hücrenin kontrol merkezi hangisidir?', 'Hücrenin kontrol merkezi hangisidir?', 'Mitokondri', 'Çekirdek', 'Hücre zarı', 'Kloroplast', 'Ribozom', 'B', NULL, 10],
        ['test', 36, 'Omurgasız bir hayvan hangisidir?', 'Omurgasız bir hayvan hangisidir?', 'Balık', 'Kurbağa', 'Ahtapot', 'Yılan', 'Kuş', 'C', NULL, 20],
        ['klasik', 37, 'Kuvvet ve hareket arasındaki ilişkiyi örnekle açıklayınız.', 'Kuvvet ve hareket arasındaki ilişkiyi örnekle açıklayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Net kuvvet uygulandığında cisim hareket eder veya hızı değişir. Örnek: itilen araba.', 10],
    ];
    foreach ($qBatch as $q) {
        $qid = ins($db, "INSERT INTO `questions` (`type`,`user_id`,`unit_id`,`title`,`content`,`option_a`,`option_b`,`option_c`,`option_d`,`option_e`,`correct_opt`,`answer`,`points`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)",
            [$q[0], $uid, $unitIds[$q[1]], $q[2], $q[3], $q[4], $q[5], $q[6], $q[7], $q[8], $q[9], $q[10], $q[11]]);
        $questionIds[$qIdx] = ['id'=>$qid, 'ui'=>$q[1], 'type'=>$q[0]];
        $qIdx++;
    }
    // Chunk 4/20
    $qBatch = [
        ['test', 39, 'Kan hücresi olmayan kan bileşeni hangisidir?', 'Kan hücresi olmayan kan bileşeni hangisidir?', 'Alyuvar', 'Akyuvar', 'Trombosit', 'Plazma', 'Hemoglobin', 'D', NULL, 10],
        ['test', 40, 'DNA nerede bulunur?', 'DNA nerede bulunur?', 'Ribozom', 'Mitokondri', 'Çekirdek', 'Hücre zarı', 'Sitoplazma', 'C', NULL, 10],
        ['test', 40, 'Güneş sistemi kaç gezegenden oluşur?', 'Güneş sistemi kaç gezegenden oluşur?', '6', '7', '8', '9', '10', 'C', NULL, 10],
        ['klasik', 43, 'Besin zinciri nedir? Bir örnek vererek açıklayınız.', 'Besin zinciri nedir? Bir örnek vererek açıklayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Enerji üreticiden tüketiciye aktarılır. Örnek: Ot → Tavşan → Tilki → Bakteri', 5],
        ['test', 44, 'Deprem hangi alet ile ölçülür?', 'Deprem hangi alet ile ölçülür?', 'Termometre', 'Barometre', 'Sismograf', 'Higrومetre', 'Manyetometre', 'C', NULL, 20],
        ['klasik', 43, 'Besin zinciri nedir? Bir örnek vererek açıklayınız.', 'Besin zinciri nedir? Bir örnek vererek açıklayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Enerji üreticiden tüketiciye aktarılır. Örnek: Ot → Tavşan → Tilki → Bakteri', 10],
        ['test', 46, 'Işığın havadaki hızı yaklaşık kaç km/s?', 'Işığın havadaki hızı yaklaşık kaç km/s?', '100.000', '200.000', '300.000', '400.000', '150.000', 'C', NULL, 10],
        ['klasik', 44, 'Kuvvet ve hareket arasındaki ilişkiyi örnekle açıklayınız.', 'Kuvvet ve hareket arasındaki ilişkiyi örnekle açıklayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Net kuvvet uygulandığında cisim hareket eder veya hızı değişir. Örnek: itilen araba.', 5],
        ['test', 48, 'Isıyı iyi ileten madde hangisidir?', 'Isıyı iyi ileten madde hangisidir?', 'Tahta', 'Plastik', 'Cam', 'Demir', 'Kauçuk', 'D', NULL, 20],
        ['test', 49, 'DNA nerede bulunur?', 'DNA nerede bulunur?', 'Ribozom', 'Mitokondri', 'Çekirdek', 'Hücre zarı', 'Sitoplazma', 'C', NULL, 10],
        ['test', 47, 'Hücrenin kontrol merkezi hangisidir?', 'Hücrenin kontrol merkezi hangisidir?', 'Mitokondri', 'Çekirdek', 'Hücre zarı', 'Kloroplast', 'Ribozom', 'B', NULL, 10],
        ['klasik', 50, 'Yer çekimi kuvvetini açıklayınız.', 'Yer çekimi kuvvetini açıklayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Kütleli cisimler birbirini çeker. Dünya yüzeyi 9,8 m/s² ivme uygular.', 10],
        ['klasik', 52, 'Maddenin üç halini karşılaştırınız.', 'Maddenin üç halini karşılaştırınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Katı: sabit şekil/hacim. Sıvı: sabit hacim, değişken şekil. Gaz: değişken şekil/hacim.', 15],
        ['klasik', 53, 'Besin zinciri nedir? Bir örnek vererek açıklayınız.', 'Besin zinciri nedir? Bir örnek vererek açıklayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Enerji üreticiden tüketiciye aktarılır. Örnek: Ot → Tavşan → Tilki → Bakteri', 20],
        ['test', 54, 'Bitkiler fotosentezde hangi gazı kullanır?', 'Bitkiler fotosentezde hangi gazı kullanır?', 'O2', 'CO2', 'N2', 'H2', 'CH4', 'B', NULL, 10],
        ['klasik', 54, 'Besin zinciri nedir? Bir örnek vererek açıklayınız.', 'Besin zinciri nedir? Bir örnek vererek açıklayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Enerji üreticiden tüketiciye aktarılır. Örnek: Ot → Tavşan → Tilki → Bakteri', 15],
        ['klasik', 56, 'Verilen kelimeleri kullanarak anlamlı bir paragraf yazınız.', 'Verilen kelimeleri kullanarak anlamlı bir paragraf yazınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Öğrenci verilen kelimelerle tutarlı bir paragraf oluşturmalıdır.', 10],
        ['test', 57, 'Cümle sonu hangi noktalama işareti kullanılır?', 'Cümle sonu hangi noktalama işareti kullanılır?', 'Virgül', 'Noktalı virgül', 'Nokta', 'Tire', 'İki nokta', 'C', NULL, 10],
        ['test', 56, '\'Ağaç\' sözcüğünde kaç hece vardır?', '\'Ağaç\' sözcüğünde kaç hece vardır?', '1', '2', '3', '4', '5', 'B', NULL, 10],
        ['test', 56, '\'Gelmek\' fiilinin geniş zamanı hangisidir?', '\'Gelmek\' fiilinin geniş zamanı hangisidir?', 'Geldi', 'Gelecek', 'Gelir', 'Gelsin', 'Gelse', 'C', NULL, 10],
        ['klasik', 60, 'Verilen kelimeleri kullanarak anlamlı bir paragraf yazınız.', 'Verilen kelimeleri kullanarak anlamlı bir paragraf yazınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Öğrenci verilen kelimelerle tutarlı bir paragraf oluşturmalıdır.', 20],
        ['test', 59, '\'Ağaç\' sözcüğünde kaç hece vardır?', '\'Ağaç\' sözcüğünde kaç hece vardır?', '1', '2', '3', '4', '5', 'B', NULL, 10],
        ['test', 61, '\'Çalışkan\' sözcüğü hangi türden sıfattır?', '\'Çalışkan\' sözcüğü hangi türden sıfattır?', 'Niteleme', 'Belgisiz', 'İşaret', 'Soru', 'Sayı', 'A', NULL, 10],
        ['test', 60, '\'Çalışkan\' sözcüğü hangi türden sıfattır?', '\'Çalışkan\' sözcüğü hangi türden sıfattır?', 'Niteleme', 'Belgisiz', 'İşaret', 'Soru', 'Sayı', 'A', NULL, 10],
        ['klasik', 64, 'Metinden çıkarılabilecek ana fikri yazınız.', 'Metinden çıkarılabilecek ana fikri yazınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Öğrenci metni okuyup ana düşünceyi ifade etmelidir.', 15],
        ['klasik', 62, 'Paragraftaki bağlaçları ve görevlerini yazınız.', 'Paragraftaki bağlaçları ve görevlerini yazınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Ve (ekleme), ama (karşıtlık), çünkü (neden-sonuç).', 15],
        ['test', 66, '\'Çalışkan\' sözcüğü hangi türden sıfattır?', '\'Çalışkan\' sözcüğü hangi türden sıfattır?', 'Niteleme', 'Belgisiz', 'İşaret', 'Soru', 'Sayı', 'A', NULL, 10],
        ['klasik', 66, 'Metinden çıkarılabilecek ana fikri yazınız.', 'Metinden çıkarılabilecek ana fikri yazınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Öğrenci metni okuyup ana düşünceyi ifade etmelidir.', 10],
        ['test', 66, '\'Büyük\' sözcüğünün zıt anlamlısı nedir?', '\'Büyük\' sözcüğünün zıt anlamlısı nedir?', 'Ufak', 'Uzun', 'Dar', 'Kalın', 'Ağır', 'A', NULL, 10],
        ['test', 69, '\'Koşmak\' fiilinin emir kipi hangisidir?', '\'Koşmak\' fiilinin emir kipi hangisidir?', 'Koşar', 'Koşacak', 'Koş', 'Koştu', 'Koşuyor', 'C', NULL, 10],
        ['klasik', 69, 'Şiirde kullanılan söz sanatlarını bulunuz.', 'Şiirde kullanılan söz sanatlarını bulunuz.', NULL, NULL, NULL, NULL, NULL, NULL, 'Teşbih, istiare, kişileştirme, abartma incelenir.', 10],
        ['klasik', 69, 'Verilen metindeki sıfatları bulunuz ve türlerini belirtiniz.', 'Verilen metindeki sıfatları bulunuz ve türlerini belirtiniz.', NULL, NULL, NULL, NULL, NULL, NULL, 'Niteleme sıfatı: güzel. Sayı sıfatı: üç. Belgisiz sıfat: bazı.', 10],
        ['klasik', 69, 'Metinden çıkarılabilecek ana fikri yazınız.', 'Metinden çıkarılabilecek ana fikri yazınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Öğrenci metni okuyup ana düşünceyi ifade etmelidir.', 15],
        ['test', 70, 'Hangi sözcük zarf değildir?', 'Hangi sözcük zarf değildir?', 'Hızlı', 'Çabuk', 'Çok', 'Kitap', 'Yavaş', 'D', NULL, 10],
        ['klasik', 72, 'Metindeki noktalama hatalarını bulunuz ve düzeltiniz.', 'Metindeki noktalama hatalarını bulunuz ve düzeltiniz.', NULL, NULL, NULL, NULL, NULL, NULL, 'Yanlış noktalama işaretleri tespit edilip düzeltilmelidir.', 10],
        ['test', 72, '\'Okul\' sözcüğü hangi türden isimdir?', '\'Okul\' sözcüğü hangi türden isimdir?', 'Özel', 'Soyut', 'Somut', 'Topluluk', 'Eylem', 'C', NULL, 10],
        ['test', 74, 'Anlatıcı bakış açısı kaç türdür?', 'Anlatıcı bakış açısı kaç türdür?', '2', '3', '4', '5', '6', 'C', NULL, 10],
        ['test', 77, 'Cumhuriyet hangi tarihte ilan edilmiştir?', 'Cumhuriyet hangi tarihte ilan edilmiştir?', '19 Mayıs', '29 Ekim', '30 Ağustos', '23 Nisan', '10 Kasım', 'B', NULL, 10],
        ['klasik', 77, 'Türkiye\'nin coğrafi konumunun avantajlarını yazınız.', 'Türkiye\'nin coğrafi konumunun avantajlarını yazınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Üç tarafı denizle çevrili, ticaret yolları üzerinde, farklı iklimlere sahip.', 10],
        ['klasik', 76, 'Atatürk\'ün gerçekleştirdiği inkılapları sıralayınız.', 'Atatürk\'ün gerçekleştirdiği inkılapları sıralayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Harf, takvim, kıyafet, hukuk, eğitim, kadın hakları inkılapları.', 5],
        ['test', 79, 'Türkiye Cumhuriyeti hangi yıl kurulmuştur?', 'Türkiye Cumhuriyeti hangi yıl kurulmuştur?', '1919', '1920', '1923', '1925', '1938', 'C', NULL, 5],
        ['klasik', 78, 'Demokrasinin temel ilkelerini açıklayınız.', 'Demokrasinin temel ilkelerini açıklayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Milli egemenlik, temel haklar, hukukun üstünlüğü, çoğulculuk.', 10],
        ['klasik', 80, 'İpek Yolu\'nun tarihsel önemini açıklayınız.', 'İpek Yolu\'nun tarihsel önemini açıklayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Doğu-Batı ticareti, kültür ve bilgi alışverişi için köprü görevi gördü.', 15],
        ['test', 81, 'Türkiye\'nin başkenti neresidir?', 'Türkiye\'nin başkenti neresidir?', 'İstanbul', 'İzmir', 'Ankara', 'Bursa', 'Antalya', 'C', NULL, 10],
        ['test', 81, 'Türkiye kaç komşuya sahiptir?', 'Türkiye kaç komşuya sahiptir?', '5', '6', '7', '8', '9', 'D', NULL, 10],
        ['klasik', 85, 'Tarih öncesi dönemleri özelliklerine göre karşılaştırınız.', 'Tarih öncesi dönemleri özelliklerine göre karşılaştırınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Taş, Maden ve Tunç Çağları; yazı ve metal kullanımına göre ayrılır.', 10],
        ['klasik', 84, 'Türkiye\'deki iklim çeşitliliğinin nedenlerini açıklayınız.', 'Türkiye\'deki iklim çeşitliliğinin nedenlerini açıklayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Enlem, yükselti, denizden uzaklık ve dağların uzanışı belirleyicidir.', 20],
        ['klasik', 84, 'Demokrasinin temel ilkelerini açıklayınız.', 'Demokrasinin temel ilkelerini açıklayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Milli egemenlik, temel haklar, hukukun üstünlüğü, çoğulculuk.', 10],
        ['test', 87, 'Türkiye hangi yarımkürededir?', 'Türkiye hangi yarımkürededir?', 'Güney', 'Kuzey', 'Batı', 'Doğu', 'Hepsi', 'B', NULL, 5],
        ['test', 88, 'Türkiye Cumhuriyeti hangi yıl kurulmuştur?', 'Türkiye Cumhuriyeti hangi yıl kurulmuştur?', '1919', '1920', '1923', '1925', '1938', 'C', NULL, 10],
    ];
    foreach ($qBatch as $q) {
        $qid = ins($db, "INSERT INTO `questions` (`type`,`user_id`,`unit_id`,`title`,`content`,`option_a`,`option_b`,`option_c`,`option_d`,`option_e`,`correct_opt`,`answer`,`points`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)",
            [$q[0], $uid, $unitIds[$q[1]], $q[2], $q[3], $q[4], $q[5], $q[6], $q[7], $q[8], $q[9], $q[10], $q[11]]);
        $questionIds[$qIdx] = ['id'=>$qid, 'ui'=>$q[1], 'type'=>$q[0]];
        $qIdx++;
    }
    // Chunk 5/20
    $qBatch = [
        ['klasik', 90, 'Cumhuriyetin ilanının önemini yazınız.', 'Cumhuriyetin ilanının önemini yazınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Milli egemenlik ilkesi yerleşti; halkın yönetime katılımı sağlandı.', 10],
        ['test', 88, 'Cumhuriyet hangi tarihte ilan edilmiştir?', 'Cumhuriyet hangi tarihte ilan edilmiştir?', '19 Mayıs', '29 Ekim', '30 Ağustos', '23 Nisan', '10 Kasım', 'B', NULL, 20],
        ['test', 90, 'Osmanlı Devleti hangi yıl kurulmuştur?', 'Osmanlı Devleti hangi yıl kurulmuştur?', '1299', '1400', '1453', '1517', '1683', 'A', NULL, 10],
        ['klasik', 90, 'Osmanlı Devleti\'nin yıkılış nedenlerini açıklayınız.', 'Osmanlı Devleti\'nin yıkılış nedenlerini açıklayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Savaş yenilgileri, azınlık isyanları, ekonomik çöküş, Batı\'nın müdahalesi.', 15],
        ['test', 91, 'Türkiye Cumhuriyeti hangi yıl kurulmuştur?', 'Türkiye Cumhuriyeti hangi yıl kurulmuştur?', '1919', '1920', '1923', '1925', '1938', 'C', NULL, 10],
        ['test', 94, '\'We ___ to school yesterday.\' Fill in.', '\'We ___ to school yesterday.\' Fill in.', 'go', 'goes', 'gone', 'went', 'going', 'D', NULL, 20],
        ['test', 95, 'What is the past tense of \'go\'?', 'What is the past tense of \'go\'?', 'Goed', 'Goes', 'Going', 'Gone', 'Went', 'E', NULL, 10],
        ['test', 97, '\'We ___ to school yesterday.\' Fill in.', '\'We ___ to school yesterday.\' Fill in.', 'go', 'goes', 'gone', 'went', 'going', 'D', NULL, 10],
        ['klasik', 98, 'Compare summer and winter using at least 5 adjectives.', 'Compare summer and winter using at least 5 adjectives.', NULL, NULL, NULL, NULL, NULL, NULL, 'Summer: hot, sunny, long, fun, bright. Winter: cold, dark, short, snowy, quiet.', 10],
        ['klasik', 97, 'Write 5 questions using Wh- question words.', 'Write 5 questions using Wh- question words.', NULL, NULL, NULL, NULL, NULL, NULL, 'What, Where, When, Why, Who ile 5 soru cümlesi yazılmalıdır.', 20],
        ['klasik', 99, 'Describe your school in 5 sentences.', 'Describe your school in 5 sentences.', NULL, NULL, NULL, NULL, NULL, NULL, 'My school is... It has... The teachers are... I study... My favourite class is...', 15],
        ['test', 101, 'Opposite of \'hot\' is:', 'Opposite of \'hot\' is:', 'Warm', 'Cold', 'Cool', 'Mild', 'Humid', 'B', NULL, 20],
        ['test', 101, 'Which sentence is correct?', 'Which sentence is correct?', 'I is happy', 'He are sad', 'She is tired', 'They is late', 'We am here', 'C', NULL, 10],
        ['test', 100, 'Which one is a verb?', 'Which one is a verb?', 'Happy', 'Book', 'Run', 'Beautiful', 'Quickly', 'C', NULL, 10],
        ['klasik', 102, 'Write 5 sentences about things you will do next weekend.', 'Write 5 sentences about things you will do next weekend.', NULL, NULL, NULL, NULL, NULL, NULL, 'Next weekend I will... I am going to... We will visit... She will help... They will play...', 15],
        ['test', 105, 'Which one is a verb?', 'Which one is a verb?', 'Happy', 'Book', 'Run', 'Beautiful', 'Quickly', 'C', NULL, 10],
        ['test', 105, 'Which is a greeting?', 'Which is a greeting?', 'Goodbye', 'Thank you', 'Hello', 'Sorry', 'Please', 'C', NULL, 10],
        ['klasik', 106, 'Write a short paragraph about your favourite season.', 'Write a short paragraph about your favourite season.', NULL, NULL, NULL, NULL, NULL, NULL, 'My favourite season is ... because ... The weather is ... I enjoy ...', 10],
        ['klasik', 107, 'Write 5 questions using Wh- question words.', 'Write 5 questions using Wh- question words.', NULL, NULL, NULL, NULL, NULL, NULL, 'What, Where, When, Why, Who ile 5 soru cümlesi yazılmalıdır.', 20],
        ['test', 108, 'Which one is a verb?', 'Which one is a verb?', 'Happy', 'Book', 'Run', 'Beautiful', 'Quickly', 'C', NULL, 15],
        ['test', 108, '\'They ___ playing football.\' Fill in.', '\'They ___ playing football.\' Fill in.', 'is', 'am', 'are', 'was', 'were', 'C', NULL, 20],
        ['test', 111, '\'They ___ playing football.\' Fill in.', '\'They ___ playing football.\' Fill in.', 'is', 'am', 'are', 'was', 'were', 'C', NULL, 10],
        ['klasik', 112, 'Write 5 sentences about your daily routine using Present Simple Tense.', 'Write 5 sentences about your daily routine using Present Simple Tense.', NULL, NULL, NULL, NULL, NULL, NULL, 'I wake up at 7. I eat breakfast. I go to school. I study. I sleep at 10.', 10],
        ['klasik', 0, '2/3 + 3/4 - 1/6 işlemini yapınız.', '2/3 + 3/4 - 1/6 işlemini yapınız.', NULL, NULL, NULL, NULL, NULL, NULL, '8/12 + 9/12 - 2/12 = 15/12 = 5/4 = 1,25', 5],
        ['test', 0, '36\'nın karekökü kaçtır?', '36\'nın karekökü kaçtır?', '4', '5', '6', '7', '8', 'C', NULL, 20],
        ['test', 0, '100\'ün %30\'u kaçtır?', '100\'ün %30\'u kaçtır?', '10', '20', '30', '40', '50', 'C', NULL, 15],
        ['test', 2, '3/4 + 1/4 işleminin sonucu nedir?', '3/4 + 1/4 işleminin sonucu nedir?', '1/2', '3/8', '1', '2', '4', 'C', NULL, 15],
        ['test', 3, 'Bir üçgenin iç açıları toplamı kaç derecedir?', 'Bir üçgenin iç açıları toplamı kaç derecedir?', '90', '120', '180', '270', '360', 'C', NULL, 20],
        ['test', 4, 'Bir üçgenin iç açıları toplamı kaç derecedir?', 'Bir üçgenin iç açıları toplamı kaç derecedir?', '90', '120', '180', '270', '360', 'C', NULL, 10],
        ['test', 5, 'Bir dikdörtgenin alanı 48 cm², kısa kenarı 6 cm ise uzun kenarı kaçtır?', 'Bir dikdörtgenin alanı 48 cm², kısa kenarı 6 cm ise uzun kenarı kaçtır?', '6', '7', '8', '9', '10', 'C', NULL, 10],
        ['klasik', 4, 'Bir mağaza %20 indirim uyguluyor. 150 TL\'lik ürünün fiyatı kaç TL olur?', 'Bir mağaza %20 indirim uyguluyor. 150 TL\'lik ürünün fiyatı kaç TL olur?', NULL, NULL, NULL, NULL, NULL, NULL, '150 × 0,80 = 120 TL', 10],
        ['klasik', 5, '2/3 + 3/4 - 1/6 işlemini yapınız.', '2/3 + 3/4 - 1/6 işlemini yapınız.', NULL, NULL, NULL, NULL, NULL, NULL, '8/12 + 9/12 - 2/12 = 15/12 = 5/4 = 1,25', 20],
        ['test', 7, '5! (5 faktöriyel) kaçtır?', '5! (5 faktöriyel) kaçtır?', '20', '60', '100', '120', '240', 'D', NULL, 20],
        ['test', 10, 'Bir dikdörtgenin alanı 48 cm², kısa kenarı 6 cm ise uzun kenarı kaçtır?', 'Bir dikdörtgenin alanı 48 cm², kısa kenarı 6 cm ise uzun kenarı kaçtır?', '6', '7', '8', '9', '10', 'C', NULL, 15],
        ['klasik', 10, 'Bir mağaza %20 indirim uyguluyor. 150 TL\'lik ürünün fiyatı kaç TL olur?', 'Bir mağaza %20 indirim uyguluyor. 150 TL\'lik ürünün fiyatı kaç TL olur?', NULL, NULL, NULL, NULL, NULL, NULL, '150 × 0,80 = 120 TL', 10],
        ['test', 11, '2^5 işleminin sonucu kaçtır?', '2^5 işleminin sonucu kaçtır?', '8', '16', '32', '64', '128', 'C', NULL, 10],
        ['test', 12, '0,5 + 0,75 kaçtır?', '0,5 + 0,75 kaçtır?', '1', '1,25', '1,5', '0,75', '2', 'B', NULL, 15],
        ['test', 12, 'Bir üçgenin iç açıları toplamı kaç derecedir?', 'Bir üçgenin iç açıları toplamı kaç derecedir?', '90', '120', '180', '270', '360', 'C', NULL, 5],
        ['klasik', 14, 'Bir sayının %25\'i 15 ise, bu sayı kaçtır?', 'Bir sayının %25\'i 15 ise, bu sayı kaçtır?', NULL, NULL, NULL, NULL, NULL, NULL, 'x × 0,25 = 15 → x = 60', 10],
        ['test', 16, '15 + 27 işleminin sonucu nedir?', '15 + 27 işleminin sonucu nedir?', '40', '41', '42', '43', '44', 'C', NULL, 5],
        ['test', 16, '3/4 + 1/4 işleminin sonucu nedir?', '3/4 + 1/4 işleminin sonucu nedir?', '1/2', '3/8', '1', '2', '4', 'C', NULL, 10],
        ['test', 17, '100\'ün %30\'u kaçtır?', '100\'ün %30\'u kaçtır?', '10', '20', '30', '40', '50', 'C', NULL, 5],
        ['klasik', 18, 'Kapalı bir kutunun kenarları 3, 4 ve 5 cm\'dir. Hacmini bulunuz.', 'Kapalı bir kutunun kenarları 3, 4 ve 5 cm\'dir. Hacmini bulunuz.', NULL, NULL, NULL, NULL, NULL, NULL, 'Hacim = 3 × 4 × 5 = 60 cm³', 10],
        ['klasik', 17, '24 ile 36\'nın EBOB ve EKOK\'unu bulunuz.', '24 ile 36\'nın EBOB ve EKOK\'unu bulunuz.', NULL, NULL, NULL, NULL, NULL, NULL, 'EBOB = 12, EKOK = 72', 10],
        ['test', 21, '100\'ün %30\'u kaçtır?', '100\'ün %30\'u kaçtır?', '10', '20', '30', '40', '50', 'C', NULL, 20],
        ['test', 19, '15 + 27 işleminin sonucu nedir?', '15 + 27 işleminin sonucu nedir?', '40', '41', '42', '43', '44', 'C', NULL, 15],
        ['test', 22, '100\'ün %30\'u kaçtır?', '100\'ün %30\'u kaçtır?', '10', '20', '30', '40', '50', 'C', NULL, 10],
        ['test', 21, '0,5 + 0,75 kaçtır?', '0,5 + 0,75 kaçtır?', '1', '1,25', '1,5', '0,75', '2', 'B', NULL, 10],
        ['klasik', 23, 'Bir çemberin çapı 14 cm ise çevresi kaçtır? (π=3,14)', 'Bir çemberin çapı 14 cm ise çevresi kaçtır? (π=3,14)', NULL, NULL, NULL, NULL, NULL, NULL, 'Çevre = π × d = 3,14 × 14 = 43,96 cm', 10],
        ['klasik', 26, 'Bir mağaza %20 indirim uyguluyor. 150 TL\'lik ürünün fiyatı kaç TL olur?', 'Bir mağaza %20 indirim uyguluyor. 150 TL\'lik ürünün fiyatı kaç TL olur?', NULL, NULL, NULL, NULL, NULL, NULL, '150 × 0,80 = 120 TL', 10],
    ];
    foreach ($qBatch as $q) {
        $qid = ins($db, "INSERT INTO `questions` (`type`,`user_id`,`unit_id`,`title`,`content`,`option_a`,`option_b`,`option_c`,`option_d`,`option_e`,`correct_opt`,`answer`,`points`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)",
            [$q[0], $uid, $unitIds[$q[1]], $q[2], $q[3], $q[4], $q[5], $q[6], $q[7], $q[8], $q[9], $q[10], $q[11]]);
        $questionIds[$qIdx] = ['id'=>$qid, 'ui'=>$q[1], 'type'=>$q[0]];
        $qIdx++;
    }
    // Chunk 6/20
    $qBatch = [
        ['test', 27, 'En büyük iki basamaklı sayı hangisidir?', 'En büyük iki basamaklı sayı hangisidir?', '89', '90', '98', '99', '100', 'D', NULL, 15],
        ['klasik', 25, '24 ile 36\'nın EBOB ve EKOK\'unu bulunuz.', '24 ile 36\'nın EBOB ve EKOK\'unu bulunuz.', NULL, NULL, NULL, NULL, NULL, NULL, 'EBOB = 12, EKOK = 72', 20],
        ['klasik', 26, '5 kişilik grubun not ortalaması 72\'dir. Toplamları kaçtır?', '5 kişilik grubun not ortalaması 72\'dir. Toplamları kaçtır?', NULL, NULL, NULL, NULL, NULL, NULL, '5 × 72 = 360', 10],
        ['test', 28, '2^5 işleminin sonucu kaçtır?', '2^5 işleminin sonucu kaçtır?', '8', '16', '32', '64', '128', 'C', NULL, 5],
        ['test', 29, 'En küçük asal sayı hangisidir?', 'En küçük asal sayı hangisidir?', '0', '1', '2', '3', '4', 'C', NULL, 15],
        ['test', 29, '3/4 + 1/4 işleminin sonucu nedir?', '3/4 + 1/4 işleminin sonucu nedir?', '1/2', '3/8', '1', '2', '4', 'C', NULL, 10],
        ['klasik', 33, 'Ses dalgalarının özellikleri nelerdir?', 'Ses dalgalarının özellikleri nelerdir?', NULL, NULL, NULL, NULL, NULL, NULL, 'Ses mekanik dalgadır; ortam gerektirir. Frekans (Hz) ve genlik ile tanımlanır.', 10],
        ['test', 33, 'Suyun kimyasal formülü nedir?', 'Suyun kimyasal formülü nedir?', 'CO2', 'H2O', 'O2', 'NaCl', 'CH4', 'B', NULL, 10],
        ['klasik', 34, 'Ses dalgalarının özellikleri nelerdir?', 'Ses dalgalarının özellikleri nelerdir?', NULL, NULL, NULL, NULL, NULL, NULL, 'Ses mekanik dalgadır; ortam gerektirir. Frekans (Hz) ve genlik ile tanımlanır.', 10],
        ['klasik', 33, 'Besin zinciri nedir? Bir örnek vererek açıklayınız.', 'Besin zinciri nedir? Bir örnek vererek açıklayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Enerji üreticiden tüketiciye aktarılır. Örnek: Ot → Tavşan → Tilki → Bakteri', 10],
        ['test', 35, 'Güneş sistemi kaç gezegenden oluşur?', 'Güneş sistemi kaç gezegenden oluşur?', '6', '7', '8', '9', '10', 'C', NULL, 15],
        ['klasik', 36, 'Kuvvet ve hareket arasındaki ilişkiyi örnekle açıklayınız.', 'Kuvvet ve hareket arasındaki ilişkiyi örnekle açıklayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Net kuvvet uygulandığında cisim hareket eder veya hızı değişir. Örnek: itilen araba.', 10],
        ['test', 37, 'Fotosentez nerede gerçekleşir?', 'Fotosentez nerede gerçekleşir?', 'Mitokondri', 'Çekirdek', 'Kloroplast', 'Ribozom', 'Hücre zarı', 'C', NULL, 10],
        ['test', 39, 'Ses hangi ortamda yayılmaz?', 'Ses hangi ortamda yayılmaz?', 'Katı', 'Sıvı', 'Gaz', 'Vakum', 'Su', 'D', NULL, 15],
        ['test', 40, 'Deprem hangi alet ile ölçülür?', 'Deprem hangi alet ile ölçülür?', 'Termometre', 'Barometre', 'Sismograf', 'Higrومetre', 'Manyetometre', 'C', NULL, 10],
        ['klasik', 41, 'Besin zinciri nedir? Bir örnek vererek açıklayınız.', 'Besin zinciri nedir? Bir örnek vererek açıklayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Enerji üreticiden tüketiciye aktarılır. Örnek: Ot → Tavşan → Tilki → Bakteri', 10],
        ['test', 43, 'Güneş sistemi kaç gezegenden oluşur?', 'Güneş sistemi kaç gezegenden oluşur?', '6', '7', '8', '9', '10', 'C', NULL, 15],
        ['klasik', 41, 'Elektrik devresinde seri ve paralel bağlamayı açıklayınız.', 'Elektrik devresinde seri ve paralel bağlamayı açıklayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Seri: tek yol, akım sabit. Paralel: çok yol, gerilim sabit.', 20],
        ['klasik', 44, 'Fotosentez olayını kendi cümlelerinizle açıklayınız.', 'Fotosentez olayını kendi cümlelerinizle açıklayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Bitkiler güneş enerjisini kullanarak CO₂ ve suyu glikoz ve O₂\'ye dönüştürür.', 5],
        ['test', 43, 'Newton\'un kuvvet birimi hangisidir?', 'Newton\'un kuvvet birimi hangisidir?', 'Joule', 'Watt', 'Newton', 'Pascal', 'Ohm', 'C', NULL, 20],
        ['klasik', 44, 'Yer çekimi kuvvetini açıklayınız.', 'Yer çekimi kuvvetini açıklayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Kütleli cisimler birbirini çeker. Dünya yüzeyi 9,8 m/s² ivme uygular.', 20],
        ['test', 47, 'Ses hangi ortamda yayılmaz?', 'Ses hangi ortamda yayılmaz?', 'Katı', 'Sıvı', 'Gaz', 'Vakum', 'Su', 'D', NULL, 10],
        ['test', 49, 'Fotosentez nerede gerçekleşir?', 'Fotosentez nerede gerçekleşir?', 'Mitokondri', 'Çekirdek', 'Kloroplast', 'Ribozom', 'Hücre zarı', 'C', NULL, 10],
        ['test', 49, 'Beyin hangi sisteme aittir?', 'Beyin hangi sisteme aittir?', 'Sindirim', 'Dolaşım', 'Sinir', 'Boşaltım', 'Solunum', 'C', NULL, 15],
        ['klasik', 51, 'Maddenin üç halini karşılaştırınız.', 'Maddenin üç halini karşılaştırınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Katı: sabit şekil/hacim. Sıvı: sabit hacim, değişken şekil. Gaz: değişken şekil/hacim.', 10],
        ['test', 49, 'Işığın havadaki hızı yaklaşık kaç km/s?', 'Işığın havadaki hızı yaklaşık kaç km/s?', '100.000', '200.000', '300.000', '400.000', '150.000', 'C', NULL, 10],
        ['test', 52, 'Omurgasız bir hayvan hangisidir?', 'Omurgasız bir hayvan hangisidir?', 'Balık', 'Kurbağa', 'Ahtapot', 'Yılan', 'Kuş', 'C', NULL, 5],
        ['test', 52, 'Isıyı iyi ileten madde hangisidir?', 'Isıyı iyi ileten madde hangisidir?', 'Tahta', 'Plastik', 'Cam', 'Demir', 'Kauçuk', 'D', NULL, 20],
        ['test', 55, 'Hücrenin kontrol merkezi hangisidir?', 'Hücrenin kontrol merkezi hangisidir?', 'Mitokondri', 'Çekirdek', 'Hücre zarı', 'Kloroplast', 'Ribozom', 'B', NULL, 10],
        ['test', 53, 'En hafif element hangisidir?', 'En hafif element hangisidir?', 'Helyum', 'Hidrojen', 'Lityum', 'Oksijen', 'Karbon', 'B', NULL, 10],
        ['test', 56, 'Aşağıdaki sözcüklerden hangisi sıfattır?', 'Aşağıdaki sözcüklerden hangisi sıfattır?', 'Kitap', 'Ev', 'Güzel', 'Çalışmak', 'Ben', 'C', NULL, 10],
        ['klasik', 55, 'Besin zinciri nedir? Bir örnek vererek açıklayınız.', 'Besin zinciri nedir? Bir örnek vererek açıklayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Enerji üreticiden tüketiciye aktarılır. Örnek: Ot → Tavşan → Tilki → Bakteri', 15],
        ['test', 59, 'Anlatıcı bakış açısı kaç türdür?', 'Anlatıcı bakış açısı kaç türdür?', '2', '3', '4', '5', '6', 'C', NULL, 10],
        ['klasik', 59, 'Aşağıdaki cümleleri olumludan olumsuza çeviriniz.', 'Aşağıdaki cümleleri olumludan olumsuza çeviriniz.', NULL, NULL, NULL, NULL, NULL, NULL, 'Fiil olumsuzluk eki (-me/-ma) eklenerek yapılır.', 10],
        ['klasik', 61, 'Verilen metindeki sıfatları bulunuz ve türlerini belirtiniz.', 'Verilen metindeki sıfatları bulunuz ve türlerini belirtiniz.', NULL, NULL, NULL, NULL, NULL, NULL, 'Niteleme sıfatı: güzel. Sayı sıfatı: üç. Belgisiz sıfat: bazı.', 10],
        ['test', 61, 'Türkçede kaç sesli harf vardır?', 'Türkçede kaç sesli harf vardır?', '5', '6', '7', '8', '9', 'D', NULL, 10],
        ['klasik', 63, 'Aşağıdaki sözcükleri alfabetik sıraya koyunuz.', 'Aşağıdaki sözcükleri alfabetik sıraya koyunuz.', NULL, NULL, NULL, NULL, NULL, NULL, 'Türkçe alfabesine göre a,b,c,ç,d... sırası izlenir.', 10],
        ['test', 61, 'Hangi sözcük zarf değildir?', 'Hangi sözcük zarf değildir?', 'Hızlı', 'Çabuk', 'Çok', 'Kitap', 'Yavaş', 'D', NULL, 20],
        ['klasik', 63, 'Paragraftaki bağlaçları ve görevlerini yazınız.', 'Paragraftaki bağlaçları ve görevlerini yazınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Ve (ekleme), ama (karşıtlık), çünkü (neden-sonuç).', 10],
        ['test', 63, 'Cümle sonu hangi noktalama işareti kullanılır?', 'Cümle sonu hangi noktalama işareti kullanılır?', 'Virgül', 'Noktalı virgül', 'Nokta', 'Tire', 'İki nokta', 'C', NULL, 5],
        ['klasik', 65, '\'Sevgi\' temalı 5-6 cümlelik kısa bir metin yazınız.', '\'Sevgi\' temalı 5-6 cümlelik kısa bir metin yazınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Öğrenci özgün metin üretmelidir.', 5],
        ['klasik', 68, 'Aşağıdaki sözcükleri alfabetik sıraya koyunuz.', 'Aşağıdaki sözcükleri alfabetik sıraya koyunuz.', NULL, NULL, NULL, NULL, NULL, NULL, 'Türkçe alfabesine göre a,b,c,ç,d... sırası izlenir.', 20],
        ['test', 67, 'Aşağıdaki sözcüklerden hangisi isimdir?', 'Aşağıdaki sözcüklerden hangisi isimdir?', 'Güzel', 'Koşmak', 'Kitap', 'Hızlı', 'Çabuk', 'C', NULL, 20],
        ['test', 69, '\'Çalışkan\' sözcüğü hangi türden sıfattır?', '\'Çalışkan\' sözcüğü hangi türden sıfattır?', 'Niteleme', 'Belgisiz', 'İşaret', 'Soru', 'Sayı', 'A', NULL, 10],
        ['klasik', 71, 'Aşağıdaki cümleleri olumludan olumsuza çeviriniz.', 'Aşağıdaki cümleleri olumludan olumsuza çeviriniz.', NULL, NULL, NULL, NULL, NULL, NULL, 'Fiil olumsuzluk eki (-me/-ma) eklenerek yapılır.', 10],
        ['test', 70, 'Aşağıdaki sözcüklerden hangisi isimdir?', 'Aşağıdaki sözcüklerden hangisi isimdir?', 'Güzel', 'Koşmak', 'Kitap', 'Hızlı', 'Çabuk', 'C', NULL, 10],
        ['test', 73, '\'Koşmak\' fiilinin emir kipi hangisidir?', '\'Koşmak\' fiilinin emir kipi hangisidir?', 'Koşar', 'Koşacak', 'Koş', 'Koştu', 'Koşuyor', 'C', NULL, 15],
        ['klasik', 72, 'Verilen metindeki sıfatları bulunuz ve türlerini belirtiniz.', 'Verilen metindeki sıfatları bulunuz ve türlerini belirtiniz.', NULL, NULL, NULL, NULL, NULL, NULL, 'Niteleme sıfatı: güzel. Sayı sıfatı: üç. Belgisiz sıfat: bazı.', 10],
        ['test', 75, 'Dünya\'nın kaç tane okyanusu vardır?', 'Dünya\'nın kaç tane okyanusu vardır?', '3', '4', '5', '6', '7', 'C', NULL, 15],
        ['klasik', 75, 'Türkiye\'deki iklim çeşitliliğinin nedenlerini açıklayınız.', 'Türkiye\'deki iklim çeşitliliğinin nedenlerini açıklayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Enlem, yükselti, denizden uzaklık ve dağların uzanışı belirleyicidir.', 10],
    ];
    foreach ($qBatch as $q) {
        $qid = ins($db, "INSERT INTO `questions` (`type`,`user_id`,`unit_id`,`title`,`content`,`option_a`,`option_b`,`option_c`,`option_d`,`option_e`,`correct_opt`,`answer`,`points`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)",
            [$q[0], $uid, $unitIds[$q[1]], $q[2], $q[3], $q[4], $q[5], $q[6], $q[7], $q[8], $q[9], $q[10], $q[11]]);
        $questionIds[$qIdx] = ['id'=>$qid, 'ui'=>$q[1], 'type'=>$q[0]];
        $qIdx++;
    }
    // Chunk 7/20
    $qBatch = [
        ['klasik', 76, 'Cumhuriyetin ilanının önemini yazınız.', 'Cumhuriyetin ilanının önemini yazınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Milli egemenlik ilkesi yerleşti; halkın yönetime katılımı sağlandı.', 15],
        ['klasik', 78, 'Türkiye\'deki iklim çeşitliliğinin nedenlerini açıklayınız.', 'Türkiye\'deki iklim çeşitliliğinin nedenlerini açıklayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Enlem, yükselti, denizden uzaklık ve dağların uzanışı belirleyicidir.', 10],
        ['test', 79, 'Dünya\'nın kaç tane okyanusu vardır?', 'Dünya\'nın kaç tane okyanusu vardır?', '3', '4', '5', '6', '7', 'C', NULL, 15],
        ['test', 80, 'Dünya\'nın en büyük kıtası hangisidir?', 'Dünya\'nın en büyük kıtası hangisidir?', 'Afrika', 'Amerika', 'Avrupa', 'Asya', 'Avustralya', 'D', NULL, 10],
        ['klasik', 79, 'Atatürk\'ün gerçekleştirdiği inkılapları sıralayınız.', 'Atatürk\'ün gerçekleştirdiği inkılapları sıralayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Harf, takvim, kıyafet, hukuk, eğitim, kadın hakları inkılapları.', 10],
        ['test', 82, 'Hangi şehir iki kıta üzerindedir?', 'Hangi şehir iki kıta üzerindedir?', 'Ankara', 'İzmir', 'İstanbul', 'Bursa', 'Antalya', 'C', NULL, 10],
        ['test', 80, 'Hangi şehir iki kıta üzerindedir?', 'Hangi şehir iki kıta üzerindedir?', 'Ankara', 'İzmir', 'İstanbul', 'Bursa', 'Antalya', 'C', NULL, 10],
        ['test', 84, 'Hangi şehir iki kıta üzerindedir?', 'Hangi şehir iki kıta üzerindedir?', 'Ankara', 'İzmir', 'İstanbul', 'Bursa', 'Antalya', 'C', NULL, 20],
        ['test', 83, 'Türkiye\'nin başkenti neresidir?', 'Türkiye\'nin başkenti neresidir?', 'İstanbul', 'İzmir', 'Ankara', 'Bursa', 'Antalya', 'C', NULL, 5],
        ['test', 84, 'Türkiye\'nin nüfusu yaklaşık kaçtır?', 'Türkiye\'nin nüfusu yaklaşık kaçtır?', '60 milyon', '70 milyon', '85 milyon', '100 milyon', '50 milyon', 'C', NULL, 15],
        ['klasik', 86, 'Türkiye\'nin komşu ülkelerini ve bu ülkelerle ilişkilerini açıklayınız.', 'Türkiye\'nin komşu ülkelerini ve bu ülkelerle ilişkilerini açıklayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Yunanistan, Bulgaristan, Gürcistan, Ermenistan, İran, Irak, Suriye, Azerbaycan.', 10],
        ['test', 85, 'Osmanlı Devleti hangi yıl kurulmuştur?', 'Osmanlı Devleti hangi yıl kurulmuştur?', '1299', '1400', '1453', '1517', '1683', 'A', NULL, 10],
        ['klasik', 86, 'Osmanlı Devleti\'nin yıkılış nedenlerini açıklayınız.', 'Osmanlı Devleti\'nin yıkılış nedenlerini açıklayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Savaş yenilgileri, azınlık isyanları, ekonomik çöküş, Batı\'nın müdahalesi.', 10],
        ['test', 88, 'Türkiye Cumhuriyeti hangi yıl kurulmuştur?', 'Türkiye Cumhuriyeti hangi yıl kurulmuştur?', '1919', '1920', '1923', '1925', '1938', 'C', NULL, 10],
        ['klasik', 90, 'Osmanlı Devleti\'nin yıkılış nedenlerini açıklayınız.', 'Osmanlı Devleti\'nin yıkılış nedenlerini açıklayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Savaş yenilgileri, azınlık isyanları, ekonomik çöküş, Batı\'nın müdahalesi.', 10],
        ['klasik', 89, 'Türkiye\'deki iklim çeşitliliğinin nedenlerini açıklayınız.', 'Türkiye\'deki iklim çeşitliliğinin nedenlerini açıklayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Enlem, yükselti, denizden uzaklık ve dağların uzanışı belirleyicidir.', 20],
        ['klasik', 93, 'Küresel ısınmanın nedenlerini ve sonuçlarını yazınız.', 'Küresel ısınmanın nedenlerini ve sonuçlarını yazınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Neden: sera gazları. Sonuç: buzul erimesi, deniz yükselmesi, iklim değişikliği.', 20],
        ['klasik', 94, 'Write a short paragraph about your favourite season.', 'Write a short paragraph about your favourite season.', NULL, NULL, NULL, NULL, NULL, NULL, 'My favourite season is ... because ... The weather is ... I enjoy ...', 5],
        ['test', 92, 'Türkiye\'nin başkenti neresidir?', 'Türkiye\'nin başkenti neresidir?', 'İstanbul', 'İzmir', 'Ankara', 'Bursa', 'Antalya', 'C', NULL, 20],
        ['klasik', 95, 'Describe your school in 5 sentences.', 'Describe your school in 5 sentences.', NULL, NULL, NULL, NULL, NULL, NULL, 'My school is... It has... The teachers are... I study... My favourite class is...', 20],
        ['test', 94, 'Which one is a verb?', 'Which one is a verb?', 'Happy', 'Book', 'Run', 'Beautiful', 'Quickly', 'C', NULL, 5],
        ['test', 95, 'What is the plural of \'child\'?', 'What is the plural of \'child\'?', 'Childs', 'Childes', 'Children', 'Childrens', 'Childies', 'C', NULL, 20],
        ['klasik', 98, 'Write a short story about a memorable holiday.', 'Write a short story about a memorable holiday.', NULL, NULL, NULL, NULL, NULL, NULL, 'Last summer I went to... We stayed at... Every day we... It was... I will never forget...', 5],
        ['klasik', 99, 'Write 5 sentences about your daily routine using Present Simple Tense.', 'Write 5 sentences about your daily routine using Present Simple Tense.', NULL, NULL, NULL, NULL, NULL, NULL, 'I wake up at 7. I eat breakfast. I go to school. I study. I sleep at 10.', 10],
        ['test', 100, '\'I ___ a student.\' Fill in the blank.', '\'I ___ a student.\' Fill in the blank.', 'am', 'is', 'are', 'be', 'been', 'A', NULL, 10],
        ['test', 100, '\'We ___ to school yesterday.\' Fill in.', '\'We ___ to school yesterday.\' Fill in.', 'go', 'goes', 'gone', 'went', 'going', 'D', NULL, 15],
        ['test', 101, '\'They ___ playing football.\' Fill in.', '\'They ___ playing football.\' Fill in.', 'is', 'am', 'are', 'was', 'were', 'C', NULL, 5],
        ['klasik', 104, 'Write 5 questions using Wh- question words.', 'Write 5 questions using Wh- question words.', NULL, NULL, NULL, NULL, NULL, NULL, 'What, Where, When, Why, Who ile 5 soru cümlesi yazılmalıdır.', 10],
        ['klasik', 105, 'Write 5 questions using Wh- question words.', 'Write 5 questions using Wh- question words.', NULL, NULL, NULL, NULL, NULL, NULL, 'What, Where, When, Why, Who ile 5 soru cümlesi yazılmalıdır.', 10],
        ['test', 104, 'Opposite of \'hot\' is:', 'Opposite of \'hot\' is:', 'Warm', 'Cold', 'Cool', 'Mild', 'Humid', 'B', NULL, 10],
        ['test', 107, 'What is the plural of \'child\'?', 'What is the plural of \'child\'?', 'Childs', 'Childes', 'Children', 'Childrens', 'Childies', 'C', NULL, 5],
        ['klasik', 108, 'Write 5 questions using Wh- question words.', 'Write 5 questions using Wh- question words.', NULL, NULL, NULL, NULL, NULL, NULL, 'What, Where, When, Why, Who ile 5 soru cümlesi yazılmalıdır.', 20],
        ['test', 107, 'What is the past tense of \'eat\'?', 'What is the past tense of \'eat\'?', 'Eated', 'Ate', 'Eaten', 'Eats', 'Eating', 'B', NULL, 10],
        ['klasik', 109, 'Write 5 questions using Wh- question words.', 'Write 5 questions using Wh- question words.', NULL, NULL, NULL, NULL, NULL, NULL, 'What, Where, When, Why, Who ile 5 soru cümlesi yazılmalıdır.', 10],
        ['test', 110, '\'I ___ a student.\' Fill in the blank.', '\'I ___ a student.\' Fill in the blank.', 'am', 'is', 'are', 'be', 'been', 'A', NULL, 10],
        ['klasik', 110, 'Describe your school in 5 sentences.', 'Describe your school in 5 sentences.', NULL, NULL, NULL, NULL, NULL, NULL, 'My school is... It has... The teachers are... I study... My favourite class is...', 10],
        ['test', 110, 'What is the past tense of \'go\'?', 'What is the past tense of \'go\'?', 'Goed', 'Goes', 'Going', 'Gone', 'Went', 'E', NULL, 15],
        ['test', 0, '0,25\'in kesir karşılığı hangisidir?', '0,25\'in kesir karşılığı hangisidir?', '1/2', '1/4', '1/3', '2/5', '3/4', 'B', NULL, 20],
        ['test', 1, '100\'ün %30\'u kaçtır?', '100\'ün %30\'u kaçtır?', '10', '20', '30', '40', '50', 'C', NULL, 20],
        ['test', 3, 'Bir üçgenin iç açıları toplamı kaç derecedir?', 'Bir üçgenin iç açıları toplamı kaç derecedir?', '90', '120', '180', '270', '360', 'C', NULL, 15],
        ['klasik', 1, '3x + 7 = 22 denklemini çözünüz.', '3x + 7 = 22 denklemini çözünüz.', NULL, NULL, NULL, NULL, NULL, NULL, '3x = 15 → x = 5', 20],
        ['klasik', 5, 'Bir çemberin çapı 14 cm ise çevresi kaçtır? (π=3,14)', 'Bir çemberin çapı 14 cm ise çevresi kaçtır? (π=3,14)', NULL, NULL, NULL, NULL, NULL, NULL, 'Çevre = π × d = 3,14 × 14 = 43,96 cm', 10],
        ['klasik', 4, '1\'den 10\'a kadar olan tam sayıların toplamını hesaplayınız.', '1\'den 10\'a kadar olan tam sayıların toplamını hesaplayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Toplam = 10×11/2 = 55', 10],
        ['klasik', 5, 'Bir dikdörtgenin uzun kenarı 12 cm, kısa kenarı 8 cm\'dir. Çevresini ve alanını hesaplayınız.', 'Bir dikdörtgenin uzun kenarı 12 cm, kısa kenarı 8 cm\'dir. Çevresini ve alanını hesaplayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Çevre = 2×(12+8) = 40 cm. Alan = 12×8 = 96 cm²', 15],
        ['klasik', 7, 'Kapalı bir kutunun kenarları 3, 4 ve 5 cm\'dir. Hacmini bulunuz.', 'Kapalı bir kutunun kenarları 3, 4 ve 5 cm\'dir. Hacmini bulunuz.', NULL, NULL, NULL, NULL, NULL, NULL, 'Hacim = 3 × 4 × 5 = 60 cm³', 20],
        ['klasik', 7, 'Kapalı bir kutunun kenarları 3, 4 ve 5 cm\'dir. Hacmini bulunuz.', 'Kapalı bir kutunun kenarları 3, 4 ve 5 cm\'dir. Hacmini bulunuz.', NULL, NULL, NULL, NULL, NULL, NULL, 'Hacim = 3 × 4 × 5 = 60 cm³', 20],
        ['test', 7, 'Bir karenin çevresi 20 cm ise alanı kaçtır?', 'Bir karenin çevresi 20 cm ise alanı kaçtır?', '5', '10', '25', '20', '50', 'C', NULL, 10],
        ['test', 8, '3/4 + 1/4 işleminin sonucu nedir?', '3/4 + 1/4 işleminin sonucu nedir?', '1/2', '3/8', '1', '2', '4', 'C', NULL, 20],
        ['test', 10, '36\'nın karekökü kaçtır?', '36\'nın karekökü kaçtır?', '4', '5', '6', '7', '8', 'C', NULL, 10],
        ['klasik', 12, '3x + 7 = 22 denklemini çözünüz.', '3x + 7 = 22 denklemini çözünüz.', NULL, NULL, NULL, NULL, NULL, NULL, '3x = 15 → x = 5', 10],
    ];
    foreach ($qBatch as $q) {
        $qid = ins($db, "INSERT INTO `questions` (`type`,`user_id`,`unit_id`,`title`,`content`,`option_a`,`option_b`,`option_c`,`option_d`,`option_e`,`correct_opt`,`answer`,`points`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)",
            [$q[0], $uid, $unitIds[$q[1]], $q[2], $q[3], $q[4], $q[5], $q[6], $q[7], $q[8], $q[9], $q[10], $q[11]]);
        $questionIds[$qIdx] = ['id'=>$qid, 'ui'=>$q[1], 'type'=>$q[0]];
        $qIdx++;
    }
    // Chunk 8/20
    $qBatch = [
        ['test', 12, '0,5 + 0,75 kaçtır?', '0,5 + 0,75 kaçtır?', '1', '1,25', '1,5', '0,75', '2', 'B', NULL, 15],
        ['test', 14, 'En büyük iki basamaklı sayı hangisidir?', 'En büyük iki basamaklı sayı hangisidir?', '89', '90', '98', '99', '100', 'D', NULL, 10],
        ['test', 14, '0,25\'in kesir karşılığı hangisidir?', '0,25\'in kesir karşılığı hangisidir?', '1/2', '1/4', '1/3', '2/5', '3/4', 'B', NULL, 10],
        ['klasik', 16, '5 kişilik grubun not ortalaması 72\'dir. Toplamları kaçtır?', '5 kişilik grubun not ortalaması 72\'dir. Toplamları kaçtır?', NULL, NULL, NULL, NULL, NULL, NULL, '5 × 72 = 360', 10],
        ['test', 15, '0,5 + 0,75 kaçtır?', '0,5 + 0,75 kaçtır?', '1', '1,25', '1,5', '0,75', '2', 'B', NULL, 5],
        ['klasik', 17, 'Bir dikdörtgenin uzun kenarı 12 cm, kısa kenarı 8 cm\'dir. Çevresini ve alanını hesaplayınız.', 'Bir dikdörtgenin uzun kenarı 12 cm, kısa kenarı 8 cm\'dir. Çevresini ve alanını hesaplayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Çevre = 2×(12+8) = 40 cm. Alan = 12×8 = 96 cm²', 10],
        ['klasik', 20, '24 ile 36\'nın EBOB ve EKOK\'unu bulunuz.', '24 ile 36\'nın EBOB ve EKOK\'unu bulunuz.', NULL, NULL, NULL, NULL, NULL, NULL, 'EBOB = 12, EKOK = 72', 15],
        ['test', 20, 'Bir üçgenin iç açıları toplamı kaç derecedir?', 'Bir üçgenin iç açıları toplamı kaç derecedir?', '90', '120', '180', '270', '360', 'C', NULL, 10],
        ['test', 19, 'En büyük iki basamaklı sayı hangisidir?', 'En büyük iki basamaklı sayı hangisidir?', '89', '90', '98', '99', '100', 'D', NULL, 5],
        ['test', 23, '100\'ün %30\'u kaçtır?', '100\'ün %30\'u kaçtır?', '10', '20', '30', '40', '50', 'C', NULL, 20],
        ['test', 23, 'En büyük iki basamaklı sayı hangisidir?', 'En büyük iki basamaklı sayı hangisidir?', '89', '90', '98', '99', '100', 'D', NULL, 20],
        ['test', 24, '3/4 + 1/4 işleminin sonucu nedir?', '3/4 + 1/4 işleminin sonucu nedir?', '1/2', '3/8', '1', '2', '4', 'C', NULL, 10],
        ['test', 26, '36\'nın karekökü kaçtır?', '36\'nın karekökü kaçtır?', '4', '5', '6', '7', '8', 'C', NULL, 15],
        ['klasik', 26, '5 kişilik grubun not ortalaması 72\'dir. Toplamları kaçtır?', '5 kişilik grubun not ortalaması 72\'dir. Toplamları kaçtır?', NULL, NULL, NULL, NULL, NULL, NULL, '5 × 72 = 360', 10],
        ['klasik', 25, 'Bir sayının %25\'i 15 ise, bu sayı kaçtır?', 'Bir sayının %25\'i 15 ise, bu sayı kaçtır?', NULL, NULL, NULL, NULL, NULL, NULL, 'x × 0,25 = 15 → x = 60', 10],
        ['klasik', 28, '5 kişilik grubun not ortalaması 72\'dir. Toplamları kaçtır?', '5 kişilik grubun not ortalaması 72\'dir. Toplamları kaçtır?', NULL, NULL, NULL, NULL, NULL, NULL, '5 × 72 = 360', 5],
        ['test', 28, 'En küçük asal sayı hangisidir?', 'En küçük asal sayı hangisidir?', '0', '1', '2', '3', '4', 'C', NULL, 5],
        ['klasik', 30, 'Bir dikdörtgenin uzun kenarı 12 cm, kısa kenarı 8 cm\'dir. Çevresini ve alanını hesaplayınız.', 'Bir dikdörtgenin uzun kenarı 12 cm, kısa kenarı 8 cm\'dir. Çevresini ve alanını hesaplayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Çevre = 2×(12+8) = 40 cm. Alan = 12×8 = 96 cm²', 5],
        ['test', 29, 'En büyük iki basamaklı sayı hangisidir?', 'En büyük iki basamaklı sayı hangisidir?', '89', '90', '98', '99', '100', 'D', NULL, 15],
        ['test', 31, 'En küçük asal sayı hangisidir?', 'En küçük asal sayı hangisidir?', '0', '1', '2', '3', '4', 'C', NULL, 10],
        ['test', 34, 'Beyin hangi sisteme aittir?', 'Beyin hangi sisteme aittir?', 'Sindirim', 'Dolaşım', 'Sinir', 'Boşaltım', 'Solunum', 'C', NULL, 20],
        ['klasik', 33, 'Yer çekimi kuvvetini açıklayınız.', 'Yer çekimi kuvvetini açıklayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Kütleli cisimler birbirini çeker. Dünya yüzeyi 9,8 m/s² ivme uygular.', 20],
        ['klasik', 33, 'Ses dalgalarının özellikleri nelerdir?', 'Ses dalgalarının özellikleri nelerdir?', NULL, NULL, NULL, NULL, NULL, NULL, 'Ses mekanik dalgadır; ortam gerektirir. Frekans (Hz) ve genlik ile tanımlanır.', 15],
        ['klasik', 37, 'Ekosistemdeki enerji akışını açıklayınız.', 'Ekosistemdeki enerji akışını açıklayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Güneş → Üreticiler → Birincil tüketiciler → İkincil tüketiciler → Ayrıştırıcılar', 10],
        ['klasik', 35, 'Ses dalgalarının özellikleri nelerdir?', 'Ses dalgalarının özellikleri nelerdir?', NULL, NULL, NULL, NULL, NULL, NULL, 'Ses mekanik dalgadır; ortam gerektirir. Frekans (Hz) ve genlik ile tanımlanır.', 10],
        ['test', 39, 'Işığın havadaki hızı yaklaşık kaç km/s?', 'Işığın havadaki hızı yaklaşık kaç km/s?', '100.000', '200.000', '300.000', '400.000', '150.000', 'C', NULL, 10],
        ['test', 39, 'Kan hücresi olmayan kan bileşeni hangisidir?', 'Kan hücresi olmayan kan bileşeni hangisidir?', 'Alyuvar', 'Akyuvar', 'Trombosit', 'Plazma', 'Hemoglobin', 'D', NULL, 20],
        ['test', 40, 'Bitkiler fotosentezde hangi gazı kullanır?', 'Bitkiler fotosentezde hangi gazı kullanır?', 'O2', 'CO2', 'N2', 'H2', 'CH4', 'B', NULL, 20],
        ['test', 41, 'Isıyı iyi ileten madde hangisidir?', 'Isıyı iyi ileten madde hangisidir?', 'Tahta', 'Plastik', 'Cam', 'Demir', 'Kauçuk', 'D', NULL, 10],
        ['klasik', 41, 'Yer çekimi kuvvetini açıklayınız.', 'Yer çekimi kuvvetini açıklayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Kütleli cisimler birbirini çeker. Dünya yüzeyi 9,8 m/s² ivme uygular.', 10],
        ['klasik', 41, 'Bitkisel ve hayvansal hücreler arasındaki farkları yazınız.', 'Bitkisel ve hayvansal hücreler arasındaki farkları yazınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Bitki hücresinde hücre duvarı ve kloroplast bulunur; hayvan hücresinde bunlar yoktur.', 20],
        ['test', 42, 'Suyun kimyasal formülü nedir?', 'Suyun kimyasal formülü nedir?', 'CO2', 'H2O', 'O2', 'NaCl', 'CH4', 'B', NULL, 10],
        ['klasik', 44, 'Sindirim sistemindeki organları sıralayınız.', 'Sindirim sistemindeki organları sıralayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Ağız → Yemek borusu → Mide → İnce bağırsak → Kalın bağırsak → Anüs', 15],
        ['test', 45, 'Hücrenin kontrol merkezi hangisidir?', 'Hücrenin kontrol merkezi hangisidir?', 'Mitokondri', 'Çekirdek', 'Hücre zarı', 'Kloroplast', 'Ribozom', 'B', NULL, 15],
        ['test', 46, 'Işığın havadaki hızı yaklaşık kaç km/s?', 'Işığın havadaki hızı yaklaşık kaç km/s?', '100.000', '200.000', '300.000', '400.000', '150.000', 'C', NULL, 10],
        ['klasik', 48, 'Elektrik devresinde seri ve paralel bağlamayı açıklayınız.', 'Elektrik devresinde seri ve paralel bağlamayı açıklayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Seri: tek yol, akım sabit. Paralel: çok yol, gerilim sabit.', 15],
        ['test', 47, 'Newton\'un kuvvet birimi hangisidir?', 'Newton\'un kuvvet birimi hangisidir?', 'Joule', 'Watt', 'Newton', 'Pascal', 'Ohm', 'C', NULL, 10],
        ['test', 49, 'Hücrenin kontrol merkezi hangisidir?', 'Hücrenin kontrol merkezi hangisidir?', 'Mitokondri', 'Çekirdek', 'Hücre zarı', 'Kloroplast', 'Ribozom', 'B', NULL, 10],
        ['klasik', 49, 'Besin zinciri nedir? Bir örnek vererek açıklayınız.', 'Besin zinciri nedir? Bir örnek vererek açıklayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Enerji üreticiden tüketiciye aktarılır. Örnek: Ot → Tavşan → Tilki → Bakteri', 5],
        ['test', 51, 'Hücrenin kontrol merkezi hangisidir?', 'Hücrenin kontrol merkezi hangisidir?', 'Mitokondri', 'Çekirdek', 'Hücre zarı', 'Kloroplast', 'Ribozom', 'B', NULL, 10],
        ['test', 53, 'Deprem hangi alet ile ölçülür?', 'Deprem hangi alet ile ölçülür?', 'Termometre', 'Barometre', 'Sismograf', 'Higrومetre', 'Manyetometre', 'C', NULL, 10],
        ['test', 52, 'DNA nerede bulunur?', 'DNA nerede bulunur?', 'Ribozom', 'Mitokondri', 'Çekirdek', 'Hücre zarı', 'Sitoplazma', 'C', NULL, 10],
        ['klasik', 53, 'Ekosistemdeki enerji akışını açıklayınız.', 'Ekosistemdeki enerji akışını açıklayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Güneş → Üreticiler → Birincil tüketiciler → İkincil tüketiciler → Ayrıştırıcılar', 10],
        ['klasik', 57, 'Metindeki noktalama hatalarını bulunuz ve düzeltiniz.', 'Metindeki noktalama hatalarını bulunuz ve düzeltiniz.', NULL, NULL, NULL, NULL, NULL, NULL, 'Yanlış noktalama işaretleri tespit edilip düzeltilmelidir.', 15],
        ['test', 55, 'Deprem hangi alet ile ölçülür?', 'Deprem hangi alet ile ölçülür?', 'Termometre', 'Barometre', 'Sismograf', 'Higrومetre', 'Manyetometre', 'C', NULL, 10],
        ['klasik', 56, 'Verilen kelimeleri kullanarak anlamlı bir paragraf yazınız.', 'Verilen kelimeleri kullanarak anlamlı bir paragraf yazınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Öğrenci verilen kelimelerle tutarlı bir paragraf oluşturmalıdır.', 20],
        ['test', 59, 'Cümle sonu hangi noktalama işareti kullanılır?', 'Cümle sonu hangi noktalama işareti kullanılır?', 'Virgül', 'Noktalı virgül', 'Nokta', 'Tire', 'İki nokta', 'C', NULL, 5],
        ['klasik', 58, 'Aşağıdaki cümleleri olumludan olumsuza çeviriniz.', 'Aşağıdaki cümleleri olumludan olumsuza çeviriniz.', NULL, NULL, NULL, NULL, NULL, NULL, 'Fiil olumsuzluk eki (-me/-ma) eklenerek yapılır.', 10],
        ['klasik', 62, '\'Sevgi\' temalı 5-6 cümlelik kısa bir metin yazınız.', '\'Sevgi\' temalı 5-6 cümlelik kısa bir metin yazınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Öğrenci özgün metin üretmelidir.', 10],
        ['test', 63, 'Cümle sonu hangi noktalama işareti kullanılır?', 'Cümle sonu hangi noktalama işareti kullanılır?', 'Virgül', 'Noktalı virgül', 'Nokta', 'Tire', 'İki nokta', 'C', NULL, 5],
    ];
    foreach ($qBatch as $q) {
        $qid = ins($db, "INSERT INTO `questions` (`type`,`user_id`,`unit_id`,`title`,`content`,`option_a`,`option_b`,`option_c`,`option_d`,`option_e`,`correct_opt`,`answer`,`points`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)",
            [$q[0], $uid, $unitIds[$q[1]], $q[2], $q[3], $q[4], $q[5], $q[6], $q[7], $q[8], $q[9], $q[10], $q[11]]);
        $questionIds[$qIdx] = ['id'=>$qid, 'ui'=>$q[1], 'type'=>$q[0]];
        $qIdx++;
    }
    // Chunk 9/20
    $qBatch = [
        ['klasik', 63, 'Metinden çıkarılabilecek ana fikri yazınız.', 'Metinden çıkarılabilecek ana fikri yazınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Öğrenci metni okuyup ana düşünceyi ifade etmelidir.', 5],
        ['test', 63, 'Aşağıdaki sözcüklerden hangisi sıfattır?', 'Aşağıdaki sözcüklerden hangisi sıfattır?', 'Kitap', 'Ev', 'Güzel', 'Çalışmak', 'Ben', 'C', NULL, 20],
        ['test', 65, 'Hangi sözcük zarf değildir?', 'Hangi sözcük zarf değildir?', 'Hızlı', 'Çabuk', 'Çok', 'Kitap', 'Yavaş', 'D', NULL, 15],
        ['test', 66, 'Anlatıcı bakış açısı kaç türdür?', 'Anlatıcı bakış açısı kaç türdür?', '2', '3', '4', '5', '6', 'C', NULL, 15],
        ['test', 68, '\'Çalışkan\' sözcüğü hangi türden sıfattır?', '\'Çalışkan\' sözcüğü hangi türden sıfattır?', 'Niteleme', 'Belgisiz', 'İşaret', 'Soru', 'Sayı', 'A', NULL, 20],
        ['klasik', 66, 'Verilen metindeki sıfatları bulunuz ve türlerini belirtiniz.', 'Verilen metindeki sıfatları bulunuz ve türlerini belirtiniz.', NULL, NULL, NULL, NULL, NULL, NULL, 'Niteleme sıfatı: güzel. Sayı sıfatı: üç. Belgisiz sıfat: bazı.', 20],
        ['test', 68, '\'Çalışkan\' sözcüğü hangi türden sıfattır?', '\'Çalışkan\' sözcüğü hangi türden sıfattır?', 'Niteleme', 'Belgisiz', 'İşaret', 'Soru', 'Sayı', 'A', NULL, 10],
        ['test', 71, 'Cümle sonu hangi noktalama işareti kullanılır?', 'Cümle sonu hangi noktalama işareti kullanılır?', 'Virgül', 'Noktalı virgül', 'Nokta', 'Tire', 'İki nokta', 'C', NULL, 10],
        ['test', 72, 'Aşağıdakilerden hangisi bağlaçtır?', 'Aşağıdakilerden hangisi bağlaçtır?', 'Çok', 'Ve', 'Güzel', 'Hızla', 'Ama', 'B', NULL, 5],
        ['test', 72, 'Anlatıcı bakış açısı kaç türdür?', 'Anlatıcı bakış açısı kaç türdür?', '2', '3', '4', '5', '6', 'C', NULL, 20],
        ['test', 73, '\'Çalışkan\' sözcüğü hangi türden sıfattır?', '\'Çalışkan\' sözcüğü hangi türden sıfattır?', 'Niteleme', 'Belgisiz', 'İşaret', 'Soru', 'Sayı', 'A', NULL, 10],
        ['test', 75, 'Dünya\'nın en büyük kıtası hangisidir?', 'Dünya\'nın en büyük kıtası hangisidir?', 'Afrika', 'Amerika', 'Avrupa', 'Asya', 'Avustralya', 'D', NULL, 5],
        ['test', 73, '\'Gelmek\' fiilinin geniş zamanı hangisidir?', '\'Gelmek\' fiilinin geniş zamanı hangisidir?', 'Geldi', 'Gelecek', 'Gelir', 'Gelsin', 'Gelse', 'C', NULL, 20],
        ['klasik', 76, 'Cumhuriyetin ilanının önemini yazınız.', 'Cumhuriyetin ilanının önemini yazınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Milli egemenlik ilkesi yerleşti; halkın yönetime katılımı sağlandı.', 15],
        ['test', 75, 'En uzun ırmak hangisidir?', 'En uzun ırmak hangisidir?', 'Amazon', 'Nil', 'Mississippi', 'Yangtze', 'Volga', 'B', NULL, 15],
        ['klasik', 78, 'Türkiye\'deki iklim çeşitliliğinin nedenlerini açıklayınız.', 'Türkiye\'deki iklim çeşitliliğinin nedenlerini açıklayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Enlem, yükselti, denizden uzaklık ve dağların uzanışı belirleyicidir.', 10],
        ['klasik', 79, 'Türkiye\'nin komşu ülkelerini ve bu ülkelerle ilişkilerini açıklayınız.', 'Türkiye\'nin komşu ülkelerini ve bu ülkelerle ilişkilerini açıklayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Yunanistan, Bulgaristan, Gürcistan, Ermenistan, İran, Irak, Suriye, Azerbaycan.', 10],
        ['klasik', 78, 'Atatürk\'ün gerçekleştirdiği inkılapları sıralayınız.', 'Atatürk\'ün gerçekleştirdiği inkılapları sıralayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Harf, takvim, kıyafet, hukuk, eğitim, kadın hakları inkılapları.', 15],
        ['test', 81, 'Türkiye\'nin en uzun nehri hangisidir?', 'Türkiye\'nin en uzun nehri hangisidir?', 'Dicle', 'Fırat', 'Kızılırmak', 'Yeşilırmak', 'Sakarya', 'C', NULL, 15],
        ['test', 81, 'Cumhuriyet hangi tarihte ilan edilmiştir?', 'Cumhuriyet hangi tarihte ilan edilmiştir?', '19 Mayıs', '29 Ekim', '30 Ağustos', '23 Nisan', '10 Kasım', 'B', NULL, 10],
        ['klasik', 82, 'Atatürk\'ün gerçekleştirdiği inkılapları sıralayınız.', 'Atatürk\'ün gerçekleştirdiği inkılapları sıralayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Harf, takvim, kıyafet, hukuk, eğitim, kadın hakları inkılapları.', 10],
        ['test', 84, 'Hangi ülke en fazla nüfusa sahiptir?', 'Hangi ülke en fazla nüfusa sahiptir?', 'Hindistan', 'ABD', 'Rusya', 'Çin', 'Brezilya', 'D', NULL, 10],
        ['klasik', 84, 'Türkiye\'nin komşu ülkelerini ve bu ülkelerle ilişkilerini açıklayınız.', 'Türkiye\'nin komşu ülkelerini ve bu ülkelerle ilişkilerini açıklayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Yunanistan, Bulgaristan, Gürcistan, Ermenistan, İran, Irak, Suriye, Azerbaycan.', 15],
        ['klasik', 84, 'Türkiye\'nin coğrafi konumunun avantajlarını yazınız.', 'Türkiye\'nin coğrafi konumunun avantajlarını yazınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Üç tarafı denizle çevrili, ticaret yolları üzerinde, farklı iklimlere sahip.', 20],
        ['test', 87, 'Cumhuriyet hangi tarihte ilan edilmiştir?', 'Cumhuriyet hangi tarihte ilan edilmiştir?', '19 Mayıs', '29 Ekim', '30 Ağustos', '23 Nisan', '10 Kasım', 'B', NULL, 10],
        ['klasik', 88, 'Atatürk\'ün gerçekleştirdiği inkılapları sıralayınız.', 'Atatürk\'ün gerçekleştirdiği inkılapları sıralayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Harf, takvim, kıyafet, hukuk, eğitim, kadın hakları inkılapları.', 10],
        ['test', 87, 'Türkiye\'nin nüfusu yaklaşık kaçtır?', 'Türkiye\'nin nüfusu yaklaşık kaçtır?', '60 milyon', '70 milyon', '85 milyon', '100 milyon', '50 milyon', 'C', NULL, 10],
        ['test', 91, 'Türkiye\'nin nüfusu yaklaşık kaçtır?', 'Türkiye\'nin nüfusu yaklaşık kaçtır?', '60 milyon', '70 milyon', '85 milyon', '100 milyon', '50 milyon', 'C', NULL, 20],
        ['test', 89, 'En uzun ırmak hangisidir?', 'En uzun ırmak hangisidir?', 'Amazon', 'Nil', 'Mississippi', 'Yangtze', 'Volga', 'B', NULL, 15],
        ['test', 91, 'Türkiye\'nin nüfusu yaklaşık kaçtır?', 'Türkiye\'nin nüfusu yaklaşık kaçtır?', '60 milyon', '70 milyon', '85 milyon', '100 milyon', '50 milyon', 'C', NULL, 10],
        ['test', 93, 'Türkiye kaç komşuya sahiptir?', 'Türkiye kaç komşuya sahiptir?', '5', '6', '7', '8', '9', 'D', NULL, 10],
        ['klasik', 94, 'Write a short story about a memorable holiday.', 'Write a short story about a memorable holiday.', NULL, NULL, NULL, NULL, NULL, NULL, 'Last summer I went to... We stayed at... Every day we... It was... I will never forget...', 15],
        ['klasik', 93, 'Tarih öncesi dönemleri özelliklerine göre karşılaştırınız.', 'Tarih öncesi dönemleri özelliklerine göre karşılaştırınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Taş, Maden ve Tunç Çağları; yazı ve metal kullanımına göre ayrılır.', 10],
        ['klasik', 95, 'Write a short letter to a pen pal introducing yourself.', 'Write a short letter to a pen pal introducing yourself.', NULL, NULL, NULL, NULL, NULL, NULL, 'Dear pen pal, My name is... I am ... years old. I live in... I like...', 5],
        ['test', 95, '\'I ___ a student.\' Fill in the blank.', '\'I ___ a student.\' Fill in the blank.', 'am', 'is', 'are', 'be', 'been', 'A', NULL, 20],
        ['test', 99, 'Opposite of \'hot\' is:', 'Opposite of \'hot\' is:', 'Warm', 'Cold', 'Cool', 'Mild', 'Humid', 'B', NULL, 10],
        ['test', 100, 'What is the plural of \'child\'?', 'What is the plural of \'child\'?', 'Childs', 'Childes', 'Children', 'Childrens', 'Childies', 'C', NULL, 15],
        ['test', 98, 'Which one is a verb?', 'Which one is a verb?', 'Happy', 'Book', 'Run', 'Beautiful', 'Quickly', 'C', NULL, 5],
        ['klasik', 101, 'Write 5 sentences about your daily routine using Present Simple Tense.', 'Write 5 sentences about your daily routine using Present Simple Tense.', NULL, NULL, NULL, NULL, NULL, NULL, 'I wake up at 7. I eat breakfast. I go to school. I study. I sleep at 10.', 15],
        ['test', 103, 'What is the past tense of \'go\'?', 'What is the past tense of \'go\'?', 'Goed', 'Goes', 'Going', 'Gone', 'Went', 'E', NULL, 10],
        ['test', 101, '\'They ___ playing football.\' Fill in.', '\'They ___ playing football.\' Fill in.', 'is', 'am', 'are', 'was', 'were', 'C', NULL, 15],
        ['test', 102, 'What is the past tense of \'go\'?', 'What is the past tense of \'go\'?', 'Goed', 'Goes', 'Going', 'Gone', 'Went', 'E', NULL, 10],
        ['klasik', 104, 'Write 5 sentences about your daily routine using Present Simple Tense.', 'Write 5 sentences about your daily routine using Present Simple Tense.', NULL, NULL, NULL, NULL, NULL, NULL, 'I wake up at 7. I eat breakfast. I go to school. I study. I sleep at 10.', 10],
        ['test', 106, 'Which is a greeting?', 'Which is a greeting?', 'Goodbye', 'Thank you', 'Hello', 'Sorry', 'Please', 'C', NULL, 5],
        ['klasik', 108, 'Write a short story about a memorable holiday.', 'Write a short story about a memorable holiday.', NULL, NULL, NULL, NULL, NULL, NULL, 'Last summer I went to... We stayed at... Every day we... It was... I will never forget...', 10],
        ['test', 107, 'What is the plural of \'child\'?', 'What is the plural of \'child\'?', 'Childs', 'Childes', 'Children', 'Childrens', 'Childies', 'C', NULL, 20],
        ['test', 108, 'How many letters are in the English alphabet?', 'How many letters are in the English alphabet?', '24', '25', '26', '27', '28', 'C', NULL, 20],
        ['test', 110, '\'We ___ to school yesterday.\' Fill in.', '\'We ___ to school yesterday.\' Fill in.', 'go', 'goes', 'gone', 'went', 'going', 'D', NULL, 20],
        ['test', 109, '\'They ___ playing football.\' Fill in.', '\'They ___ playing football.\' Fill in.', 'is', 'am', 'are', 'was', 'were', 'C', NULL, 10],
        ['klasik', 110, 'Make 5 sentences using the Past Simple Tense.', 'Make 5 sentences using the Past Simple Tense.', NULL, NULL, NULL, NULL, NULL, NULL, 'Yesterday I went... Last week she visited... They played... He cooked... We watched...', 15],
    ];
    foreach ($qBatch as $q) {
        $qid = ins($db, "INSERT INTO `questions` (`type`,`user_id`,`unit_id`,`title`,`content`,`option_a`,`option_b`,`option_c`,`option_d`,`option_e`,`correct_opt`,`answer`,`points`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)",
            [$q[0], $uid, $unitIds[$q[1]], $q[2], $q[3], $q[4], $q[5], $q[6], $q[7], $q[8], $q[9], $q[10], $q[11]]);
        $questionIds[$qIdx] = ['id'=>$qid, 'ui'=>$q[1], 'type'=>$q[0]];
        $qIdx++;
    }
    // Chunk 10/20
    $qBatch = [
        ['test', 1, 'En küçük asal sayı hangisidir?', 'En küçük asal sayı hangisidir?', '0', '1', '2', '3', '4', 'C', NULL, 20],
        ['test', 2, '100\'ün %30\'u kaçtır?', '100\'ün %30\'u kaçtır?', '10', '20', '30', '40', '50', 'C', NULL, 10],
        ['klasik', 0, 'Bir mağaza %20 indirim uyguluyor. 150 TL\'lik ürünün fiyatı kaç TL olur?', 'Bir mağaza %20 indirim uyguluyor. 150 TL\'lik ürünün fiyatı kaç TL olur?', NULL, NULL, NULL, NULL, NULL, NULL, '150 × 0,80 = 120 TL', 10],
        ['klasik', 4, '1\'den 10\'a kadar olan tam sayıların toplamını hesaplayınız.', '1\'den 10\'a kadar olan tam sayıların toplamını hesaplayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Toplam = 10×11/2 = 55', 10],
        ['test', 2, '0,25\'in kesir karşılığı hangisidir?', '0,25\'in kesir karşılığı hangisidir?', '1/2', '1/4', '1/3', '2/5', '3/4', 'B', NULL, 5],
        ['test', 4, 'En küçük asal sayı hangisidir?', 'En küçük asal sayı hangisidir?', '0', '1', '2', '3', '4', 'C', NULL, 10],
        ['test', 4, 'Bir dikdörtgenin alanı 48 cm², kısa kenarı 6 cm ise uzun kenarı kaçtır?', 'Bir dikdörtgenin alanı 48 cm², kısa kenarı 6 cm ise uzun kenarı kaçtır?', '6', '7', '8', '9', '10', 'C', NULL, 10],
        ['test', 7, 'Bir dikdörtgenin alanı 48 cm², kısa kenarı 6 cm ise uzun kenarı kaçtır?', 'Bir dikdörtgenin alanı 48 cm², kısa kenarı 6 cm ise uzun kenarı kaçtır?', '6', '7', '8', '9', '10', 'C', NULL, 10],
        ['test', 6, '2^5 işleminin sonucu kaçtır?', '2^5 işleminin sonucu kaçtır?', '8', '16', '32', '64', '128', 'C', NULL, 5],
        ['klasik', 8, 'Bir dikdörtgenin uzun kenarı 12 cm, kısa kenarı 8 cm\'dir. Çevresini ve alanını hesaplayınız.', 'Bir dikdörtgenin uzun kenarı 12 cm, kısa kenarı 8 cm\'dir. Çevresini ve alanını hesaplayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Çevre = 2×(12+8) = 40 cm. Alan = 12×8 = 96 cm²', 10],
        ['klasik', 10, 'Bir dikdörtgenin uzun kenarı 12 cm, kısa kenarı 8 cm\'dir. Çevresini ve alanını hesaplayınız.', 'Bir dikdörtgenin uzun kenarı 12 cm, kısa kenarı 8 cm\'dir. Çevresini ve alanını hesaplayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Çevre = 2×(12+8) = 40 cm. Alan = 12×8 = 96 cm²', 20],
        ['test', 10, '100\'ün %30\'u kaçtır?', '100\'ün %30\'u kaçtır?', '10', '20', '30', '40', '50', 'C', NULL, 10],
        ['test', 12, 'Bir karenin çevresi 20 cm ise alanı kaçtır?', 'Bir karenin çevresi 20 cm ise alanı kaçtır?', '5', '10', '25', '20', '50', 'C', NULL, 15],
        ['klasik', 12, '1\'den 10\'a kadar olan tam sayıların toplamını hesaplayınız.', '1\'den 10\'a kadar olan tam sayıların toplamını hesaplayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Toplam = 10×11/2 = 55', 20],
        ['test', 12, 'En küçük asal sayı hangisidir?', 'En küçük asal sayı hangisidir?', '0', '1', '2', '3', '4', 'C', NULL, 20],
        ['test', 16, '15 + 27 işleminin sonucu nedir?', '15 + 27 işleminin sonucu nedir?', '40', '41', '42', '43', '44', 'C', NULL, 15],
        ['klasik', 17, '5 kişilik grubun not ortalaması 72\'dir. Toplamları kaçtır?', '5 kişilik grubun not ortalaması 72\'dir. Toplamları kaçtır?', NULL, NULL, NULL, NULL, NULL, NULL, '5 × 72 = 360', 15],
        ['klasik', 17, 'Bir mağaza %20 indirim uyguluyor. 150 TL\'lik ürünün fiyatı kaç TL olur?', 'Bir mağaza %20 indirim uyguluyor. 150 TL\'lik ürünün fiyatı kaç TL olur?', NULL, NULL, NULL, NULL, NULL, NULL, '150 × 0,80 = 120 TL', 20],
        ['test', 18, '15 + 27 işleminin sonucu nedir?', '15 + 27 işleminin sonucu nedir?', '40', '41', '42', '43', '44', 'C', NULL, 5],
        ['test', 20, '2^5 işleminin sonucu kaçtır?', '2^5 işleminin sonucu kaçtır?', '8', '16', '32', '64', '128', 'C', NULL, 10],
        ['test', 20, '5! (5 faktöriyel) kaçtır?', '5! (5 faktöriyel) kaçtır?', '20', '60', '100', '120', '240', 'D', NULL, 10],
        ['test', 19, '36\'nın karekökü kaçtır?', '36\'nın karekökü kaçtır?', '4', '5', '6', '7', '8', 'C', NULL, 10],
        ['test', 20, 'En büyük iki basamaklı sayı hangisidir?', 'En büyük iki basamaklı sayı hangisidir?', '89', '90', '98', '99', '100', 'D', NULL, 10],
        ['klasik', 21, 'Kapalı bir kutunun kenarları 3, 4 ve 5 cm\'dir. Hacmini bulunuz.', 'Kapalı bir kutunun kenarları 3, 4 ve 5 cm\'dir. Hacmini bulunuz.', NULL, NULL, NULL, NULL, NULL, NULL, 'Hacim = 3 × 4 × 5 = 60 cm³', 5],
        ['test', 25, '15 + 27 işleminin sonucu nedir?', '15 + 27 işleminin sonucu nedir?', '40', '41', '42', '43', '44', 'C', NULL, 15],
        ['test', 23, '2^5 işleminin sonucu kaçtır?', '2^5 işleminin sonucu kaçtır?', '8', '16', '32', '64', '128', 'C', NULL, 15],
        ['test', 25, 'Bir üçgenin iç açıları toplamı kaç derecedir?', 'Bir üçgenin iç açıları toplamı kaç derecedir?', '90', '120', '180', '270', '360', 'C', NULL, 10],
        ['klasik', 26, 'Bir mağaza %20 indirim uyguluyor. 150 TL\'lik ürünün fiyatı kaç TL olur?', 'Bir mağaza %20 indirim uyguluyor. 150 TL\'lik ürünün fiyatı kaç TL olur?', NULL, NULL, NULL, NULL, NULL, NULL, '150 × 0,80 = 120 TL', 10],
        ['klasik', 26, '1\'den 10\'a kadar olan tam sayıların toplamını hesaplayınız.', '1\'den 10\'a kadar olan tam sayıların toplamını hesaplayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Toplam = 10×11/2 = 55', 10],
        ['test', 27, '100\'ün %30\'u kaçtır?', '100\'ün %30\'u kaçtır?', '10', '20', '30', '40', '50', 'C', NULL, 10],
        ['test', 28, '5! (5 faktöriyel) kaçtır?', '5! (5 faktöriyel) kaçtır?', '20', '60', '100', '120', '240', 'D', NULL, 10],
        ['test', 32, 'Omurgasız bir hayvan hangisidir?', 'Omurgasız bir hayvan hangisidir?', 'Balık', 'Kurbağa', 'Ahtapot', 'Yılan', 'Kuş', 'C', NULL, 15],
        ['test', 31, 'En küçük asal sayı hangisidir?', 'En küçük asal sayı hangisidir?', '0', '1', '2', '3', '4', 'C', NULL, 10],
        ['test', 34, 'En hafif element hangisidir?', 'En hafif element hangisidir?', 'Helyum', 'Hidrojen', 'Lityum', 'Oksijen', 'Karbon', 'B', NULL, 10],
        ['klasik', 32, 'Ses dalgalarının özellikleri nelerdir?', 'Ses dalgalarının özellikleri nelerdir?', NULL, NULL, NULL, NULL, NULL, NULL, 'Ses mekanik dalgadır; ortam gerektirir. Frekans (Hz) ve genlik ile tanımlanır.', 20],
        ['test', 35, 'En hafif element hangisidir?', 'En hafif element hangisidir?', 'Helyum', 'Hidrojen', 'Lityum', 'Oksijen', 'Karbon', 'B', NULL, 5],
        ['test', 37, 'Işığın havadaki hızı yaklaşık kaç km/s?', 'Işığın havadaki hızı yaklaşık kaç km/s?', '100.000', '200.000', '300.000', '400.000', '150.000', 'C', NULL, 20],
        ['test', 36, 'Ses hangi ortamda yayılmaz?', 'Ses hangi ortamda yayılmaz?', 'Katı', 'Sıvı', 'Gaz', 'Vakum', 'Su', 'D', NULL, 15],
        ['klasik', 37, 'Ekosistemdeki enerji akışını açıklayınız.', 'Ekosistemdeki enerji akışını açıklayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Güneş → Üreticiler → Birincil tüketiciler → İkincil tüketiciler → Ayrıştırıcılar', 10],
        ['test', 40, 'Beyin hangi sisteme aittir?', 'Beyin hangi sisteme aittir?', 'Sindirim', 'Dolaşım', 'Sinir', 'Boşaltım', 'Solunum', 'C', NULL, 20],
        ['test', 38, 'Kan hücresi olmayan kan bileşeni hangisidir?', 'Kan hücresi olmayan kan bileşeni hangisidir?', 'Alyuvar', 'Akyuvar', 'Trombosit', 'Plazma', 'Hemoglobin', 'D', NULL, 10],
        ['klasik', 41, 'Elektrik devresinde seri ve paralel bağlamayı açıklayınız.', 'Elektrik devresinde seri ve paralel bağlamayı açıklayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Seri: tek yol, akım sabit. Paralel: çok yol, gerilim sabit.', 20],
        ['klasik', 40, 'Yer çekimi kuvvetini açıklayınız.', 'Yer çekimi kuvvetini açıklayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Kütleli cisimler birbirini çeker. Dünya yüzeyi 9,8 m/s² ivme uygular.', 10],
        ['test', 43, 'Newton\'un kuvvet birimi hangisidir?', 'Newton\'un kuvvet birimi hangisidir?', 'Joule', 'Watt', 'Newton', 'Pascal', 'Ohm', 'C', NULL, 10],
        ['klasik', 43, 'Elektrik devresinde seri ve paralel bağlamayı açıklayınız.', 'Elektrik devresinde seri ve paralel bağlamayı açıklayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Seri: tek yol, akım sabit. Paralel: çok yol, gerilim sabit.', 10],
        ['test', 46, 'Güneş sistemi kaç gezegenden oluşur?', 'Güneş sistemi kaç gezegenden oluşur?', '6', '7', '8', '9', '10', 'C', NULL, 10],
        ['test', 45, 'Ses hangi ortamda yayılmaz?', 'Ses hangi ortamda yayılmaz?', 'Katı', 'Sıvı', 'Gaz', 'Vakum', 'Su', 'D', NULL, 5],
        ['klasik', 45, 'Bitkisel ve hayvansal hücreler arasındaki farkları yazınız.', 'Bitkisel ve hayvansal hücreler arasındaki farkları yazınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Bitki hücresinde hücre duvarı ve kloroplast bulunur; hayvan hücresinde bunlar yoktur.', 15],
        ['test', 47, 'Suyun kimyasal formülü nedir?', 'Suyun kimyasal formülü nedir?', 'CO2', 'H2O', 'O2', 'NaCl', 'CH4', 'B', NULL, 10],
        ['test', 48, 'Beyin hangi sisteme aittir?', 'Beyin hangi sisteme aittir?', 'Sindirim', 'Dolaşım', 'Sinir', 'Boşaltım', 'Solunum', 'C', NULL, 20],
    ];
    foreach ($qBatch as $q) {
        $qid = ins($db, "INSERT INTO `questions` (`type`,`user_id`,`unit_id`,`title`,`content`,`option_a`,`option_b`,`option_c`,`option_d`,`option_e`,`correct_opt`,`answer`,`points`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)",
            [$q[0], $uid, $unitIds[$q[1]], $q[2], $q[3], $q[4], $q[5], $q[6], $q[7], $q[8], $q[9], $q[10], $q[11]]);
        $questionIds[$qIdx] = ['id'=>$qid, 'ui'=>$q[1], 'type'=>$q[0]];
        $qIdx++;
    }
    // Chunk 11/20
    $qBatch = [
        ['klasik', 49, 'Besin zinciri nedir? Bir örnek vererek açıklayınız.', 'Besin zinciri nedir? Bir örnek vererek açıklayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Enerji üreticiden tüketiciye aktarılır. Örnek: Ot → Tavşan → Tilki → Bakteri', 5],
        ['test', 52, 'Ses hangi ortamda yayılmaz?', 'Ses hangi ortamda yayılmaz?', 'Katı', 'Sıvı', 'Gaz', 'Vakum', 'Su', 'D', NULL, 20],
        ['klasik', 51, 'Sindirim sistemindeki organları sıralayınız.', 'Sindirim sistemindeki organları sıralayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Ağız → Yemek borusu → Mide → İnce bağırsak → Kalın bağırsak → Anüs', 15],
        ['klasik', 53, 'Fotosentez olayını kendi cümlelerinizle açıklayınız.', 'Fotosentez olayını kendi cümlelerinizle açıklayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Bitkiler güneş enerjisini kullanarak CO₂ ve suyu glikoz ve O₂\'ye dönüştürür.', 10],
        ['test', 53, 'Beyin hangi sisteme aittir?', 'Beyin hangi sisteme aittir?', 'Sindirim', 'Dolaşım', 'Sinir', 'Boşaltım', 'Solunum', 'C', NULL, 10],
        ['klasik', 56, 'Verilen kelimeleri kullanarak anlamlı bir paragraf yazınız.', 'Verilen kelimeleri kullanarak anlamlı bir paragraf yazınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Öğrenci verilen kelimelerle tutarlı bir paragraf oluşturmalıdır.', 10],
        ['klasik', 56, 'Şiirde kullanılan söz sanatlarını bulunuz.', 'Şiirde kullanılan söz sanatlarını bulunuz.', NULL, NULL, NULL, NULL, NULL, NULL, 'Teşbih, istiare, kişileştirme, abartma incelenir.', 15],
        ['test', 58, 'Anlatıcı bakış açısı kaç türdür?', 'Anlatıcı bakış açısı kaç türdür?', '2', '3', '4', '5', '6', 'C', NULL, 10],
        ['test', 59, '\'Çalışkan\' sözcüğü hangi türden sıfattır?', '\'Çalışkan\' sözcüğü hangi türden sıfattır?', 'Niteleme', 'Belgisiz', 'İşaret', 'Soru', 'Sayı', 'A', NULL, 10],
        ['klasik', 58, 'Verilen kelimeleri kullanarak anlamlı bir paragraf yazınız.', 'Verilen kelimeleri kullanarak anlamlı bir paragraf yazınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Öğrenci verilen kelimelerle tutarlı bir paragraf oluşturmalıdır.', 15],
        ['klasik', 59, 'Verilen metindeki sıfatları bulunuz ve türlerini belirtiniz.', 'Verilen metindeki sıfatları bulunuz ve türlerini belirtiniz.', NULL, NULL, NULL, NULL, NULL, NULL, 'Niteleme sıfatı: güzel. Sayı sıfatı: üç. Belgisiz sıfat: bazı.', 5],
        ['klasik', 62, 'Aşağıdaki cümleleri olumludan olumsuza çeviriniz.', 'Aşağıdaki cümleleri olumludan olumsuza çeviriniz.', NULL, NULL, NULL, NULL, NULL, NULL, 'Fiil olumsuzluk eki (-me/-ma) eklenerek yapılır.', 15],
        ['klasik', 60, 'Verilen kelimeleri kullanarak anlamlı bir paragraf yazınız.', 'Verilen kelimeleri kullanarak anlamlı bir paragraf yazınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Öğrenci verilen kelimelerle tutarlı bir paragraf oluşturmalıdır.', 15],
        ['test', 61, '\'Gelmek\' fiilinin geniş zamanı hangisidir?', '\'Gelmek\' fiilinin geniş zamanı hangisidir?', 'Geldi', 'Gelecek', 'Gelir', 'Gelsin', 'Gelse', 'C', NULL, 10],
        ['test', 64, '\'Yazmak\' fiilinin isim hali hangisidir?', '\'Yazmak\' fiilinin isim hali hangisidir?', 'Yazı', 'Yazıcı', 'Yazan', 'Yazarak', 'Yazılı', 'A', NULL, 15],
        ['klasik', 64, 'Verilen kelimeleri kullanarak anlamlı bir paragraf yazınız.', 'Verilen kelimeleri kullanarak anlamlı bir paragraf yazınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Öğrenci verilen kelimelerle tutarlı bir paragraf oluşturmalıdır.', 5],
        ['klasik', 65, 'Metinden çıkarılabilecek ana fikri yazınız.', 'Metinden çıkarılabilecek ana fikri yazınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Öğrenci metni okuyup ana düşünceyi ifade etmelidir.', 10],
        ['test', 65, '\'Okul\' sözcüğü hangi türden isimdir?', '\'Okul\' sözcüğü hangi türden isimdir?', 'Özel', 'Soyut', 'Somut', 'Topluluk', 'Eylem', 'C', NULL, 10],
        ['test', 68, 'Aşağıdakilerden hangisi bağlaçtır?', 'Aşağıdakilerden hangisi bağlaçtır?', 'Çok', 'Ve', 'Güzel', 'Hızla', 'Ama', 'B', NULL, 5],
        ['test', 69, 'Aşağıdaki sözcüklerden hangisi isimdir?', 'Aşağıdaki sözcüklerden hangisi isimdir?', 'Güzel', 'Koşmak', 'Kitap', 'Hızlı', 'Çabuk', 'C', NULL, 10],
        ['klasik', 68, 'Aşağıdaki sözcükleri alfabetik sıraya koyunuz.', 'Aşağıdaki sözcükleri alfabetik sıraya koyunuz.', NULL, NULL, NULL, NULL, NULL, NULL, 'Türkçe alfabesine göre a,b,c,ç,d... sırası izlenir.', 20],
        ['klasik', 71, 'Aşağıdaki cümleleri olumludan olumsuza çeviriniz.', 'Aşağıdaki cümleleri olumludan olumsuza çeviriniz.', NULL, NULL, NULL, NULL, NULL, NULL, 'Fiil olumsuzluk eki (-me/-ma) eklenerek yapılır.', 10],
        ['test', 70, '\'Gelmek\' fiilinin geniş zamanı hangisidir?', '\'Gelmek\' fiilinin geniş zamanı hangisidir?', 'Geldi', 'Gelecek', 'Gelir', 'Gelsin', 'Gelse', 'C', NULL, 10],
        ['klasik', 72, '\'Sevgi\' temalı 5-6 cümlelik kısa bir metin yazınız.', '\'Sevgi\' temalı 5-6 cümlelik kısa bir metin yazınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Öğrenci özgün metin üretmelidir.', 10],
        ['klasik', 75, 'Türkiye\'deki iklim çeşitliliğinin nedenlerini açıklayınız.', 'Türkiye\'deki iklim çeşitliliğinin nedenlerini açıklayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Enlem, yükselti, denizden uzaklık ve dağların uzanışı belirleyicidir.', 5],
        ['test', 73, '\'Gelmek\' fiilinin geniş zamanı hangisidir?', '\'Gelmek\' fiilinin geniş zamanı hangisidir?', 'Geldi', 'Gelecek', 'Gelir', 'Gelsin', 'Gelse', 'C', NULL, 10],
        ['test', 77, 'Atatürk hangi yıl vefat etmiştir?', 'Atatürk hangi yıl vefat etmiştir?', '1932', '1935', '1938', '1940', '1945', 'C', NULL, 10],
        ['test', 76, 'En uzun ırmak hangisidir?', 'En uzun ırmak hangisidir?', 'Amazon', 'Nil', 'Mississippi', 'Yangtze', 'Volga', 'B', NULL, 5],
        ['test', 77, 'Hangi şehir iki kıta üzerindedir?', 'Hangi şehir iki kıta üzerindedir?', 'Ankara', 'İzmir', 'İstanbul', 'Bursa', 'Antalya', 'C', NULL, 15],
        ['test', 80, 'Hangi ülke en fazla nüfusa sahiptir?', 'Hangi ülke en fazla nüfusa sahiptir?', 'Hindistan', 'ABD', 'Rusya', 'Çin', 'Brezilya', 'D', NULL, 10],
        ['klasik', 78, 'Osmanlı Devleti\'nin yıkılış nedenlerini açıklayınız.', 'Osmanlı Devleti\'nin yıkılış nedenlerini açıklayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Savaş yenilgileri, azınlık isyanları, ekonomik çöküş, Batı\'nın müdahalesi.', 15],
        ['klasik', 80, 'Atatürk\'ün gerçekleştirdiği inkılapları sıralayınız.', 'Atatürk\'ün gerçekleştirdiği inkılapları sıralayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Harf, takvim, kıyafet, hukuk, eğitim, kadın hakları inkılapları.', 10],
        ['test', 83, 'Türkiye hangi yarımkürededir?', 'Türkiye hangi yarımkürededir?', 'Güney', 'Kuzey', 'Batı', 'Doğu', 'Hepsi', 'B', NULL, 10],
        ['test', 82, 'Türkiye Cumhuriyeti hangi yıl kurulmuştur?', 'Türkiye Cumhuriyeti hangi yıl kurulmuştur?', '1919', '1920', '1923', '1925', '1938', 'C', NULL, 10],
        ['test', 82, 'Osmanlı Devleti hangi yıl kurulmuştur?', 'Osmanlı Devleti hangi yıl kurulmuştur?', '1299', '1400', '1453', '1517', '1683', 'A', NULL, 10],
        ['klasik', 83, 'Türkiye\'deki iklim çeşitliliğinin nedenlerini açıklayınız.', 'Türkiye\'deki iklim çeşitliliğinin nedenlerini açıklayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Enlem, yükselti, denizden uzaklık ve dağların uzanışı belirleyicidir.', 15],
        ['test', 84, 'Türkiye kaç komşuya sahiptir?', 'Türkiye kaç komşuya sahiptir?', '5', '6', '7', '8', '9', 'D', NULL, 10],
        ['klasik', 85, 'Osmanlı Devleti\'nin yıkılış nedenlerini açıklayınız.', 'Osmanlı Devleti\'nin yıkılış nedenlerini açıklayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Savaş yenilgileri, azınlık isyanları, ekonomik çöküş, Batı\'nın müdahalesi.', 10],
        ['klasik', 88, 'Atatürk\'ün gerçekleştirdiği inkılapları sıralayınız.', 'Atatürk\'ün gerçekleştirdiği inkılapları sıralayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Harf, takvim, kıyafet, hukuk, eğitim, kadın hakları inkılapları.', 10],
        ['test', 87, 'Osmanlı Devleti hangi yıl kurulmuştur?', 'Osmanlı Devleti hangi yıl kurulmuştur?', '1299', '1400', '1453', '1517', '1683', 'A', NULL, 20],
        ['klasik', 88, 'Osmanlı Devleti\'nin yıkılış nedenlerini açıklayınız.', 'Osmanlı Devleti\'nin yıkılış nedenlerini açıklayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Savaş yenilgileri, azınlık isyanları, ekonomik çöküş, Batı\'nın müdahalesi.', 20],
        ['test', 91, 'Cumhuriyet hangi tarihte ilan edilmiştir?', 'Cumhuriyet hangi tarihte ilan edilmiştir?', '19 Mayıs', '29 Ekim', '30 Ağustos', '23 Nisan', '10 Kasım', 'B', NULL, 10],
        ['klasik', 92, 'Türkiye\'nin coğrafi konumunun avantajlarını yazınız.', 'Türkiye\'nin coğrafi konumunun avantajlarını yazınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Üç tarafı denizle çevrili, ticaret yolları üzerinde, farklı iklimlere sahip.', 10],
        ['klasik', 94, 'Write 5 sentences about your daily routine using Present Simple Tense.', 'Write 5 sentences about your daily routine using Present Simple Tense.', NULL, NULL, NULL, NULL, NULL, NULL, 'I wake up at 7. I eat breakfast. I go to school. I study. I sleep at 10.', 10],
        ['klasik', 93, 'Demokrasinin temel ilkelerini açıklayınız.', 'Demokrasinin temel ilkelerini açıklayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Milli egemenlik, temel haklar, hukukun üstünlüğü, çoğulculuk.', 5],
        ['test', 96, 'Opposite of \'hot\' is:', 'Opposite of \'hot\' is:', 'Warm', 'Cold', 'Cool', 'Mild', 'Humid', 'B', NULL, 10],
        ['klasik', 94, 'Write 5 questions using Wh- question words.', 'Write 5 questions using Wh- question words.', NULL, NULL, NULL, NULL, NULL, NULL, 'What, Where, When, Why, Who ile 5 soru cümlesi yazılmalıdır.', 5],
        ['klasik', 97, 'Write a short paragraph about your favourite season.', 'Write a short paragraph about your favourite season.', NULL, NULL, NULL, NULL, NULL, NULL, 'My favourite season is ... because ... The weather is ... I enjoy ...', 15],
        ['klasik', 99, 'Write 5 questions using Wh- question words.', 'Write 5 questions using Wh- question words.', NULL, NULL, NULL, NULL, NULL, NULL, 'What, Where, When, Why, Who ile 5 soru cümlesi yazılmalıdır.', 15],
        ['test', 100, 'Opposite of \'hot\' is:', 'Opposite of \'hot\' is:', 'Warm', 'Cold', 'Cool', 'Mild', 'Humid', 'B', NULL, 20],
    ];
    foreach ($qBatch as $q) {
        $qid = ins($db, "INSERT INTO `questions` (`type`,`user_id`,`unit_id`,`title`,`content`,`option_a`,`option_b`,`option_c`,`option_d`,`option_e`,`correct_opt`,`answer`,`points`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)",
            [$q[0], $uid, $unitIds[$q[1]], $q[2], $q[3], $q[4], $q[5], $q[6], $q[7], $q[8], $q[9], $q[10], $q[11]]);
        $questionIds[$qIdx] = ['id'=>$qid, 'ui'=>$q[1], 'type'=>$q[0]];
        $qIdx++;
    }
    // Chunk 12/20
    $qBatch = [
        ['test', 99, '\'We ___ to school yesterday.\' Fill in.', '\'We ___ to school yesterday.\' Fill in.', 'go', 'goes', 'gone', 'went', 'going', 'D', NULL, 5],
        ['test', 101, '\'Big\' means:', '\'Big\' means:', 'Küçük', 'Güzel', 'Büyük', 'Uzun', 'Kısa', 'C', NULL, 10],
        ['klasik', 103, 'Describe your school in 5 sentences.', 'Describe your school in 5 sentences.', NULL, NULL, NULL, NULL, NULL, NULL, 'My school is... It has... The teachers are... I study... My favourite class is...', 20],
        ['klasik', 104, 'Compare summer and winter using at least 5 adjectives.', 'Compare summer and winter using at least 5 adjectives.', NULL, NULL, NULL, NULL, NULL, NULL, 'Summer: hot, sunny, long, fun, bright. Winter: cold, dark, short, snowy, quiet.', 10],
        ['klasik', 104, 'Write 5 questions using Wh- question words.', 'Write 5 questions using Wh- question words.', NULL, NULL, NULL, NULL, NULL, NULL, 'What, Where, When, Why, Who ile 5 soru cümlesi yazılmalıdır.', 10],
        ['test', 106, 'Which one is a verb?', 'Which one is a verb?', 'Happy', 'Book', 'Run', 'Beautiful', 'Quickly', 'C', NULL, 15],
        ['klasik', 106, 'Write a short story about a memorable holiday.', 'Write a short story about a memorable holiday.', NULL, NULL, NULL, NULL, NULL, NULL, 'Last summer I went to... We stayed at... Every day we... It was... I will never forget...', 20],
        ['test', 108, 'What does \'beautiful\' mean?', 'What does \'beautiful\' mean?', 'Çirkin', 'Küçük', 'Büyük', 'Güzel', 'Uzun', 'D', NULL, 15],
        ['test', 107, 'Which word is an adjective?', 'Which word is an adjective?', 'Run', 'Quickly', 'Happy', 'Eat', 'Slowly', 'C', NULL, 10],
        ['test', 107, '\'They ___ playing football.\' Fill in.', '\'They ___ playing football.\' Fill in.', 'is', 'am', 'are', 'was', 'were', 'C', NULL, 20],
        ['test', 110, 'What is the past tense of \'eat\'?', 'What is the past tense of \'eat\'?', 'Eated', 'Ate', 'Eaten', 'Eats', 'Eating', 'B', NULL, 15],
        ['test', 112, 'What does \'beautiful\' mean?', 'What does \'beautiful\' mean?', 'Çirkin', 'Küçük', 'Büyük', 'Güzel', 'Uzun', 'D', NULL, 10],
        ['test', 112, 'What is the past tense of \'eat\'?', 'What is the past tense of \'eat\'?', 'Eated', 'Ate', 'Eaten', 'Eats', 'Eating', 'B', NULL, 20],
        ['klasik', 0, 'Bir sayının %25\'i 15 ise, bu sayı kaçtır?', 'Bir sayının %25\'i 15 ise, bu sayı kaçtır?', NULL, NULL, NULL, NULL, NULL, NULL, 'x × 0,25 = 15 → x = 60', 10],
        ['klasik', 1, '1\'den 10\'a kadar olan tam sayıların toplamını hesaplayınız.', '1\'den 10\'a kadar olan tam sayıların toplamını hesaplayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Toplam = 10×11/2 = 55', 5],
        ['klasik', 0, 'Bir çemberin çapı 14 cm ise çevresi kaçtır? (π=3,14)', 'Bir çemberin çapı 14 cm ise çevresi kaçtır? (π=3,14)', NULL, NULL, NULL, NULL, NULL, NULL, 'Çevre = π × d = 3,14 × 14 = 43,96 cm', 10],
        ['test', 3, '15 + 27 işleminin sonucu nedir?', '15 + 27 işleminin sonucu nedir?', '40', '41', '42', '43', '44', 'C', NULL, 20],
        ['klasik', 3, 'Bir dikdörtgenin uzun kenarı 12 cm, kısa kenarı 8 cm\'dir. Çevresini ve alanını hesaplayınız.', 'Bir dikdörtgenin uzun kenarı 12 cm, kısa kenarı 8 cm\'dir. Çevresini ve alanını hesaplayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Çevre = 2×(12+8) = 40 cm. Alan = 12×8 = 96 cm²', 10],
        ['test', 6, 'Bir üçgenin iç açıları toplamı kaç derecedir?', 'Bir üçgenin iç açıları toplamı kaç derecedir?', '90', '120', '180', '270', '360', 'C', NULL, 5],
        ['klasik', 5, 'Bir dikdörtgenin uzun kenarı 12 cm, kısa kenarı 8 cm\'dir. Çevresini ve alanını hesaplayınız.', 'Bir dikdörtgenin uzun kenarı 12 cm, kısa kenarı 8 cm\'dir. Çevresini ve alanını hesaplayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Çevre = 2×(12+8) = 40 cm. Alan = 12×8 = 96 cm²', 10],
        ['test', 7, '2^5 işleminin sonucu kaçtır?', '2^5 işleminin sonucu kaçtır?', '8', '16', '32', '64', '128', 'C', NULL, 15],
        ['klasik', 6, '3x + 7 = 22 denklemini çözünüz.', '3x + 7 = 22 denklemini çözünüz.', NULL, NULL, NULL, NULL, NULL, NULL, '3x = 15 → x = 5', 20],
        ['klasik', 10, '24 ile 36\'nın EBOB ve EKOK\'unu bulunuz.', '24 ile 36\'nın EBOB ve EKOK\'unu bulunuz.', NULL, NULL, NULL, NULL, NULL, NULL, 'EBOB = 12, EKOK = 72', 10],
        ['klasik', 11, '24 ile 36\'nın EBOB ve EKOK\'unu bulunuz.', '24 ile 36\'nın EBOB ve EKOK\'unu bulunuz.', NULL, NULL, NULL, NULL, NULL, NULL, 'EBOB = 12, EKOK = 72', 15],
        ['test', 10, '2^5 işleminin sonucu kaçtır?', '2^5 işleminin sonucu kaçtır?', '8', '16', '32', '64', '128', 'C', NULL, 10],
        ['klasik', 12, '1\'den 10\'a kadar olan tam sayıların toplamını hesaplayınız.', '1\'den 10\'a kadar olan tam sayıların toplamını hesaplayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Toplam = 10×11/2 = 55', 10],
        ['test', 11, '0,25\'in kesir karşılığı hangisidir?', '0,25\'in kesir karşılığı hangisidir?', '1/2', '1/4', '1/3', '2/5', '3/4', 'B', NULL, 10],
        ['test', 15, 'En büyük iki basamaklı sayı hangisidir?', 'En büyük iki basamaklı sayı hangisidir?', '89', '90', '98', '99', '100', 'D', NULL, 15],
        ['test', 13, '100\'ün %30\'u kaçtır?', '100\'ün %30\'u kaçtır?', '10', '20', '30', '40', '50', 'C', NULL, 10],
        ['test', 17, '5! (5 faktöriyel) kaçtır?', '5! (5 faktöriyel) kaçtır?', '20', '60', '100', '120', '240', 'D', NULL, 15],
        ['klasik', 17, '24 ile 36\'nın EBOB ve EKOK\'unu bulunuz.', '24 ile 36\'nın EBOB ve EKOK\'unu bulunuz.', NULL, NULL, NULL, NULL, NULL, NULL, 'EBOB = 12, EKOK = 72', 10],
        ['test', 17, '9\'un karekökü kaçtır?', '9\'un karekökü kaçtır?', '2', '3', '4', '6', '81', 'B', NULL, 5],
        ['test', 20, '0,25\'in kesir karşılığı hangisidir?', '0,25\'in kesir karşılığı hangisidir?', '1/2', '1/4', '1/3', '2/5', '3/4', 'B', NULL, 20],
        ['klasik', 21, '24 ile 36\'nın EBOB ve EKOK\'unu bulunuz.', '24 ile 36\'nın EBOB ve EKOK\'unu bulunuz.', NULL, NULL, NULL, NULL, NULL, NULL, 'EBOB = 12, EKOK = 72', 20],
        ['klasik', 21, 'Bir mağaza %20 indirim uyguluyor. 150 TL\'lik ürünün fiyatı kaç TL olur?', 'Bir mağaza %20 indirim uyguluyor. 150 TL\'lik ürünün fiyatı kaç TL olur?', NULL, NULL, NULL, NULL, NULL, NULL, '150 × 0,80 = 120 TL', 20],
        ['test', 20, 'Bir karenin çevresi 20 cm ise alanı kaçtır?', 'Bir karenin çevresi 20 cm ise alanı kaçtır?', '5', '10', '25', '20', '50', 'C', NULL, 10],
        ['klasik', 23, 'Kapalı bir kutunun kenarları 3, 4 ve 5 cm\'dir. Hacmini bulunuz.', 'Kapalı bir kutunun kenarları 3, 4 ve 5 cm\'dir. Hacmini bulunuz.', NULL, NULL, NULL, NULL, NULL, NULL, 'Hacim = 3 × 4 × 5 = 60 cm³', 20],
        ['klasik', 23, 'Bir çemberin çapı 14 cm ise çevresi kaçtır? (π=3,14)', 'Bir çemberin çapı 14 cm ise çevresi kaçtır? (π=3,14)', NULL, NULL, NULL, NULL, NULL, NULL, 'Çevre = π × d = 3,14 × 14 = 43,96 cm', 15],
        ['test', 24, '5! (5 faktöriyel) kaçtır?', '5! (5 faktöriyel) kaçtır?', '20', '60', '100', '120', '240', 'D', NULL, 10],
        ['test', 25, 'Aşağıdakilerden hangisi asal sayıdır?', 'Aşağıdakilerden hangisi asal sayıdır?', '12', '17', '21', '35', '9', 'B', NULL, 10],
        ['test', 25, 'En büyük iki basamaklı sayı hangisidir?', 'En büyük iki basamaklı sayı hangisidir?', '89', '90', '98', '99', '100', 'D', NULL, 15],
        ['test', 28, '36\'nın karekökü kaçtır?', '36\'nın karekökü kaçtır?', '4', '5', '6', '7', '8', 'C', NULL, 15],
        ['klasik', 28, '1\'den 10\'a kadar olan tam sayıların toplamını hesaplayınız.', '1\'den 10\'a kadar olan tam sayıların toplamını hesaplayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Toplam = 10×11/2 = 55', 5],
        ['test', 28, 'Bir karenin çevresi 20 cm ise alanı kaçtır?', 'Bir karenin çevresi 20 cm ise alanı kaçtır?', '5', '10', '25', '20', '50', 'C', NULL, 20],
        ['test', 32, 'Kan hücresi olmayan kan bileşeni hangisidir?', 'Kan hücresi olmayan kan bileşeni hangisidir?', 'Alyuvar', 'Akyuvar', 'Trombosit', 'Plazma', 'Hemoglobin', 'D', NULL, 10],
        ['test', 30, 'En büyük iki basamaklı sayı hangisidir?', 'En büyük iki basamaklı sayı hangisidir?', '89', '90', '98', '99', '100', 'D', NULL, 10],
        ['test', 31, '100\'ün %30\'u kaçtır?', '100\'ün %30\'u kaçtır?', '10', '20', '30', '40', '50', 'C', NULL, 20],
        ['test', 34, 'Hücrenin kontrol merkezi hangisidir?', 'Hücrenin kontrol merkezi hangisidir?', 'Mitokondri', 'Çekirdek', 'Hücre zarı', 'Kloroplast', 'Ribozom', 'B', NULL, 15],
        ['klasik', 34, 'Kuvvet ve hareket arasındaki ilişkiyi örnekle açıklayınız.', 'Kuvvet ve hareket arasındaki ilişkiyi örnekle açıklayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Net kuvvet uygulandığında cisim hareket eder veya hızı değişir. Örnek: itilen araba.', 10],
        ['test', 37, 'Işığın havadaki hızı yaklaşık kaç km/s?', 'Işığın havadaki hızı yaklaşık kaç km/s?', '100.000', '200.000', '300.000', '400.000', '150.000', 'C', NULL, 5],
    ];
    foreach ($qBatch as $q) {
        $qid = ins($db, "INSERT INTO `questions` (`type`,`user_id`,`unit_id`,`title`,`content`,`option_a`,`option_b`,`option_c`,`option_d`,`option_e`,`correct_opt`,`answer`,`points`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)",
            [$q[0], $uid, $unitIds[$q[1]], $q[2], $q[3], $q[4], $q[5], $q[6], $q[7], $q[8], $q[9], $q[10], $q[11]]);
        $questionIds[$qIdx] = ['id'=>$qid, 'ui'=>$q[1], 'type'=>$q[0]];
        $qIdx++;
    }
    // Chunk 13/20
    $qBatch = [
        ['test', 35, 'Newton\'un kuvvet birimi hangisidir?', 'Newton\'un kuvvet birimi hangisidir?', 'Joule', 'Watt', 'Newton', 'Pascal', 'Ohm', 'C', NULL, 10],
        ['test', 37, 'Newton\'un kuvvet birimi hangisidir?', 'Newton\'un kuvvet birimi hangisidir?', 'Joule', 'Watt', 'Newton', 'Pascal', 'Ohm', 'C', NULL, 20],
        ['test', 38, 'Omurgasız bir hayvan hangisidir?', 'Omurgasız bir hayvan hangisidir?', 'Balık', 'Kurbağa', 'Ahtapot', 'Yılan', 'Kuş', 'C', NULL, 15],
        ['klasik', 40, 'Ekosistemdeki enerji akışını açıklayınız.', 'Ekosistemdeki enerji akışını açıklayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Güneş → Üreticiler → Birincil tüketiciler → İkincil tüketiciler → Ayrıştırıcılar', 10],
        ['klasik', 40, 'Ekosistemdeki enerji akışını açıklayınız.', 'Ekosistemdeki enerji akışını açıklayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Güneş → Üreticiler → Birincil tüketiciler → İkincil tüketiciler → Ayrıştırıcılar', 15],
        ['test', 42, 'Suyun kimyasal formülü nedir?', 'Suyun kimyasal formülü nedir?', 'CO2', 'H2O', 'O2', 'NaCl', 'CH4', 'B', NULL, 10],
        ['test', 41, 'En hafif element hangisidir?', 'En hafif element hangisidir?', 'Helyum', 'Hidrojen', 'Lityum', 'Oksijen', 'Karbon', 'B', NULL, 10],
        ['test', 43, 'Suyun kimyasal formülü nedir?', 'Suyun kimyasal formülü nedir?', 'CO2', 'H2O', 'O2', 'NaCl', 'CH4', 'B', NULL, 15],
        ['klasik', 45, 'Besin zinciri nedir? Bir örnek vererek açıklayınız.', 'Besin zinciri nedir? Bir örnek vererek açıklayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Enerji üreticiden tüketiciye aktarılır. Örnek: Ot → Tavşan → Tilki → Bakteri', 15],
        ['klasik', 44, 'Maddenin üç halini karşılaştırınız.', 'Maddenin üç halini karşılaştırınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Katı: sabit şekil/hacim. Sıvı: sabit hacim, değişken şekil. Gaz: değişken şekil/hacim.', 5],
        ['test', 45, 'Fotosentez nerede gerçekleşir?', 'Fotosentez nerede gerçekleşir?', 'Mitokondri', 'Çekirdek', 'Kloroplast', 'Ribozom', 'Hücre zarı', 'C', NULL, 20],
        ['klasik', 48, 'Fotosentez olayını kendi cümlelerinizle açıklayınız.', 'Fotosentez olayını kendi cümlelerinizle açıklayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Bitkiler güneş enerjisini kullanarak CO₂ ve suyu glikoz ve O₂\'ye dönüştürür.', 15],
        ['klasik', 50, 'Bitkisel ve hayvansal hücreler arasındaki farkları yazınız.', 'Bitkisel ve hayvansal hücreler arasındaki farkları yazınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Bitki hücresinde hücre duvarı ve kloroplast bulunur; hayvan hücresinde bunlar yoktur.', 5],
        ['klasik', 51, 'Elektrik devresinde seri ve paralel bağlamayı açıklayınız.', 'Elektrik devresinde seri ve paralel bağlamayı açıklayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Seri: tek yol, akım sabit. Paralel: çok yol, gerilim sabit.', 15],
        ['klasik', 51, 'Ekosistemdeki enerji akışını açıklayınız.', 'Ekosistemdeki enerji akışını açıklayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Güneş → Üreticiler → Birincil tüketiciler → İkincil tüketiciler → Ayrıştırıcılar', 10],
        ['test', 53, 'Güneş sistemi kaç gezegenden oluşur?', 'Güneş sistemi kaç gezegenden oluşur?', '6', '7', '8', '9', '10', 'C', NULL, 20],
        ['test', 54, 'DNA nerede bulunur?', 'DNA nerede bulunur?', 'Ribozom', 'Mitokondri', 'Çekirdek', 'Hücre zarı', 'Sitoplazma', 'C', NULL, 5],
        ['test', 52, 'Ses hangi ortamda yayılmaz?', 'Ses hangi ortamda yayılmaz?', 'Katı', 'Sıvı', 'Gaz', 'Vakum', 'Su', 'D', NULL, 10],
        ['test', 56, '\'Koşmak\' fiilinin emir kipi hangisidir?', '\'Koşmak\' fiilinin emir kipi hangisidir?', 'Koşar', 'Koşacak', 'Koş', 'Koştu', 'Koşuyor', 'C', NULL, 10],
        ['test', 55, 'Işığın havadaki hızı yaklaşık kaç km/s?', 'Işığın havadaki hızı yaklaşık kaç km/s?', '100.000', '200.000', '300.000', '400.000', '150.000', 'C', NULL, 20],
        ['klasik', 57, 'Verilen metindeki sıfatları bulunuz ve türlerini belirtiniz.', 'Verilen metindeki sıfatları bulunuz ve türlerini belirtiniz.', NULL, NULL, NULL, NULL, NULL, NULL, 'Niteleme sıfatı: güzel. Sayı sıfatı: üç. Belgisiz sıfat: bazı.', 10],
        ['test', 59, 'Türkçede kaç sesli harf vardır?', 'Türkçede kaç sesli harf vardır?', '5', '6', '7', '8', '9', 'D', NULL, 10],
        ['test', 57, 'Aşağıdaki sözcüklerden hangisi isimdir?', 'Aşağıdaki sözcüklerden hangisi isimdir?', 'Güzel', 'Koşmak', 'Kitap', 'Hızlı', 'Çabuk', 'C', NULL, 10],
        ['test', 58, 'Aşağıdaki sözcüklerden hangisi sıfattır?', 'Aşağıdaki sözcüklerden hangisi sıfattır?', 'Kitap', 'Ev', 'Güzel', 'Çalışmak', 'Ben', 'C', NULL, 10],
        ['test', 60, 'Anlatıcı bakış açısı kaç türdür?', 'Anlatıcı bakış açısı kaç türdür?', '2', '3', '4', '5', '6', 'C', NULL, 10],
        ['klasik', 63, 'Verilen kelimeleri kullanarak anlamlı bir paragraf yazınız.', 'Verilen kelimeleri kullanarak anlamlı bir paragraf yazınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Öğrenci verilen kelimelerle tutarlı bir paragraf oluşturmalıdır.', 10],
        ['klasik', 62, 'Aşağıdaki sözcükleri alfabetik sıraya koyunuz.', 'Aşağıdaki sözcükleri alfabetik sıraya koyunuz.', NULL, NULL, NULL, NULL, NULL, NULL, 'Türkçe alfabesine göre a,b,c,ç,d... sırası izlenir.', 10],
        ['klasik', 64, 'Metinden çıkarılabilecek ana fikri yazınız.', 'Metinden çıkarılabilecek ana fikri yazınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Öğrenci metni okuyup ana düşünceyi ifade etmelidir.', 20],
        ['test', 65, 'Aşağıdaki sözcüklerden hangisi isimdir?', 'Aşağıdaki sözcüklerden hangisi isimdir?', 'Güzel', 'Koşmak', 'Kitap', 'Hızlı', 'Çabuk', 'C', NULL, 10],
        ['klasik', 67, 'Verilen metindeki sıfatları bulunuz ve türlerini belirtiniz.', 'Verilen metindeki sıfatları bulunuz ve türlerini belirtiniz.', NULL, NULL, NULL, NULL, NULL, NULL, 'Niteleme sıfatı: güzel. Sayı sıfatı: üç. Belgisiz sıfat: bazı.', 10],
        ['klasik', 68, 'Metinden çıkarılabilecek ana fikri yazınız.', 'Metinden çıkarılabilecek ana fikri yazınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Öğrenci metni okuyup ana düşünceyi ifade etmelidir.', 10],
        ['test', 66, '\'Büyük\' sözcüğünün zıt anlamlısı nedir?', '\'Büyük\' sözcüğünün zıt anlamlısı nedir?', 'Ufak', 'Uzun', 'Dar', 'Kalın', 'Ağır', 'A', NULL, 10],
        ['klasik', 70, 'Aşağıdaki fiillerin istek kipini yazınız.', 'Aşağıdaki fiillerin istek kipini yazınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'İstek kipi -e/-a eki ile yapılır: gide, yaza, söyle.', 10],
        ['test', 70, 'Aşağıdaki sözcüklerden hangisi isimdir?', 'Aşağıdaki sözcüklerden hangisi isimdir?', 'Güzel', 'Koşmak', 'Kitap', 'Hızlı', 'Çabuk', 'C', NULL, 10],
        ['test', 72, '\'Ağaç\' sözcüğünde kaç hece vardır?', '\'Ağaç\' sözcüğünde kaç hece vardır?', '1', '2', '3', '4', '5', 'B', NULL, 20],
        ['test', 73, 'Anlatıcı bakış açısı kaç türdür?', 'Anlatıcı bakış açısı kaç türdür?', '2', '3', '4', '5', '6', 'C', NULL, 5],
        ['test', 71, 'Aşağıdaki sözcüklerden hangisi isimdir?', 'Aşağıdaki sözcüklerden hangisi isimdir?', 'Güzel', 'Koşmak', 'Kitap', 'Hızlı', 'Çabuk', 'C', NULL, 10],
        ['klasik', 74, 'Aşağıdaki fiillerin istek kipini yazınız.', 'Aşağıdaki fiillerin istek kipini yazınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'İstek kipi -e/-a eki ile yapılır: gide, yaza, söyle.', 10],
        ['test', 73, '\'Çalışkan\' sözcüğü hangi türden sıfattır?', '\'Çalışkan\' sözcüğü hangi türden sıfattır?', 'Niteleme', 'Belgisiz', 'İşaret', 'Soru', 'Sayı', 'A', NULL, 15],
        ['test', 75, 'Dünya\'nın en büyük kıtası hangisidir?', 'Dünya\'nın en büyük kıtası hangisidir?', 'Afrika', 'Amerika', 'Avrupa', 'Asya', 'Avustralya', 'D', NULL, 10],
        ['test', 75, 'Türkiye hangi yarımkürededir?', 'Türkiye hangi yarımkürededir?', 'Güney', 'Kuzey', 'Batı', 'Doğu', 'Hepsi', 'B', NULL, 10],
        ['klasik', 78, 'Küresel ısınmanın nedenlerini ve sonuçlarını yazınız.', 'Küresel ısınmanın nedenlerini ve sonuçlarını yazınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Neden: sera gazları. Sonuç: buzul erimesi, deniz yükselmesi, iklim değişikliği.', 15],
        ['test', 77, 'Hangi ülke en fazla nüfusa sahiptir?', 'Hangi ülke en fazla nüfusa sahiptir?', 'Hindistan', 'ABD', 'Rusya', 'Çin', 'Brezilya', 'D', NULL, 5],
        ['test', 81, 'Hangi şehir iki kıta üzerindedir?', 'Hangi şehir iki kıta üzerindedir?', 'Ankara', 'İzmir', 'İstanbul', 'Bursa', 'Antalya', 'C', NULL, 10],
        ['klasik', 81, 'İpek Yolu\'nun tarihsel önemini açıklayınız.', 'İpek Yolu\'nun tarihsel önemini açıklayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Doğu-Batı ticareti, kültür ve bilgi alışverişi için köprü görevi gördü.', 10],
        ['test', 83, 'Türkiye\'nin başkenti neresidir?', 'Türkiye\'nin başkenti neresidir?', 'İstanbul', 'İzmir', 'Ankara', 'Bursa', 'Antalya', 'C', NULL, 5],
        ['test', 81, 'Hangi şehir iki kıta üzerindedir?', 'Hangi şehir iki kıta üzerindedir?', 'Ankara', 'İzmir', 'İstanbul', 'Bursa', 'Antalya', 'C', NULL, 10],
        ['test', 85, 'İpek Yolu hangi medeniyetleri birleştirirdi?', 'İpek Yolu hangi medeniyetleri birleştirirdi?', 'Asya-Avrupa', 'Afrika-Asya', 'Amerika-Avrupa', 'Pasifik-Atlantik', 'Avrupa-Avustralya', 'A', NULL, 10],
        ['test', 84, 'Dünya\'nın en büyük kıtası hangisidir?', 'Dünya\'nın en büyük kıtası hangisidir?', 'Afrika', 'Amerika', 'Avrupa', 'Asya', 'Avustralya', 'D', NULL, 15],
        ['test', 86, 'En uzun ırmak hangisidir?', 'En uzun ırmak hangisidir?', 'Amazon', 'Nil', 'Mississippi', 'Yangtze', 'Volga', 'B', NULL, 15],
    ];
    foreach ($qBatch as $q) {
        $qid = ins($db, "INSERT INTO `questions` (`type`,`user_id`,`unit_id`,`title`,`content`,`option_a`,`option_b`,`option_c`,`option_d`,`option_e`,`correct_opt`,`answer`,`points`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)",
            [$q[0], $uid, $unitIds[$q[1]], $q[2], $q[3], $q[4], $q[5], $q[6], $q[7], $q[8], $q[9], $q[10], $q[11]]);
        $questionIds[$qIdx] = ['id'=>$qid, 'ui'=>$q[1], 'type'=>$q[0]];
        $qIdx++;
    }
    // Chunk 14/20
    $qBatch = [
        ['test', 87, 'Atatürk hangi yıl vefat etmiştir?', 'Atatürk hangi yıl vefat etmiştir?', '1932', '1935', '1938', '1940', '1945', 'C', NULL, 15],
        ['klasik', 89, 'Demokrasinin temel ilkelerini açıklayınız.', 'Demokrasinin temel ilkelerini açıklayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Milli egemenlik, temel haklar, hukukun üstünlüğü, çoğulculuk.', 10],
        ['klasik', 90, 'Atatürk\'ün gerçekleştirdiği inkılapları sıralayınız.', 'Atatürk\'ün gerçekleştirdiği inkılapları sıralayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Harf, takvim, kıyafet, hukuk, eğitim, kadın hakları inkılapları.', 10],
        ['klasik', 90, 'Cumhuriyetin ilanının önemini yazınız.', 'Cumhuriyetin ilanının önemini yazınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Milli egemenlik ilkesi yerleşti; halkın yönetime katılımı sağlandı.', 10],
        ['klasik', 91, 'Küresel ısınmanın nedenlerini ve sonuçlarını yazınız.', 'Küresel ısınmanın nedenlerini ve sonuçlarını yazınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Neden: sera gazları. Sonuç: buzul erimesi, deniz yükselmesi, iklim değişikliği.', 20],
        ['test', 90, 'Türkiye Cumhuriyeti hangi yıl kurulmuştur?', 'Türkiye Cumhuriyeti hangi yıl kurulmuştur?', '1919', '1920', '1923', '1925', '1938', 'C', NULL, 10],
        ['klasik', 92, 'İpek Yolu\'nun tarihsel önemini açıklayınız.', 'İpek Yolu\'nun tarihsel önemini açıklayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Doğu-Batı ticareti, kültür ve bilgi alışverişi için köprü görevi gördü.', 15],
        ['test', 95, 'Opposite of \'hot\' is:', 'Opposite of \'hot\' is:', 'Warm', 'Cold', 'Cool', 'Mild', 'Humid', 'B', NULL, 20],
        ['test', 96, 'Opposite of \'hot\' is:', 'Opposite of \'hot\' is:', 'Warm', 'Cold', 'Cool', 'Mild', 'Humid', 'B', NULL, 20],
        ['test', 95, 'Which is correct?', 'Which is correct?', 'She don\'t', 'She doesn\'t', 'She not', 'She isn\'t run', 'She no go', 'B', NULL, 20],
        ['test', 95, 'What is the past tense of \'go\'?', 'What is the past tense of \'go\'?', 'Goed', 'Goes', 'Going', 'Gone', 'Went', 'E', NULL, 10],
        ['test', 98, 'Which word is an adjective?', 'Which word is an adjective?', 'Run', 'Quickly', 'Happy', 'Eat', 'Slowly', 'C', NULL, 10],
        ['test', 98, 'Which is correct?', 'Which is correct?', 'She don\'t', 'She doesn\'t', 'She not', 'She isn\'t run', 'She no go', 'B', NULL, 15],
        ['klasik', 101, 'Write 5 sentences about things you will do next weekend.', 'Write 5 sentences about things you will do next weekend.', NULL, NULL, NULL, NULL, NULL, NULL, 'Next weekend I will... I am going to... We will visit... She will help... They will play...', 20],
        ['klasik', 99, 'Write 5 sentences about your daily routine using Present Simple Tense.', 'Write 5 sentences about your daily routine using Present Simple Tense.', NULL, NULL, NULL, NULL, NULL, NULL, 'I wake up at 7. I eat breakfast. I go to school. I study. I sleep at 10.', 15],
        ['test', 101, '\'I ___ a student.\' Fill in the blank.', '\'I ___ a student.\' Fill in the blank.', 'am', 'is', 'are', 'be', 'been', 'A', NULL, 20],
        ['test', 104, 'What does \'beautiful\' mean?', 'What does \'beautiful\' mean?', 'Çirkin', 'Küçük', 'Büyük', 'Güzel', 'Uzun', 'D', NULL, 10],
        ['test', 103, 'Which sentence is correct?', 'Which sentence is correct?', 'I is happy', 'He are sad', 'She is tired', 'They is late', 'We am here', 'C', NULL, 15],
        ['test', 105, 'What is the past tense of \'eat\'?', 'What is the past tense of \'eat\'?', 'Eated', 'Ate', 'Eaten', 'Eats', 'Eating', 'B', NULL, 20],
        ['klasik', 106, 'Describe your best friend in 4-5 sentences.', 'Describe your best friend in 4-5 sentences.', NULL, NULL, NULL, NULL, NULL, NULL, 'My best friend is ... He/She is ... We like to ... We have been friends since...', 10],
        ['test', 106, 'Which one is a verb?', 'Which one is a verb?', 'Happy', 'Book', 'Run', 'Beautiful', 'Quickly', 'C', NULL, 10],
        ['klasik', 107, 'Describe your school in 5 sentences.', 'Describe your school in 5 sentences.', NULL, NULL, NULL, NULL, NULL, NULL, 'My school is... It has... The teachers are... I study... My favourite class is...', 10],
        ['klasik', 109, 'Compare summer and winter using at least 5 adjectives.', 'Compare summer and winter using at least 5 adjectives.', NULL, NULL, NULL, NULL, NULL, NULL, 'Summer: hot, sunny, long, fun, bright. Winter: cold, dark, short, snowy, quiet.', 20],
        ['test', 111, '\'Big\' means:', '\'Big\' means:', 'Küçük', 'Güzel', 'Büyük', 'Uzun', 'Kısa', 'C', NULL, 10],
        ['test', 111, 'What does \'beautiful\' mean?', 'What does \'beautiful\' mean?', 'Çirkin', 'Küçük', 'Büyük', 'Güzel', 'Uzun', 'D', NULL, 15],
        ['klasik', 0, 'Kapalı bir kutunun kenarları 3, 4 ve 5 cm\'dir. Hacmini bulunuz.', 'Kapalı bir kutunun kenarları 3, 4 ve 5 cm\'dir. Hacmini bulunuz.', NULL, NULL, NULL, NULL, NULL, NULL, 'Hacim = 3 × 4 × 5 = 60 cm³', 10],
        ['klasik', 0, 'Bir sayının %25\'i 15 ise, bu sayı kaçtır?', 'Bir sayının %25\'i 15 ise, bu sayı kaçtır?', NULL, NULL, NULL, NULL, NULL, NULL, 'x × 0,25 = 15 → x = 60', 10],
        ['test', 1, '9\'un karekökü kaçtır?', '9\'un karekökü kaçtır?', '2', '3', '4', '6', '81', 'B', NULL, 20],
        ['test', 0, 'Aşağıdakilerden hangisi asal sayıdır?', 'Aşağıdakilerden hangisi asal sayıdır?', '12', '17', '21', '35', '9', 'B', NULL, 10],
        ['klasik', 3, '2/3 + 3/4 - 1/6 işlemini yapınız.', '2/3 + 3/4 - 1/6 işlemini yapınız.', NULL, NULL, NULL, NULL, NULL, NULL, '8/12 + 9/12 - 2/12 = 15/12 = 5/4 = 1,25', 10],
        ['klasik', 3, '5 kişilik grubun not ortalaması 72\'dir. Toplamları kaçtır?', '5 kişilik grubun not ortalaması 72\'dir. Toplamları kaçtır?', NULL, NULL, NULL, NULL, NULL, NULL, '5 × 72 = 360', 10],
        ['test', 5, 'En büyük iki basamaklı sayı hangisidir?', 'En büyük iki basamaklı sayı hangisidir?', '89', '90', '98', '99', '100', 'D', NULL, 15],
        ['test', 5, '9\'un karekökü kaçtır?', '9\'un karekökü kaçtır?', '2', '3', '4', '6', '81', 'B', NULL, 20],
        ['klasik', 6, 'Bir çemberin çapı 14 cm ise çevresi kaçtır? (π=3,14)', 'Bir çemberin çapı 14 cm ise çevresi kaçtır? (π=3,14)', NULL, NULL, NULL, NULL, NULL, NULL, 'Çevre = π × d = 3,14 × 14 = 43,96 cm', 5],
        ['test', 6, 'Bir karenin çevresi 20 cm ise alanı kaçtır?', 'Bir karenin çevresi 20 cm ise alanı kaçtır?', '5', '10', '25', '20', '50', 'C', NULL, 20],
        ['test', 10, '0,25\'in kesir karşılığı hangisidir?', '0,25\'in kesir karşılığı hangisidir?', '1/2', '1/4', '1/3', '2/5', '3/4', 'B', NULL, 10],
        ['klasik', 8, 'Bir sayının %25\'i 15 ise, bu sayı kaçtır?', 'Bir sayının %25\'i 15 ise, bu sayı kaçtır?', NULL, NULL, NULL, NULL, NULL, NULL, 'x × 0,25 = 15 → x = 60', 10],
        ['klasik', 12, 'Bir çemberin çapı 14 cm ise çevresi kaçtır? (π=3,14)', 'Bir çemberin çapı 14 cm ise çevresi kaçtır? (π=3,14)', NULL, NULL, NULL, NULL, NULL, NULL, 'Çevre = π × d = 3,14 × 14 = 43,96 cm', 10],
        ['klasik', 13, '3x + 7 = 22 denklemini çözünüz.', '3x + 7 = 22 denklemini çözünüz.', NULL, NULL, NULL, NULL, NULL, NULL, '3x = 15 → x = 5', 10],
        ['test', 13, '15 + 27 işleminin sonucu nedir?', '15 + 27 işleminin sonucu nedir?', '40', '41', '42', '43', '44', 'C', NULL, 5],
        ['klasik', 12, '2/3 + 3/4 - 1/6 işlemini yapınız.', '2/3 + 3/4 - 1/6 işlemini yapınız.', NULL, NULL, NULL, NULL, NULL, NULL, '8/12 + 9/12 - 2/12 = 15/12 = 5/4 = 1,25', 10],
        ['klasik', 13, 'Bir dikdörtgenin uzun kenarı 12 cm, kısa kenarı 8 cm\'dir. Çevresini ve alanını hesaplayınız.', 'Bir dikdörtgenin uzun kenarı 12 cm, kısa kenarı 8 cm\'dir. Çevresini ve alanını hesaplayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Çevre = 2×(12+8) = 40 cm. Alan = 12×8 = 96 cm²', 10],
        ['klasik', 17, '5 kişilik grubun not ortalaması 72\'dir. Toplamları kaçtır?', '5 kişilik grubun not ortalaması 72\'dir. Toplamları kaçtır?', NULL, NULL, NULL, NULL, NULL, NULL, '5 × 72 = 360', 10],
        ['klasik', 15, 'Kapalı bir kutunun kenarları 3, 4 ve 5 cm\'dir. Hacmini bulunuz.', 'Kapalı bir kutunun kenarları 3, 4 ve 5 cm\'dir. Hacmini bulunuz.', NULL, NULL, NULL, NULL, NULL, NULL, 'Hacim = 3 × 4 × 5 = 60 cm³', 10],
        ['test', 19, '0,25\'in kesir karşılığı hangisidir?', '0,25\'in kesir karşılığı hangisidir?', '1/2', '1/4', '1/3', '2/5', '3/4', 'B', NULL, 20],
        ['klasik', 19, '5 kişilik grubun not ortalaması 72\'dir. Toplamları kaçtır?', '5 kişilik grubun not ortalaması 72\'dir. Toplamları kaçtır?', NULL, NULL, NULL, NULL, NULL, NULL, '5 × 72 = 360', 5],
        ['test', 20, 'En küçük asal sayı hangisidir?', 'En küçük asal sayı hangisidir?', '0', '1', '2', '3', '4', 'C', NULL, 5],
        ['test', 21, 'Bir karenin çevresi 20 cm ise alanı kaçtır?', 'Bir karenin çevresi 20 cm ise alanı kaçtır?', '5', '10', '25', '20', '50', 'C', NULL, 20],
        ['klasik', 23, 'Kapalı bir kutunun kenarları 3, 4 ve 5 cm\'dir. Hacmini bulunuz.', 'Kapalı bir kutunun kenarları 3, 4 ve 5 cm\'dir. Hacmini bulunuz.', NULL, NULL, NULL, NULL, NULL, NULL, 'Hacim = 3 × 4 × 5 = 60 cm³', 10],
        ['test', 24, '100\'ün %30\'u kaçtır?', '100\'ün %30\'u kaçtır?', '10', '20', '30', '40', '50', 'C', NULL, 5],
    ];
    foreach ($qBatch as $q) {
        $qid = ins($db, "INSERT INTO `questions` (`type`,`user_id`,`unit_id`,`title`,`content`,`option_a`,`option_b`,`option_c`,`option_d`,`option_e`,`correct_opt`,`answer`,`points`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)",
            [$q[0], $uid, $unitIds[$q[1]], $q[2], $q[3], $q[4], $q[5], $q[6], $q[7], $q[8], $q[9], $q[10], $q[11]]);
        $questionIds[$qIdx] = ['id'=>$qid, 'ui'=>$q[1], 'type'=>$q[0]];
        $qIdx++;
    }
    // Chunk 15/20
    $qBatch = [
        ['test', 24, '15 + 27 işleminin sonucu nedir?', '15 + 27 işleminin sonucu nedir?', '40', '41', '42', '43', '44', 'C', NULL, 10],
        ['klasik', 25, 'Bir çemberin çapı 14 cm ise çevresi kaçtır? (π=3,14)', 'Bir çemberin çapı 14 cm ise çevresi kaçtır? (π=3,14)', NULL, NULL, NULL, NULL, NULL, NULL, 'Çevre = π × d = 3,14 × 14 = 43,96 cm', 5],
        ['klasik', 26, '1\'den 10\'a kadar olan tam sayıların toplamını hesaplayınız.', '1\'den 10\'a kadar olan tam sayıların toplamını hesaplayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Toplam = 10×11/2 = 55', 15],
        ['test', 26, '100\'ün %30\'u kaçtır?', '100\'ün %30\'u kaçtır?', '10', '20', '30', '40', '50', 'C', NULL, 20],
        ['klasik', 29, 'Bir sayının %25\'i 15 ise, bu sayı kaçtır?', 'Bir sayının %25\'i 15 ise, bu sayı kaçtır?', NULL, NULL, NULL, NULL, NULL, NULL, 'x × 0,25 = 15 → x = 60', 15],
        ['test', 29, 'En küçük asal sayı hangisidir?', 'En küçük asal sayı hangisidir?', '0', '1', '2', '3', '4', 'C', NULL, 20],
        ['test', 28, '3/4 + 1/4 işleminin sonucu nedir?', '3/4 + 1/4 işleminin sonucu nedir?', '1/2', '3/8', '1', '2', '4', 'C', NULL, 10],
        ['test', 32, 'Ses hangi ortamda yayılmaz?', 'Ses hangi ortamda yayılmaz?', 'Katı', 'Sıvı', 'Gaz', 'Vakum', 'Su', 'D', NULL, 10],
        ['test', 30, 'Aşağıdakilerden hangisi asal sayıdır?', 'Aşağıdakilerden hangisi asal sayıdır?', '12', '17', '21', '35', '9', 'B', NULL, 10],
        ['klasik', 34, 'Bitkisel ve hayvansal hücreler arasındaki farkları yazınız.', 'Bitkisel ve hayvansal hücreler arasındaki farkları yazınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Bitki hücresinde hücre duvarı ve kloroplast bulunur; hayvan hücresinde bunlar yoktur.', 10],
        ['test', 34, 'Fotosentez nerede gerçekleşir?', 'Fotosentez nerede gerçekleşir?', 'Mitokondri', 'Çekirdek', 'Kloroplast', 'Ribozom', 'Hücre zarı', 'C', NULL, 15],
        ['test', 33, 'En hafif element hangisidir?', 'En hafif element hangisidir?', 'Helyum', 'Hidrojen', 'Lityum', 'Oksijen', 'Karbon', 'B', NULL, 10],
        ['test', 35, 'Omurgasız bir hayvan hangisidir?', 'Omurgasız bir hayvan hangisidir?', 'Balık', 'Kurbağa', 'Ahtapot', 'Yılan', 'Kuş', 'C', NULL, 5],
        ['test', 38, 'Newton\'un kuvvet birimi hangisidir?', 'Newton\'un kuvvet birimi hangisidir?', 'Joule', 'Watt', 'Newton', 'Pascal', 'Ohm', 'C', NULL, 10],
        ['klasik', 38, 'Sindirim sistemindeki organları sıralayınız.', 'Sindirim sistemindeki organları sıralayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Ağız → Yemek borusu → Mide → İnce bağırsak → Kalın bağırsak → Anüs', 20],
        ['test', 39, 'Güneş sistemi kaç gezegenden oluşur?', 'Güneş sistemi kaç gezegenden oluşur?', '6', '7', '8', '9', '10', 'C', NULL, 10],
        ['klasik', 39, 'Elektrik devresinde seri ve paralel bağlamayı açıklayınız.', 'Elektrik devresinde seri ve paralel bağlamayı açıklayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Seri: tek yol, akım sabit. Paralel: çok yol, gerilim sabit.', 10],
        ['test', 40, 'Suyun kimyasal formülü nedir?', 'Suyun kimyasal formülü nedir?', 'CO2', 'H2O', 'O2', 'NaCl', 'CH4', 'B', NULL, 15],
        ['test', 42, 'Işığın havadaki hızı yaklaşık kaç km/s?', 'Işığın havadaki hızı yaklaşık kaç km/s?', '100.000', '200.000', '300.000', '400.000', '150.000', 'C', NULL, 15],
        ['klasik', 44, 'Kuvvet ve hareket arasındaki ilişkiyi örnekle açıklayınız.', 'Kuvvet ve hareket arasındaki ilişkiyi örnekle açıklayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Net kuvvet uygulandığında cisim hareket eder veya hızı değişir. Örnek: itilen araba.', 20],
        ['klasik', 43, 'Kuvvet ve hareket arasındaki ilişkiyi örnekle açıklayınız.', 'Kuvvet ve hareket arasındaki ilişkiyi örnekle açıklayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Net kuvvet uygulandığında cisim hareket eder veya hızı değişir. Örnek: itilen araba.', 20],
        ['test', 43, 'Omurgasız bir hayvan hangisidir?', 'Omurgasız bir hayvan hangisidir?', 'Balık', 'Kurbağa', 'Ahtapot', 'Yılan', 'Kuş', 'C', NULL, 15],
        ['test', 46, 'Isıyı iyi ileten madde hangisidir?', 'Isıyı iyi ileten madde hangisidir?', 'Tahta', 'Plastik', 'Cam', 'Demir', 'Kauçuk', 'D', NULL, 15],
        ['test', 45, 'Omurgasız bir hayvan hangisidir?', 'Omurgasız bir hayvan hangisidir?', 'Balık', 'Kurbağa', 'Ahtapot', 'Yılan', 'Kuş', 'C', NULL, 15],
        ['test', 47, 'Isıyı iyi ileten madde hangisidir?', 'Isıyı iyi ileten madde hangisidir?', 'Tahta', 'Plastik', 'Cam', 'Demir', 'Kauçuk', 'D', NULL, 10],
        ['test', 48, 'Işığın havadaki hızı yaklaşık kaç km/s?', 'Işığın havadaki hızı yaklaşık kaç km/s?', '100.000', '200.000', '300.000', '400.000', '150.000', 'C', NULL, 15],
        ['test', 51, 'Güneş sistemi kaç gezegenden oluşur?', 'Güneş sistemi kaç gezegenden oluşur?', '6', '7', '8', '9', '10', 'C', NULL, 10],
        ['test', 52, 'Suyun kimyasal formülü nedir?', 'Suyun kimyasal formülü nedir?', 'CO2', 'H2O', 'O2', 'NaCl', 'CH4', 'B', NULL, 15],
        ['klasik', 53, 'Ekosistemdeki enerji akışını açıklayınız.', 'Ekosistemdeki enerji akışını açıklayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Güneş → Üreticiler → Birincil tüketiciler → İkincil tüketiciler → Ayrıştırıcılar', 10],
        ['test', 52, 'DNA nerede bulunur?', 'DNA nerede bulunur?', 'Ribozom', 'Mitokondri', 'Çekirdek', 'Hücre zarı', 'Sitoplazma', 'C', NULL, 10],
        ['test', 52, 'Kan hücresi olmayan kan bileşeni hangisidir?', 'Kan hücresi olmayan kan bileşeni hangisidir?', 'Alyuvar', 'Akyuvar', 'Trombosit', 'Plazma', 'Hemoglobin', 'D', NULL, 20],
        ['klasik', 55, 'Fotosentez olayını kendi cümlelerinizle açıklayınız.', 'Fotosentez olayını kendi cümlelerinizle açıklayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Bitkiler güneş enerjisini kullanarak CO₂ ve suyu glikoz ve O₂\'ye dönüştürür.', 10],
        ['klasik', 56, 'Verilen kelimeleri kullanarak anlamlı bir paragraf yazınız.', 'Verilen kelimeleri kullanarak anlamlı bir paragraf yazınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Öğrenci verilen kelimelerle tutarlı bir paragraf oluşturmalıdır.', 20],
        ['test', 56, 'Aşağıdakilerden hangisi bağlaçtır?', 'Aşağıdakilerden hangisi bağlaçtır?', 'Çok', 'Ve', 'Güzel', 'Hızla', 'Ama', 'B', NULL, 5],
        ['test', 59, 'Hangi sözcük zarf değildir?', 'Hangi sözcük zarf değildir?', 'Hızlı', 'Çabuk', 'Çok', 'Kitap', 'Yavaş', 'D', NULL, 5],
        ['klasik', 57, 'Şiirde kullanılan söz sanatlarını bulunuz.', 'Şiirde kullanılan söz sanatlarını bulunuz.', NULL, NULL, NULL, NULL, NULL, NULL, 'Teşbih, istiare, kişileştirme, abartma incelenir.', 5],
        ['test', 61, 'Aşağıdaki sözcüklerden hangisi isimdir?', 'Aşağıdaki sözcüklerden hangisi isimdir?', 'Güzel', 'Koşmak', 'Kitap', 'Hızlı', 'Çabuk', 'C', NULL, 10],
        ['klasik', 60, 'Şiirde kullanılan söz sanatlarını bulunuz.', 'Şiirde kullanılan söz sanatlarını bulunuz.', NULL, NULL, NULL, NULL, NULL, NULL, 'Teşbih, istiare, kişileştirme, abartma incelenir.', 20],
        ['test', 63, '\'Büyük\' sözcüğünün zıt anlamlısı nedir?', '\'Büyük\' sözcüğünün zıt anlamlısı nedir?', 'Ufak', 'Uzun', 'Dar', 'Kalın', 'Ağır', 'A', NULL, 10],
        ['klasik', 64, 'Aşağıdaki cümleleri olumludan olumsuza çeviriniz.', 'Aşağıdaki cümleleri olumludan olumsuza çeviriniz.', NULL, NULL, NULL, NULL, NULL, NULL, 'Fiil olumsuzluk eki (-me/-ma) eklenerek yapılır.', 10],
        ['test', 65, 'Aşağıdakilerden hangisi bağlaçtır?', 'Aşağıdakilerden hangisi bağlaçtır?', 'Çok', 'Ve', 'Güzel', 'Hızla', 'Ama', 'B', NULL, 20],
        ['klasik', 64, 'Verilen kelimeleri kullanarak anlamlı bir paragraf yazınız.', 'Verilen kelimeleri kullanarak anlamlı bir paragraf yazınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Öğrenci verilen kelimelerle tutarlı bir paragraf oluşturmalıdır.', 10],
        ['test', 64, 'Aşağıdakilerden hangisi bağlaçtır?', 'Aşağıdakilerden hangisi bağlaçtır?', 'Çok', 'Ve', 'Güzel', 'Hızla', 'Ama', 'B', NULL, 15],
        ['test', 68, '\'Çalışkan\' sözcüğü hangi türden sıfattır?', '\'Çalışkan\' sözcüğü hangi türden sıfattır?', 'Niteleme', 'Belgisiz', 'İşaret', 'Soru', 'Sayı', 'A', NULL, 20],
        ['klasik', 67, '\'Sevgi\' temalı 5-6 cümlelik kısa bir metin yazınız.', '\'Sevgi\' temalı 5-6 cümlelik kısa bir metin yazınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Öğrenci özgün metin üretmelidir.', 5],
        ['test', 69, '\'Yazmak\' fiilinin isim hali hangisidir?', '\'Yazmak\' fiilinin isim hali hangisidir?', 'Yazı', 'Yazıcı', 'Yazan', 'Yazarak', 'Yazılı', 'A', NULL, 20],
        ['test', 71, 'Aşağıdaki sözcüklerden hangisi isimdir?', 'Aşağıdaki sözcüklerden hangisi isimdir?', 'Güzel', 'Koşmak', 'Kitap', 'Hızlı', 'Çabuk', 'C', NULL, 10],
        ['test', 70, 'Aşağıdakilerden hangisi bağlaçtır?', 'Aşağıdakilerden hangisi bağlaçtır?', 'Çok', 'Ve', 'Güzel', 'Hızla', 'Ama', 'B', NULL, 10],
        ['test', 72, '\'Çalışkan\' sözcüğü hangi türden sıfattır?', '\'Çalışkan\' sözcüğü hangi türden sıfattır?', 'Niteleme', 'Belgisiz', 'İşaret', 'Soru', 'Sayı', 'A', NULL, 20],
        ['test', 71, 'Aşağıdaki sözcüklerden hangisi isimdir?', 'Aşağıdaki sözcüklerden hangisi isimdir?', 'Güzel', 'Koşmak', 'Kitap', 'Hızlı', 'Çabuk', 'C', NULL, 20],
    ];
    foreach ($qBatch as $q) {
        $qid = ins($db, "INSERT INTO `questions` (`type`,`user_id`,`unit_id`,`title`,`content`,`option_a`,`option_b`,`option_c`,`option_d`,`option_e`,`correct_opt`,`answer`,`points`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)",
            [$q[0], $uid, $unitIds[$q[1]], $q[2], $q[3], $q[4], $q[5], $q[6], $q[7], $q[8], $q[9], $q[10], $q[11]]);
        $questionIds[$qIdx] = ['id'=>$qid, 'ui'=>$q[1], 'type'=>$q[0]];
        $qIdx++;
    }
    // Chunk 16/20
    $qBatch = [
        ['klasik', 75, 'İpek Yolu\'nun tarihsel önemini açıklayınız.', 'İpek Yolu\'nun tarihsel önemini açıklayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Doğu-Batı ticareti, kültür ve bilgi alışverişi için köprü görevi gördü.', 5],
        ['test', 74, 'Anlatıcı bakış açısı kaç türdür?', 'Anlatıcı bakış açısı kaç türdür?', '2', '3', '4', '5', '6', 'C', NULL, 5],
        ['test', 74, 'Hangi sözcük zarf değildir?', 'Hangi sözcük zarf değildir?', 'Hızlı', 'Çabuk', 'Çok', 'Kitap', 'Yavaş', 'D', NULL, 20],
        ['test', 75, 'Hangi ülke en fazla nüfusa sahiptir?', 'Hangi ülke en fazla nüfusa sahiptir?', 'Hindistan', 'ABD', 'Rusya', 'Çin', 'Brezilya', 'D', NULL, 5],
        ['test', 79, 'Atatürk hangi yıl vefat etmiştir?', 'Atatürk hangi yıl vefat etmiştir?', '1932', '1935', '1938', '1940', '1945', 'C', NULL, 15],
        ['klasik', 78, 'Demokrasinin temel ilkelerini açıklayınız.', 'Demokrasinin temel ilkelerini açıklayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Milli egemenlik, temel haklar, hukukun üstünlüğü, çoğulculuk.', 5],
        ['test', 79, 'Osmanlı Devleti hangi yıl kurulmuştur?', 'Osmanlı Devleti hangi yıl kurulmuştur?', '1299', '1400', '1453', '1517', '1683', 'A', NULL, 10],
        ['klasik', 80, 'Demokrasinin temel ilkelerini açıklayınız.', 'Demokrasinin temel ilkelerini açıklayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Milli egemenlik, temel haklar, hukukun üstünlüğü, çoğulculuk.', 20],
        ['test', 82, 'Hangi şehir iki kıta üzerindedir?', 'Hangi şehir iki kıta üzerindedir?', 'Ankara', 'İzmir', 'İstanbul', 'Bursa', 'Antalya', 'C', NULL, 20],
        ['test', 83, 'Türkiye hangi yarımkürededir?', 'Türkiye hangi yarımkürededir?', 'Güney', 'Kuzey', 'Batı', 'Doğu', 'Hepsi', 'B', NULL, 10],
        ['test', 83, 'Türkiye\'nin nüfusu yaklaşık kaçtır?', 'Türkiye\'nin nüfusu yaklaşık kaçtır?', '60 milyon', '70 milyon', '85 milyon', '100 milyon', '50 milyon', 'C', NULL, 10],
        ['test', 83, 'Türkiye\'nin nüfusu yaklaşık kaçtır?', 'Türkiye\'nin nüfusu yaklaşık kaçtır?', '60 milyon', '70 milyon', '85 milyon', '100 milyon', '50 milyon', 'C', NULL, 15],
        ['klasik', 85, 'Küresel ısınmanın nedenlerini ve sonuçlarını yazınız.', 'Küresel ısınmanın nedenlerini ve sonuçlarını yazınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Neden: sera gazları. Sonuç: buzul erimesi, deniz yükselmesi, iklim değişikliği.', 10],
        ['test', 88, 'Türkiye Cumhuriyeti hangi yıl kurulmuştur?', 'Türkiye Cumhuriyeti hangi yıl kurulmuştur?', '1919', '1920', '1923', '1925', '1938', 'C', NULL, 10],
        ['klasik', 89, 'Cumhuriyetin ilanının önemini yazınız.', 'Cumhuriyetin ilanının önemini yazınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Milli egemenlik ilkesi yerleşti; halkın yönetime katılımı sağlandı.', 10],
        ['test', 90, 'Osmanlı Devleti hangi yıl kurulmuştur?', 'Osmanlı Devleti hangi yıl kurulmuştur?', '1299', '1400', '1453', '1517', '1683', 'A', NULL, 10],
        ['test', 91, 'Dünya\'nın en büyük kıtası hangisidir?', 'Dünya\'nın en büyük kıtası hangisidir?', 'Afrika', 'Amerika', 'Avrupa', 'Asya', 'Avustralya', 'D', NULL, 15],
        ['test', 92, 'En uzun ırmak hangisidir?', 'En uzun ırmak hangisidir?', 'Amazon', 'Nil', 'Mississippi', 'Yangtze', 'Volga', 'B', NULL, 10],
        ['klasik', 92, 'Türkiye\'nin komşu ülkelerini ve bu ülkelerle ilişkilerini açıklayınız.', 'Türkiye\'nin komşu ülkelerini ve bu ülkelerle ilişkilerini açıklayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Yunanistan, Bulgaristan, Gürcistan, Ermenistan, İran, Irak, Suriye, Azerbaycan.', 10],
        ['klasik', 94, 'Write 5 sentences about things you will do next weekend.', 'Write 5 sentences about things you will do next weekend.', NULL, NULL, NULL, NULL, NULL, NULL, 'Next weekend I will... I am going to... We will visit... She will help... They will play...', 15],
        ['klasik', 95, 'Write 5 questions using Wh- question words.', 'Write 5 questions using Wh- question words.', NULL, NULL, NULL, NULL, NULL, NULL, 'What, Where, When, Why, Who ile 5 soru cümlesi yazılmalıdır.', 15],
        ['test', 94, 'What is the plural of \'child\'?', 'What is the plural of \'child\'?', 'Childs', 'Childes', 'Children', 'Childrens', 'Childies', 'C', NULL, 10],
        ['test', 95, '\'Big\' means:', '\'Big\' means:', 'Küçük', 'Güzel', 'Büyük', 'Uzun', 'Kısa', 'C', NULL, 5],
        ['klasik', 98, 'Describe your school in 5 sentences.', 'Describe your school in 5 sentences.', NULL, NULL, NULL, NULL, NULL, NULL, 'My school is... It has... The teachers are... I study... My favourite class is...', 5],
        ['test', 97, 'Which sentence is correct?', 'Which sentence is correct?', 'I is happy', 'He are sad', 'She is tired', 'They is late', 'We am here', 'C', NULL, 10],
        ['klasik', 97, 'Make 5 sentences using the Past Simple Tense.', 'Make 5 sentences using the Past Simple Tense.', NULL, NULL, NULL, NULL, NULL, NULL, 'Yesterday I went... Last week she visited... They played... He cooked... We watched...', 10],
        ['test', 99, 'What is the plural of \'child\'?', 'What is the plural of \'child\'?', 'Childs', 'Childes', 'Children', 'Childrens', 'Childies', 'C', NULL, 10],
        ['klasik', 101, 'Write a short story about a memorable holiday.', 'Write a short story about a memorable holiday.', NULL, NULL, NULL, NULL, NULL, NULL, 'Last summer I went to... We stayed at... Every day we... It was... I will never forget...', 5],
        ['klasik', 101, 'Write a short letter to a pen pal introducing yourself.', 'Write a short letter to a pen pal introducing yourself.', NULL, NULL, NULL, NULL, NULL, NULL, 'Dear pen pal, My name is... I am ... years old. I live in... I like...', 20],
        ['klasik', 101, 'Describe your school in 5 sentences.', 'Describe your school in 5 sentences.', NULL, NULL, NULL, NULL, NULL, NULL, 'My school is... It has... The teachers are... I study... My favourite class is...', 10],
        ['klasik', 103, 'Write 5 sentences about your daily routine using Present Simple Tense.', 'Write 5 sentences about your daily routine using Present Simple Tense.', NULL, NULL, NULL, NULL, NULL, NULL, 'I wake up at 7. I eat breakfast. I go to school. I study. I sleep at 10.', 20],
        ['test', 104, 'How many letters are in the English alphabet?', 'How many letters are in the English alphabet?', '24', '25', '26', '27', '28', 'C', NULL, 10],
        ['klasik', 106, 'Write a short letter to a pen pal introducing yourself.', 'Write a short letter to a pen pal introducing yourself.', NULL, NULL, NULL, NULL, NULL, NULL, 'Dear pen pal, My name is... I am ... years old. I live in... I like...', 10],
        ['test', 108, 'Which is correct?', 'Which is correct?', 'She don\'t', 'She doesn\'t', 'She not', 'She isn\'t run', 'She no go', 'B', NULL, 10],
        ['test', 108, 'Which is correct?', 'Which is correct?', 'She don\'t', 'She doesn\'t', 'She not', 'She isn\'t run', 'She no go', 'B', NULL, 15],
        ['test', 110, 'Which word is an adjective?', 'Which word is an adjective?', 'Run', 'Quickly', 'Happy', 'Eat', 'Slowly', 'C', NULL, 20],
        ['klasik', 111, 'Write a short story about a memorable holiday.', 'Write a short story about a memorable holiday.', NULL, NULL, NULL, NULL, NULL, NULL, 'Last summer I went to... We stayed at... Every day we... It was... I will never forget...', 10],
        ['test', 110, 'Which word is an adjective?', 'Which word is an adjective?', 'Run', 'Quickly', 'Happy', 'Eat', 'Slowly', 'C', NULL, 10],
        ['test', 110, 'Which sentence is correct?', 'Which sentence is correct?', 'I is happy', 'He are sad', 'She is tired', 'They is late', 'We am here', 'C', NULL, 10],
        ['test', 112, 'What is the past tense of \'eat\'?', 'What is the past tense of \'eat\'?', 'Eated', 'Ate', 'Eaten', 'Eats', 'Eating', 'B', NULL, 15],
        ['test', 2, '15 + 27 işleminin sonucu nedir?', '15 + 27 işleminin sonucu nedir?', '40', '41', '42', '43', '44', 'C', NULL, 10],
        ['test', 2, 'Bir dikdörtgenin alanı 48 cm², kısa kenarı 6 cm ise uzun kenarı kaçtır?', 'Bir dikdörtgenin alanı 48 cm², kısa kenarı 6 cm ise uzun kenarı kaçtır?', '6', '7', '8', '9', '10', 'C', NULL, 15],
        ['test', 2, '3/4 + 1/4 işleminin sonucu nedir?', '3/4 + 1/4 işleminin sonucu nedir?', '1/2', '3/8', '1', '2', '4', 'C', NULL, 20],
        ['test', 4, '100\'ün %30\'u kaçtır?', '100\'ün %30\'u kaçtır?', '10', '20', '30', '40', '50', 'C', NULL, 10],
        ['test', 6, 'Bir üçgenin iç açıları toplamı kaç derecedir?', 'Bir üçgenin iç açıları toplamı kaç derecedir?', '90', '120', '180', '270', '360', 'C', NULL, 20],
        ['test', 6, '5! (5 faktöriyel) kaçtır?', '5! (5 faktöriyel) kaçtır?', '20', '60', '100', '120', '240', 'D', NULL, 20],
        ['test', 8, '0,5 + 0,75 kaçtır?', '0,5 + 0,75 kaçtır?', '1', '1,25', '1,5', '0,75', '2', 'B', NULL, 15],
        ['test', 6, 'En küçük asal sayı hangisidir?', 'En küçük asal sayı hangisidir?', '0', '1', '2', '3', '4', 'C', NULL, 5],
        ['klasik', 7, 'Bir mağaza %20 indirim uyguluyor. 150 TL\'lik ürünün fiyatı kaç TL olur?', 'Bir mağaza %20 indirim uyguluyor. 150 TL\'lik ürünün fiyatı kaç TL olur?', NULL, NULL, NULL, NULL, NULL, NULL, '150 × 0,80 = 120 TL', 10],
        ['klasik', 9, '3x + 7 = 22 denklemini çözünüz.', '3x + 7 = 22 denklemini çözünüz.', NULL, NULL, NULL, NULL, NULL, NULL, '3x = 15 → x = 5', 10],
    ];
    foreach ($qBatch as $q) {
        $qid = ins($db, "INSERT INTO `questions` (`type`,`user_id`,`unit_id`,`title`,`content`,`option_a`,`option_b`,`option_c`,`option_d`,`option_e`,`correct_opt`,`answer`,`points`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)",
            [$q[0], $uid, $unitIds[$q[1]], $q[2], $q[3], $q[4], $q[5], $q[6], $q[7], $q[8], $q[9], $q[10], $q[11]]);
        $questionIds[$qIdx] = ['id'=>$qid, 'ui'=>$q[1], 'type'=>$q[0]];
        $qIdx++;
    }
    // Chunk 17/20
    $qBatch = [
        ['klasik', 9, '2/3 + 3/4 - 1/6 işlemini yapınız.', '2/3 + 3/4 - 1/6 işlemini yapınız.', NULL, NULL, NULL, NULL, NULL, NULL, '8/12 + 9/12 - 2/12 = 15/12 = 5/4 = 1,25', 5],
        ['test', 11, 'Bir karenin çevresi 20 cm ise alanı kaçtır?', 'Bir karenin çevresi 20 cm ise alanı kaçtır?', '5', '10', '25', '20', '50', 'C', NULL, 10],
        ['klasik', 11, '24 ile 36\'nın EBOB ve EKOK\'unu bulunuz.', '24 ile 36\'nın EBOB ve EKOK\'unu bulunuz.', NULL, NULL, NULL, NULL, NULL, NULL, 'EBOB = 12, EKOK = 72', 20],
        ['klasik', 15, 'Bir sayının %25\'i 15 ise, bu sayı kaçtır?', 'Bir sayının %25\'i 15 ise, bu sayı kaçtır?', NULL, NULL, NULL, NULL, NULL, NULL, 'x × 0,25 = 15 → x = 60', 5],
        ['test', 13, '0,5 + 0,75 kaçtır?', '0,5 + 0,75 kaçtır?', '1', '1,25', '1,5', '0,75', '2', 'B', NULL, 10],
        ['test', 14, '3/4 + 1/4 işleminin sonucu nedir?', '3/4 + 1/4 işleminin sonucu nedir?', '1/2', '3/8', '1', '2', '4', 'C', NULL, 20],
        ['test', 17, '0,25\'in kesir karşılığı hangisidir?', '0,25\'in kesir karşılığı hangisidir?', '1/2', '1/4', '1/3', '2/5', '3/4', 'B', NULL, 10],
        ['test', 16, 'Bir dikdörtgenin alanı 48 cm², kısa kenarı 6 cm ise uzun kenarı kaçtır?', 'Bir dikdörtgenin alanı 48 cm², kısa kenarı 6 cm ise uzun kenarı kaçtır?', '6', '7', '8', '9', '10', 'C', NULL, 20],
        ['klasik', 20, '2/3 + 3/4 - 1/6 işlemini yapınız.', '2/3 + 3/4 - 1/6 işlemini yapınız.', NULL, NULL, NULL, NULL, NULL, NULL, '8/12 + 9/12 - 2/12 = 15/12 = 5/4 = 1,25', 10],
        ['klasik', 18, 'Bir çemberin çapı 14 cm ise çevresi kaçtır? (π=3,14)', 'Bir çemberin çapı 14 cm ise çevresi kaçtır? (π=3,14)', NULL, NULL, NULL, NULL, NULL, NULL, 'Çevre = π × d = 3,14 × 14 = 43,96 cm', 15],
        ['test', 20, 'En küçük asal sayı hangisidir?', 'En küçük asal sayı hangisidir?', '0', '1', '2', '3', '4', 'C', NULL, 15],
        ['klasik', 22, '5 kişilik grubun not ortalaması 72\'dir. Toplamları kaçtır?', '5 kişilik grubun not ortalaması 72\'dir. Toplamları kaçtır?', NULL, NULL, NULL, NULL, NULL, NULL, '5 × 72 = 360', 10],
        ['test', 24, 'Bir dikdörtgenin alanı 48 cm², kısa kenarı 6 cm ise uzun kenarı kaçtır?', 'Bir dikdörtgenin alanı 48 cm², kısa kenarı 6 cm ise uzun kenarı kaçtır?', '6', '7', '8', '9', '10', 'C', NULL, 10],
        ['test', 25, '2^5 işleminin sonucu kaçtır?', '2^5 işleminin sonucu kaçtır?', '8', '16', '32', '64', '128', 'C', NULL, 20],
        ['test', 24, 'Bir dikdörtgenin alanı 48 cm², kısa kenarı 6 cm ise uzun kenarı kaçtır?', 'Bir dikdörtgenin alanı 48 cm², kısa kenarı 6 cm ise uzun kenarı kaçtır?', '6', '7', '8', '9', '10', 'C', NULL, 5],
        ['test', 24, 'Aşağıdakilerden hangisi asal sayıdır?', 'Aşağıdakilerden hangisi asal sayıdır?', '12', '17', '21', '35', '9', 'B', NULL, 10],
        ['test', 28, '9\'un karekökü kaçtır?', '9\'un karekökü kaçtır?', '2', '3', '4', '6', '81', 'B', NULL, 5],
        ['klasik', 26, '1\'den 10\'a kadar olan tam sayıların toplamını hesaplayınız.', '1\'den 10\'a kadar olan tam sayıların toplamını hesaplayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Toplam = 10×11/2 = 55', 5],
        ['klasik', 30, '24 ile 36\'nın EBOB ve EKOK\'unu bulunuz.', '24 ile 36\'nın EBOB ve EKOK\'unu bulunuz.', NULL, NULL, NULL, NULL, NULL, NULL, 'EBOB = 12, EKOK = 72', 20],
        ['test', 30, 'Bir üçgenin iç açıları toplamı kaç derecedir?', 'Bir üçgenin iç açıları toplamı kaç derecedir?', '90', '120', '180', '270', '360', 'C', NULL, 5],
        ['test', 29, '5! (5 faktöriyel) kaçtır?', '5! (5 faktöriyel) kaçtır?', '20', '60', '100', '120', '240', 'D', NULL, 20],
        ['test', 33, 'Isıyı iyi ileten madde hangisidir?', 'Isıyı iyi ileten madde hangisidir?', 'Tahta', 'Plastik', 'Cam', 'Demir', 'Kauçuk', 'D', NULL, 5],
        ['klasik', 33, 'Bitkisel ve hayvansal hücreler arasındaki farkları yazınız.', 'Bitkisel ve hayvansal hücreler arasındaki farkları yazınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Bitki hücresinde hücre duvarı ve kloroplast bulunur; hayvan hücresinde bunlar yoktur.', 15],
        ['test', 32, 'Ses hangi ortamda yayılmaz?', 'Ses hangi ortamda yayılmaz?', 'Katı', 'Sıvı', 'Gaz', 'Vakum', 'Su', 'D', NULL, 10],
        ['klasik', 33, 'Ekosistemdeki enerji akışını açıklayınız.', 'Ekosistemdeki enerji akışını açıklayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Güneş → Üreticiler → Birincil tüketiciler → İkincil tüketiciler → Ayrıştırıcılar', 15],
        ['klasik', 35, 'Elektrik devresinde seri ve paralel bağlamayı açıklayınız.', 'Elektrik devresinde seri ve paralel bağlamayı açıklayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Seri: tek yol, akım sabit. Paralel: çok yol, gerilim sabit.', 20],
        ['klasik', 38, 'Fotosentez olayını kendi cümlelerinizle açıklayınız.', 'Fotosentez olayını kendi cümlelerinizle açıklayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Bitkiler güneş enerjisini kullanarak CO₂ ve suyu glikoz ve O₂\'ye dönüştürür.', 10],
        ['test', 37, 'Ses hangi ortamda yayılmaz?', 'Ses hangi ortamda yayılmaz?', 'Katı', 'Sıvı', 'Gaz', 'Vakum', 'Su', 'D', NULL, 15],
        ['klasik', 40, 'Maddenin üç halini karşılaştırınız.', 'Maddenin üç halini karşılaştırınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Katı: sabit şekil/hacim. Sıvı: sabit hacim, değişken şekil. Gaz: değişken şekil/hacim.', 15],
        ['test', 40, 'Kan hücresi olmayan kan bileşeni hangisidir?', 'Kan hücresi olmayan kan bileşeni hangisidir?', 'Alyuvar', 'Akyuvar', 'Trombosit', 'Plazma', 'Hemoglobin', 'D', NULL, 20],
        ['klasik', 40, 'Maddenin üç halini karşılaştırınız.', 'Maddenin üç halini karşılaştırınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Katı: sabit şekil/hacim. Sıvı: sabit hacim, değişken şekil. Gaz: değişken şekil/hacim.', 20],
        ['test', 40, 'Güneş sistemi kaç gezegenden oluşur?', 'Güneş sistemi kaç gezegenden oluşur?', '6', '7', '8', '9', '10', 'C', NULL, 10],
        ['test', 41, 'Hücrenin kontrol merkezi hangisidir?', 'Hücrenin kontrol merkezi hangisidir?', 'Mitokondri', 'Çekirdek', 'Hücre zarı', 'Kloroplast', 'Ribozom', 'B', NULL, 10],
        ['test', 42, 'Newton\'un kuvvet birimi hangisidir?', 'Newton\'un kuvvet birimi hangisidir?', 'Joule', 'Watt', 'Newton', 'Pascal', 'Ohm', 'C', NULL, 15],
        ['klasik', 46, 'Fotosentez olayını kendi cümlelerinizle açıklayınız.', 'Fotosentez olayını kendi cümlelerinizle açıklayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Bitkiler güneş enerjisini kullanarak CO₂ ve suyu glikoz ve O₂\'ye dönüştürür.', 10],
        ['klasik', 44, 'Ses dalgalarının özellikleri nelerdir?', 'Ses dalgalarının özellikleri nelerdir?', NULL, NULL, NULL, NULL, NULL, NULL, 'Ses mekanik dalgadır; ortam gerektirir. Frekans (Hz) ve genlik ile tanımlanır.', 10],
        ['test', 48, 'Hücrenin kontrol merkezi hangisidir?', 'Hücrenin kontrol merkezi hangisidir?', 'Mitokondri', 'Çekirdek', 'Hücre zarı', 'Kloroplast', 'Ribozom', 'B', NULL, 15],
        ['test', 49, 'Kan hücresi olmayan kan bileşeni hangisidir?', 'Kan hücresi olmayan kan bileşeni hangisidir?', 'Alyuvar', 'Akyuvar', 'Trombosit', 'Plazma', 'Hemoglobin', 'D', NULL, 20],
        ['test', 50, 'Işığın havadaki hızı yaklaşık kaç km/s?', 'Işığın havadaki hızı yaklaşık kaç km/s?', '100.000', '200.000', '300.000', '400.000', '150.000', 'C', NULL, 15],
        ['klasik', 50, 'Maddenin üç halini karşılaştırınız.', 'Maddenin üç halini karşılaştırınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Katı: sabit şekil/hacim. Sıvı: sabit hacim, değişken şekil. Gaz: değişken şekil/hacim.', 20],
        ['test', 49, 'Suyun kimyasal formülü nedir?', 'Suyun kimyasal formülü nedir?', 'CO2', 'H2O', 'O2', 'NaCl', 'CH4', 'B', NULL, 10],
        ['klasik', 53, 'Yer çekimi kuvvetini açıklayınız.', 'Yer çekimi kuvvetini açıklayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Kütleli cisimler birbirini çeker. Dünya yüzeyi 9,8 m/s² ivme uygular.', 20],
        ['klasik', 52, 'Ses dalgalarının özellikleri nelerdir?', 'Ses dalgalarının özellikleri nelerdir?', NULL, NULL, NULL, NULL, NULL, NULL, 'Ses mekanik dalgadır; ortam gerektirir. Frekans (Hz) ve genlik ile tanımlanır.', 10],
        ['test', 54, 'Isıyı iyi ileten madde hangisidir?', 'Isıyı iyi ileten madde hangisidir?', 'Tahta', 'Plastik', 'Cam', 'Demir', 'Kauçuk', 'D', NULL, 5],
        ['test', 55, 'Omurgasız bir hayvan hangisidir?', 'Omurgasız bir hayvan hangisidir?', 'Balık', 'Kurbağa', 'Ahtapot', 'Yılan', 'Kuş', 'C', NULL, 10],
        ['test', 54, 'En hafif element hangisidir?', 'En hafif element hangisidir?', 'Helyum', 'Hidrojen', 'Lityum', 'Oksijen', 'Karbon', 'B', NULL, 10],
        ['test', 58, '\'Yazmak\' fiilinin isim hali hangisidir?', '\'Yazmak\' fiilinin isim hali hangisidir?', 'Yazı', 'Yazıcı', 'Yazan', 'Yazarak', 'Yazılı', 'A', NULL, 15],
        ['test', 59, 'Aşağıdaki sözcüklerden hangisi isimdir?', 'Aşağıdaki sözcüklerden hangisi isimdir?', 'Güzel', 'Koşmak', 'Kitap', 'Hızlı', 'Çabuk', 'C', NULL, 5],
        ['test', 59, '\'Okul\' sözcüğü hangi türden isimdir?', '\'Okul\' sözcüğü hangi türden isimdir?', 'Özel', 'Soyut', 'Somut', 'Topluluk', 'Eylem', 'C', NULL, 15],
        ['klasik', 60, 'Verilen metindeki sıfatları bulunuz ve türlerini belirtiniz.', 'Verilen metindeki sıfatları bulunuz ve türlerini belirtiniz.', NULL, NULL, NULL, NULL, NULL, NULL, 'Niteleme sıfatı: güzel. Sayı sıfatı: üç. Belgisiz sıfat: bazı.', 5],
    ];
    foreach ($qBatch as $q) {
        $qid = ins($db, "INSERT INTO `questions` (`type`,`user_id`,`unit_id`,`title`,`content`,`option_a`,`option_b`,`option_c`,`option_d`,`option_e`,`correct_opt`,`answer`,`points`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)",
            [$q[0], $uid, $unitIds[$q[1]], $q[2], $q[3], $q[4], $q[5], $q[6], $q[7], $q[8], $q[9], $q[10], $q[11]]);
        $questionIds[$qIdx] = ['id'=>$qid, 'ui'=>$q[1], 'type'=>$q[0]];
        $qIdx++;
    }
    // Chunk 18/20
    $qBatch = [
        ['test', 61, 'Cümle sonu hangi noktalama işareti kullanılır?', 'Cümle sonu hangi noktalama işareti kullanılır?', 'Virgül', 'Noktalı virgül', 'Nokta', 'Tire', 'İki nokta', 'C', NULL, 5],
        ['test', 61, '\'Gelmek\' fiilinin geniş zamanı hangisidir?', '\'Gelmek\' fiilinin geniş zamanı hangisidir?', 'Geldi', 'Gelecek', 'Gelir', 'Gelsin', 'Gelse', 'C', NULL, 10],
        ['test', 64, '\'Ağaç\' sözcüğünde kaç hece vardır?', '\'Ağaç\' sözcüğünde kaç hece vardır?', '1', '2', '3', '4', '5', 'B', NULL, 10],
        ['test', 64, 'Aşağıdakilerden hangisi bağlaçtır?', 'Aşağıdakilerden hangisi bağlaçtır?', 'Çok', 'Ve', 'Güzel', 'Hızla', 'Ama', 'B', NULL, 10],
        ['test', 63, '\'Ağaç\' sözcüğünde kaç hece vardır?', '\'Ağaç\' sözcüğünde kaç hece vardır?', '1', '2', '3', '4', '5', 'B', NULL, 10],
        ['test', 67, '\'Büyük\' sözcüğünün zıt anlamlısı nedir?', '\'Büyük\' sözcüğünün zıt anlamlısı nedir?', 'Ufak', 'Uzun', 'Dar', 'Kalın', 'Ağır', 'A', NULL, 5],
        ['test', 67, 'Anlatıcı bakış açısı kaç türdür?', 'Anlatıcı bakış açısı kaç türdür?', '2', '3', '4', '5', '6', 'C', NULL, 10],
        ['test', 69, '\'Ağaç\' sözcüğünde kaç hece vardır?', '\'Ağaç\' sözcüğünde kaç hece vardır?', '1', '2', '3', '4', '5', 'B', NULL, 10],
        ['test', 70, 'Aşağıdakilerden hangisi bağlaçtır?', 'Aşağıdakilerden hangisi bağlaçtır?', 'Çok', 'Ve', 'Güzel', 'Hızla', 'Ama', 'B', NULL, 10],
        ['klasik', 68, 'Paragraftaki bağlaçları ve görevlerini yazınız.', 'Paragraftaki bağlaçları ve görevlerini yazınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Ve (ekleme), ama (karşıtlık), çünkü (neden-sonuç).', 10],
        ['klasik', 72, 'Metindeki noktalama hatalarını bulunuz ve düzeltiniz.', 'Metindeki noktalama hatalarını bulunuz ve düzeltiniz.', NULL, NULL, NULL, NULL, NULL, NULL, 'Yanlış noktalama işaretleri tespit edilip düzeltilmelidir.', 10],
        ['test', 73, '\'Gelmek\' fiilinin geniş zamanı hangisidir?', '\'Gelmek\' fiilinin geniş zamanı hangisidir?', 'Geldi', 'Gelecek', 'Gelir', 'Gelsin', 'Gelse', 'C', NULL, 20],
        ['test', 72, '\'Büyük\' sözcüğünün zıt anlamlısı nedir?', '\'Büyük\' sözcüğünün zıt anlamlısı nedir?', 'Ufak', 'Uzun', 'Dar', 'Kalın', 'Ağır', 'A', NULL, 15],
        ['test', 73, 'Anlatıcı bakış açısı kaç türdür?', 'Anlatıcı bakış açısı kaç türdür?', '2', '3', '4', '5', '6', 'C', NULL, 10],
        ['klasik', 76, 'İpek Yolu\'nun tarihsel önemini açıklayınız.', 'İpek Yolu\'nun tarihsel önemini açıklayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Doğu-Batı ticareti, kültür ve bilgi alışverişi için köprü görevi gördü.', 10],
        ['klasik', 77, 'Osmanlı Devleti\'nin yıkılış nedenlerini açıklayınız.', 'Osmanlı Devleti\'nin yıkılış nedenlerini açıklayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Savaş yenilgileri, azınlık isyanları, ekonomik çöküş, Batı\'nın müdahalesi.', 10],
        ['test', 77, 'Osmanlı Devleti hangi yıl kurulmuştur?', 'Osmanlı Devleti hangi yıl kurulmuştur?', '1299', '1400', '1453', '1517', '1683', 'A', NULL, 10],
        ['test', 78, 'Türkiye Cumhuriyeti hangi yıl kurulmuştur?', 'Türkiye Cumhuriyeti hangi yıl kurulmuştur?', '1919', '1920', '1923', '1925', '1938', 'C', NULL, 15],
        ['test', 78, 'Dünya\'nın en büyük kıtası hangisidir?', 'Dünya\'nın en büyük kıtası hangisidir?', 'Afrika', 'Amerika', 'Avrupa', 'Asya', 'Avustralya', 'D', NULL, 15],
        ['klasik', 79, 'Türkiye\'nin coğrafi konumunun avantajlarını yazınız.', 'Türkiye\'nin coğrafi konumunun avantajlarını yazınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Üç tarafı denizle çevrili, ticaret yolları üzerinde, farklı iklimlere sahip.', 15],
        ['klasik', 79, 'Cumhuriyetin ilanının önemini yazınız.', 'Cumhuriyetin ilanının önemini yazınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Milli egemenlik ilkesi yerleşti; halkın yönetime katılımı sağlandı.', 10],
        ['test', 81, 'Dünya\'nın kaç tane okyanusu vardır?', 'Dünya\'nın kaç tane okyanusu vardır?', '3', '4', '5', '6', '7', 'C', NULL, 10],
        ['klasik', 81, 'Türkiye\'nin komşu ülkelerini ve bu ülkelerle ilişkilerini açıklayınız.', 'Türkiye\'nin komşu ülkelerini ve bu ülkelerle ilişkilerini açıklayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Yunanistan, Bulgaristan, Gürcistan, Ermenistan, İran, Irak, Suriye, Azerbaycan.', 10],
        ['test', 85, 'Türkiye\'nin en uzun nehri hangisidir?', 'Türkiye\'nin en uzun nehri hangisidir?', 'Dicle', 'Fırat', 'Kızılırmak', 'Yeşilırmak', 'Sakarya', 'C', NULL, 10],
        ['test', 84, 'Türkiye kaç komşuya sahiptir?', 'Türkiye kaç komşuya sahiptir?', '5', '6', '7', '8', '9', 'D', NULL, 10],
        ['klasik', 87, 'Türkiye\'deki iklim çeşitliliğinin nedenlerini açıklayınız.', 'Türkiye\'deki iklim çeşitliliğinin nedenlerini açıklayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Enlem, yükselti, denizden uzaklık ve dağların uzanışı belirleyicidir.', 5],
        ['klasik', 88, 'Türkiye\'deki iklim çeşitliliğinin nedenlerini açıklayınız.', 'Türkiye\'deki iklim çeşitliliğinin nedenlerini açıklayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Enlem, yükselti, denizden uzaklık ve dağların uzanışı belirleyicidir.', 10],
        ['test', 89, 'Hangi şehir iki kıta üzerindedir?', 'Hangi şehir iki kıta üzerindedir?', 'Ankara', 'İzmir', 'İstanbul', 'Bursa', 'Antalya', 'C', NULL, 20],
        ['test', 90, 'Türkiye kaç komşuya sahiptir?', 'Türkiye kaç komşuya sahiptir?', '5', '6', '7', '8', '9', 'D', NULL, 10],
        ['test', 88, 'Osmanlı Devleti hangi yıl kurulmuştur?', 'Osmanlı Devleti hangi yıl kurulmuştur?', '1299', '1400', '1453', '1517', '1683', 'A', NULL, 10],
        ['test', 91, 'Hangi şehir iki kıta üzerindedir?', 'Hangi şehir iki kıta üzerindedir?', 'Ankara', 'İzmir', 'İstanbul', 'Bursa', 'Antalya', 'C', NULL, 15],
        ['test', 90, 'Dünya\'nın en büyük kıtası hangisidir?', 'Dünya\'nın en büyük kıtası hangisidir?', 'Afrika', 'Amerika', 'Avrupa', 'Asya', 'Avustralya', 'D', NULL, 20],
        ['klasik', 94, 'Compare summer and winter using at least 5 adjectives.', 'Compare summer and winter using at least 5 adjectives.', NULL, NULL, NULL, NULL, NULL, NULL, 'Summer: hot, sunny, long, fun, bright. Winter: cold, dark, short, snowy, quiet.', 10],
        ['test', 95, 'What is the plural of \'child\'?', 'What is the plural of \'child\'?', 'Childs', 'Childes', 'Children', 'Childrens', 'Childies', 'C', NULL, 5],
        ['test', 95, 'Which word is an adjective?', 'Which word is an adjective?', 'Run', 'Quickly', 'Happy', 'Eat', 'Slowly', 'C', NULL, 20],
        ['test', 95, 'What does \'beautiful\' mean?', 'What does \'beautiful\' mean?', 'Çirkin', 'Küçük', 'Büyük', 'Güzel', 'Uzun', 'D', NULL, 10],
        ['klasik', 98, 'Write 5 sentences about your daily routine using Present Simple Tense.', 'Write 5 sentences about your daily routine using Present Simple Tense.', NULL, NULL, NULL, NULL, NULL, NULL, 'I wake up at 7. I eat breakfast. I go to school. I study. I sleep at 10.', 15],
        ['test', 97, 'Opposite of \'hot\' is:', 'Opposite of \'hot\' is:', 'Warm', 'Cold', 'Cool', 'Mild', 'Humid', 'B', NULL, 5],
        ['test', 100, '\'We ___ to school yesterday.\' Fill in.', '\'We ___ to school yesterday.\' Fill in.', 'go', 'goes', 'gone', 'went', 'going', 'D', NULL, 10],
        ['test', 98, '\'I ___ a student.\' Fill in the blank.', '\'I ___ a student.\' Fill in the blank.', 'am', 'is', 'are', 'be', 'been', 'A', NULL, 20],
        ['test', 100, 'What is the past tense of \'eat\'?', 'What is the past tense of \'eat\'?', 'Eated', 'Ate', 'Eaten', 'Eats', 'Eating', 'B', NULL, 10],
        ['klasik', 103, 'Write a short paragraph about your favourite season.', 'Write a short paragraph about your favourite season.', NULL, NULL, NULL, NULL, NULL, NULL, 'My favourite season is ... because ... The weather is ... I enjoy ...', 15],
        ['test', 104, '\'They ___ playing football.\' Fill in.', '\'They ___ playing football.\' Fill in.', 'is', 'am', 'are', 'was', 'were', 'C', NULL, 5],
        ['klasik', 105, 'Write 5 sentences about your daily routine using Present Simple Tense.', 'Write 5 sentences about your daily routine using Present Simple Tense.', NULL, NULL, NULL, NULL, NULL, NULL, 'I wake up at 7. I eat breakfast. I go to school. I study. I sleep at 10.', 10],
        ['test', 105, 'What is the past tense of \'go\'?', 'What is the past tense of \'go\'?', 'Goed', 'Goes', 'Going', 'Gone', 'Went', 'E', NULL, 5],
        ['test', 104, 'Which is correct?', 'Which is correct?', 'She don\'t', 'She doesn\'t', 'She not', 'She isn\'t run', 'She no go', 'B', NULL, 10],
        ['klasik', 105, 'Write 5 sentences about things you will do next weekend.', 'Write 5 sentences about things you will do next weekend.', NULL, NULL, NULL, NULL, NULL, NULL, 'Next weekend I will... I am going to... We will visit... She will help... They will play...', 10],
        ['test', 107, 'What does \'beautiful\' mean?', 'What does \'beautiful\' mean?', 'Çirkin', 'Küçük', 'Büyük', 'Güzel', 'Uzun', 'D', NULL, 15],
        ['test', 108, 'Which is correct?', 'Which is correct?', 'She don\'t', 'She doesn\'t', 'She not', 'She isn\'t run', 'She no go', 'B', NULL, 10],
        ['test', 111, 'Which sentence is correct?', 'Which sentence is correct?', 'I is happy', 'He are sad', 'She is tired', 'They is late', 'We am here', 'C', NULL, 10],
    ];
    foreach ($qBatch as $q) {
        $qid = ins($db, "INSERT INTO `questions` (`type`,`user_id`,`unit_id`,`title`,`content`,`option_a`,`option_b`,`option_c`,`option_d`,`option_e`,`correct_opt`,`answer`,`points`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)",
            [$q[0], $uid, $unitIds[$q[1]], $q[2], $q[3], $q[4], $q[5], $q[6], $q[7], $q[8], $q[9], $q[10], $q[11]]);
        $questionIds[$qIdx] = ['id'=>$qid, 'ui'=>$q[1], 'type'=>$q[0]];
        $qIdx++;
    }
    // Chunk 19/20
    $qBatch = [
        ['test', 110, 'Which sentence is correct?', 'Which sentence is correct?', 'I is happy', 'He are sad', 'She is tired', 'They is late', 'We am here', 'C', NULL, 20],
        ['test', 0, '36\'nın karekökü kaçtır?', '36\'nın karekökü kaçtır?', '4', '5', '6', '7', '8', 'C', NULL, 20],
        ['test', 112, 'Which is correct?', 'Which is correct?', 'She don\'t', 'She doesn\'t', 'She not', 'She isn\'t run', 'She no go', 'B', NULL, 10],
        ['test', 2, 'En büyük iki basamaklı sayı hangisidir?', 'En büyük iki basamaklı sayı hangisidir?', '89', '90', '98', '99', '100', 'D', NULL, 10],
        ['test', 1, '0,25\'in kesir karşılığı hangisidir?', '0,25\'in kesir karşılığı hangisidir?', '1/2', '1/4', '1/3', '2/5', '3/4', 'B', NULL, 20],
        ['test', 1, 'En büyük iki basamaklı sayı hangisidir?', 'En büyük iki basamaklı sayı hangisidir?', '89', '90', '98', '99', '100', 'D', NULL, 15],
        ['test', 3, 'Bir dikdörtgenin alanı 48 cm², kısa kenarı 6 cm ise uzun kenarı kaçtır?', 'Bir dikdörtgenin alanı 48 cm², kısa kenarı 6 cm ise uzun kenarı kaçtır?', '6', '7', '8', '9', '10', 'C', NULL, 10],
        ['test', 6, '2^5 işleminin sonucu kaçtır?', '2^5 işleminin sonucu kaçtır?', '8', '16', '32', '64', '128', 'C', NULL, 20],
        ['test', 7, '0,5 + 0,75 kaçtır?', '0,5 + 0,75 kaçtır?', '1', '1,25', '1,5', '0,75', '2', 'B', NULL, 20],
        ['test', 7, 'Bir üçgenin iç açıları toplamı kaç derecedir?', 'Bir üçgenin iç açıları toplamı kaç derecedir?', '90', '120', '180', '270', '360', 'C', NULL, 15],
        ['test', 9, '3/4 + 1/4 işleminin sonucu nedir?', '3/4 + 1/4 işleminin sonucu nedir?', '1/2', '3/8', '1', '2', '4', 'C', NULL, 10],
        ['klasik', 9, 'Bir çemberin çapı 14 cm ise çevresi kaçtır? (π=3,14)', 'Bir çemberin çapı 14 cm ise çevresi kaçtır? (π=3,14)', NULL, NULL, NULL, NULL, NULL, NULL, 'Çevre = π × d = 3,14 × 14 = 43,96 cm', 15],
        ['klasik', 9, '3x + 7 = 22 denklemini çözünüz.', '3x + 7 = 22 denklemini çözünüz.', NULL, NULL, NULL, NULL, NULL, NULL, '3x = 15 → x = 5', 20],
        ['klasik', 11, '5 kişilik grubun not ortalaması 72\'dir. Toplamları kaçtır?', '5 kişilik grubun not ortalaması 72\'dir. Toplamları kaçtır?', NULL, NULL, NULL, NULL, NULL, NULL, '5 × 72 = 360', 15],
        ['klasik', 10, 'Kapalı bir kutunun kenarları 3, 4 ve 5 cm\'dir. Hacmini bulunuz.', 'Kapalı bir kutunun kenarları 3, 4 ve 5 cm\'dir. Hacmini bulunuz.', NULL, NULL, NULL, NULL, NULL, NULL, 'Hacim = 3 × 4 × 5 = 60 cm³', 10],
        ['test', 11, 'En büyük iki basamaklı sayı hangisidir?', 'En büyük iki basamaklı sayı hangisidir?', '89', '90', '98', '99', '100', 'D', NULL, 5],
        ['test', 13, 'Bir karenin çevresi 20 cm ise alanı kaçtır?', 'Bir karenin çevresi 20 cm ise alanı kaçtır?', '5', '10', '25', '20', '50', 'C', NULL, 10],
        ['test', 16, '15 + 27 işleminin sonucu nedir?', '15 + 27 işleminin sonucu nedir?', '40', '41', '42', '43', '44', 'C', NULL, 10],
        ['test', 17, 'Aşağıdakilerden hangisi asal sayıdır?', 'Aşağıdakilerden hangisi asal sayıdır?', '12', '17', '21', '35', '9', 'B', NULL, 10],
        ['test', 16, '9\'un karekökü kaçtır?', '9\'un karekökü kaçtır?', '2', '3', '4', '6', '81', 'B', NULL, 10],
        ['test', 18, '36\'nın karekökü kaçtır?', '36\'nın karekökü kaçtır?', '4', '5', '6', '7', '8', 'C', NULL, 10],
        ['klasik', 20, 'Bir mağaza %20 indirim uyguluyor. 150 TL\'lik ürünün fiyatı kaç TL olur?', 'Bir mağaza %20 indirim uyguluyor. 150 TL\'lik ürünün fiyatı kaç TL olur?', NULL, NULL, NULL, NULL, NULL, NULL, '150 × 0,80 = 120 TL', 10],
        ['test', 18, '9\'un karekökü kaçtır?', '9\'un karekökü kaçtır?', '2', '3', '4', '6', '81', 'B', NULL, 10],
        ['klasik', 22, 'Kapalı bir kutunun kenarları 3, 4 ve 5 cm\'dir. Hacmini bulunuz.', 'Kapalı bir kutunun kenarları 3, 4 ve 5 cm\'dir. Hacmini bulunuz.', NULL, NULL, NULL, NULL, NULL, NULL, 'Hacim = 3 × 4 × 5 = 60 cm³', 15],
        ['test', 23, 'Bir karenin çevresi 20 cm ise alanı kaçtır?', 'Bir karenin çevresi 20 cm ise alanı kaçtır?', '5', '10', '25', '20', '50', 'C', NULL, 10],
        ['klasik', 24, '3x + 7 = 22 denklemini çözünüz.', '3x + 7 = 22 denklemini çözünüz.', NULL, NULL, NULL, NULL, NULL, NULL, '3x = 15 → x = 5', 10],
        ['test', 25, 'Bir üçgenin iç açıları toplamı kaç derecedir?', 'Bir üçgenin iç açıları toplamı kaç derecedir?', '90', '120', '180', '270', '360', 'C', NULL, 15],
        ['klasik', 23, '24 ile 36\'nın EBOB ve EKOK\'unu bulunuz.', '24 ile 36\'nın EBOB ve EKOK\'unu bulunuz.', NULL, NULL, NULL, NULL, NULL, NULL, 'EBOB = 12, EKOK = 72', 10],
        ['test', 26, '0,5 + 0,75 kaçtır?', '0,5 + 0,75 kaçtır?', '1', '1,25', '1,5', '0,75', '2', 'B', NULL, 10],
        ['test', 27, '15 + 27 işleminin sonucu nedir?', '15 + 27 işleminin sonucu nedir?', '40', '41', '42', '43', '44', 'C', NULL, 10],
        ['test', 29, '2^5 işleminin sonucu kaçtır?', '2^5 işleminin sonucu kaçtır?', '8', '16', '32', '64', '128', 'C', NULL, 5],
        ['test', 27, 'Bir üçgenin iç açıları toplamı kaç derecedir?', 'Bir üçgenin iç açıları toplamı kaç derecedir?', '90', '120', '180', '270', '360', 'C', NULL, 15],
        ['test', 30, '36\'nın karekökü kaçtır?', '36\'nın karekökü kaçtır?', '4', '5', '6', '7', '8', 'C', NULL, 5],
        ['test', 32, 'Işığın havadaki hızı yaklaşık kaç km/s?', 'Işığın havadaki hızı yaklaşık kaç km/s?', '100.000', '200.000', '300.000', '400.000', '150.000', 'C', NULL, 10],
        ['klasik', 30, '3x + 7 = 22 denklemini çözünüz.', '3x + 7 = 22 denklemini çözünüz.', NULL, NULL, NULL, NULL, NULL, NULL, '3x = 15 → x = 5', 20],
        ['test', 32, 'Suyun kimyasal formülü nedir?', 'Suyun kimyasal formülü nedir?', 'CO2', 'H2O', 'O2', 'NaCl', 'CH4', 'B', NULL, 5],
        ['test', 35, 'En hafif element hangisidir?', 'En hafif element hangisidir?', 'Helyum', 'Hidrojen', 'Lityum', 'Oksijen', 'Karbon', 'B', NULL, 5],
        ['klasik', 36, 'Besin zinciri nedir? Bir örnek vererek açıklayınız.', 'Besin zinciri nedir? Bir örnek vererek açıklayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Enerji üreticiden tüketiciye aktarılır. Örnek: Ot → Tavşan → Tilki → Bakteri', 5],
        ['test', 35, 'Newton\'un kuvvet birimi hangisidir?', 'Newton\'un kuvvet birimi hangisidir?', 'Joule', 'Watt', 'Newton', 'Pascal', 'Ohm', 'C', NULL, 20],
        ['klasik', 37, 'Sindirim sistemindeki organları sıralayınız.', 'Sindirim sistemindeki organları sıralayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Ağız → Yemek borusu → Mide → İnce bağırsak → Kalın bağırsak → Anüs', 15],
        ['test', 39, 'Isıyı iyi ileten madde hangisidir?', 'Isıyı iyi ileten madde hangisidir?', 'Tahta', 'Plastik', 'Cam', 'Demir', 'Kauçuk', 'D', NULL, 10],
        ['klasik', 38, 'Maddenin üç halini karşılaştırınız.', 'Maddenin üç halini karşılaştırınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Katı: sabit şekil/hacim. Sıvı: sabit hacim, değişken şekil. Gaz: değişken şekil/hacim.', 5],
        ['test', 40, 'Kan hücresi olmayan kan bileşeni hangisidir?', 'Kan hücresi olmayan kan bileşeni hangisidir?', 'Alyuvar', 'Akyuvar', 'Trombosit', 'Plazma', 'Hemoglobin', 'D', NULL, 15],
        ['test', 40, 'Hücrenin kontrol merkezi hangisidir?', 'Hücrenin kontrol merkezi hangisidir?', 'Mitokondri', 'Çekirdek', 'Hücre zarı', 'Kloroplast', 'Ribozom', 'B', NULL, 20],
        ['test', 42, 'Deprem hangi alet ile ölçülür?', 'Deprem hangi alet ile ölçülür?', 'Termometre', 'Barometre', 'Sismograf', 'Higrومetre', 'Manyetometre', 'C', NULL, 10],
        ['test', 41, 'Güneş sistemi kaç gezegenden oluşur?', 'Güneş sistemi kaç gezegenden oluşur?', '6', '7', '8', '9', '10', 'C', NULL, 10],
        ['klasik', 42, 'Elektrik devresinde seri ve paralel bağlamayı açıklayınız.', 'Elektrik devresinde seri ve paralel bağlamayı açıklayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Seri: tek yol, akım sabit. Paralel: çok yol, gerilim sabit.', 20],
        ['klasik', 44, 'Bitkisel ve hayvansal hücreler arasındaki farkları yazınız.', 'Bitkisel ve hayvansal hücreler arasındaki farkları yazınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Bitki hücresinde hücre duvarı ve kloroplast bulunur; hayvan hücresinde bunlar yoktur.', 10],
        ['test', 45, 'En hafif element hangisidir?', 'En hafif element hangisidir?', 'Helyum', 'Hidrojen', 'Lityum', 'Oksijen', 'Karbon', 'B', NULL, 10],
        ['klasik', 46, 'Yer çekimi kuvvetini açıklayınız.', 'Yer çekimi kuvvetini açıklayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Kütleli cisimler birbirini çeker. Dünya yüzeyi 9,8 m/s² ivme uygular.', 20],
    ];
    foreach ($qBatch as $q) {
        $qid = ins($db, "INSERT INTO `questions` (`type`,`user_id`,`unit_id`,`title`,`content`,`option_a`,`option_b`,`option_c`,`option_d`,`option_e`,`correct_opt`,`answer`,`points`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)",
            [$q[0], $uid, $unitIds[$q[1]], $q[2], $q[3], $q[4], $q[5], $q[6], $q[7], $q[8], $q[9], $q[10], $q[11]]);
        $questionIds[$qIdx] = ['id'=>$qid, 'ui'=>$q[1], 'type'=>$q[0]];
        $qIdx++;
    }
    // Chunk 20/20
    $qBatch = [
        ['test', 49, 'En hafif element hangisidir?', 'En hafif element hangisidir?', 'Helyum', 'Hidrojen', 'Lityum', 'Oksijen', 'Karbon', 'B', NULL, 15],
        ['klasik', 49, 'Sindirim sistemindeki organları sıralayınız.', 'Sindirim sistemindeki organları sıralayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Ağız → Yemek borusu → Mide → İnce bağırsak → Kalın bağırsak → Anüs', 10],
        ['test', 51, 'Beyin hangi sisteme aittir?', 'Beyin hangi sisteme aittir?', 'Sindirim', 'Dolaşım', 'Sinir', 'Boşaltım', 'Solunum', 'C', NULL, 5],
        ['test', 52, 'Hücrenin kontrol merkezi hangisidir?', 'Hücrenin kontrol merkezi hangisidir?', 'Mitokondri', 'Çekirdek', 'Hücre zarı', 'Kloroplast', 'Ribozom', 'B', NULL, 10],
        ['test', 52, 'En hafif element hangisidir?', 'En hafif element hangisidir?', 'Helyum', 'Hidrojen', 'Lityum', 'Oksijen', 'Karbon', 'B', NULL, 10],
        ['test', 53, 'DNA nerede bulunur?', 'DNA nerede bulunur?', 'Ribozom', 'Mitokondri', 'Çekirdek', 'Hücre zarı', 'Sitoplazma', 'C', NULL, 20],
        ['klasik', 52, 'Besin zinciri nedir? Bir örnek vererek açıklayınız.', 'Besin zinciri nedir? Bir örnek vererek açıklayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Enerji üreticiden tüketiciye aktarılır. Örnek: Ot → Tavşan → Tilki → Bakteri', 10],
        ['klasik', 55, 'Besin zinciri nedir? Bir örnek vererek açıklayınız.', 'Besin zinciri nedir? Bir örnek vererek açıklayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Enerji üreticiden tüketiciye aktarılır. Örnek: Ot → Tavşan → Tilki → Bakteri', 15],
        ['test', 57, '\'Yazmak\' fiilinin isim hali hangisidir?', '\'Yazmak\' fiilinin isim hali hangisidir?', 'Yazı', 'Yazıcı', 'Yazan', 'Yazarak', 'Yazılı', 'A', NULL, 10],
        ['test', 56, 'Türkçede kaç sesli harf vardır?', 'Türkçede kaç sesli harf vardır?', '5', '6', '7', '8', '9', 'D', NULL, 10],
        ['test', 58, 'Türkçede kaç sesli harf vardır?', 'Türkçede kaç sesli harf vardır?', '5', '6', '7', '8', '9', 'D', NULL, 15],
        ['test', 58, 'Türkçede kaç sesli harf vardır?', 'Türkçede kaç sesli harf vardır?', '5', '6', '7', '8', '9', 'D', NULL, 10],
        ['klasik', 60, 'Verilen metindeki sıfatları bulunuz ve türlerini belirtiniz.', 'Verilen metindeki sıfatları bulunuz ve türlerini belirtiniz.', NULL, NULL, NULL, NULL, NULL, NULL, 'Niteleme sıfatı: güzel. Sayı sıfatı: üç. Belgisiz sıfat: bazı.', 5],
        ['test', 59, 'Aşağıdakilerden hangisi bağlaçtır?', 'Aşağıdakilerden hangisi bağlaçtır?', 'Çok', 'Ve', 'Güzel', 'Hızla', 'Ama', 'B', NULL, 10],
        ['test', 60, 'Hangi cümle soru cümlesidir?', 'Hangi cümle soru cümlesidir?', 'Gel buraya.', 'Ne zaman geldin?', 'Çok güzeldi.', 'Hadi gidelim.', 'Dur orada.', 'B', NULL, 10],
        ['test', 64, 'Aşağıdakilerden hangisi bağlaçtır?', 'Aşağıdakilerden hangisi bağlaçtır?', 'Çok', 'Ve', 'Güzel', 'Hızla', 'Ama', 'B', NULL, 10],
        ['klasik', 63, 'Metinden çıkarılabilecek ana fikri yazınız.', 'Metinden çıkarılabilecek ana fikri yazınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Öğrenci metni okuyup ana düşünceyi ifade etmelidir.', 10],
        ['test', 65, '\'Gelmek\' fiilinin geniş zamanı hangisidir?', '\'Gelmek\' fiilinin geniş zamanı hangisidir?', 'Geldi', 'Gelecek', 'Gelir', 'Gelsin', 'Gelse', 'C', NULL, 10],
        ['klasik', 64, 'Metinden çıkarılabilecek ana fikri yazınız.', 'Metinden çıkarılabilecek ana fikri yazınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Öğrenci metni okuyup ana düşünceyi ifade etmelidir.', 10],
        ['test', 65, '\'Okul\' sözcüğü hangi türden isimdir?', '\'Okul\' sözcüğü hangi türden isimdir?', 'Özel', 'Soyut', 'Somut', 'Topluluk', 'Eylem', 'C', NULL, 20],
        ['test', 67, 'Hangi sözcük zarf değildir?', 'Hangi sözcük zarf değildir?', 'Hızlı', 'Çabuk', 'Çok', 'Kitap', 'Yavaş', 'D', NULL, 20],
        ['klasik', 69, 'Aşağıdaki sözcükleri alfabetik sıraya koyunuz.', 'Aşağıdaki sözcükleri alfabetik sıraya koyunuz.', NULL, NULL, NULL, NULL, NULL, NULL, 'Türkçe alfabesine göre a,b,c,ç,d... sırası izlenir.', 10],
        ['klasik', 71, 'Metindeki noktalama hatalarını bulunuz ve düzeltiniz.', 'Metindeki noktalama hatalarını bulunuz ve düzeltiniz.', NULL, NULL, NULL, NULL, NULL, NULL, 'Yanlış noktalama işaretleri tespit edilip düzeltilmelidir.', 20],
        ['test', 69, 'Hangi sözcük zarf değildir?', 'Hangi sözcük zarf değildir?', 'Hızlı', 'Çabuk', 'Çok', 'Kitap', 'Yavaş', 'D', NULL, 10],
        ['test', 70, 'Aşağıdaki sözcüklerden hangisi sıfattır?', 'Aşağıdaki sözcüklerden hangisi sıfattır?', 'Kitap', 'Ev', 'Güzel', 'Çalışmak', 'Ben', 'C', NULL, 10],
        ['test', 74, 'Anlatıcı bakış açısı kaç türdür?', 'Anlatıcı bakış açısı kaç türdür?', '2', '3', '4', '5', '6', 'C', NULL, 20],
        ['klasik', 74, 'Aşağıdaki cümleleri olumludan olumsuza çeviriniz.', 'Aşağıdaki cümleleri olumludan olumsuza çeviriniz.', NULL, NULL, NULL, NULL, NULL, NULL, 'Fiil olumsuzluk eki (-me/-ma) eklenerek yapılır.', 10],
        ['test', 74, 'Anlatıcı bakış açısı kaç türdür?', 'Anlatıcı bakış açısı kaç türdür?', '2', '3', '4', '5', '6', 'C', NULL, 10],
        ['test', 77, 'Hangi şehir iki kıta üzerindedir?', 'Hangi şehir iki kıta üzerindedir?', 'Ankara', 'İzmir', 'İstanbul', 'Bursa', 'Antalya', 'C', NULL, 5],
        ['test', 75, 'Hangi ülke en fazla nüfusa sahiptir?', 'Hangi ülke en fazla nüfusa sahiptir?', 'Hindistan', 'ABD', 'Rusya', 'Çin', 'Brezilya', 'D', NULL, 10],
        ['test', 77, 'İpek Yolu hangi medeniyetleri birleştirirdi?', 'İpek Yolu hangi medeniyetleri birleştirirdi?', 'Asya-Avrupa', 'Afrika-Asya', 'Amerika-Avrupa', 'Pasifik-Atlantik', 'Avrupa-Avustralya', 'A', NULL, 10],
        ['test', 78, 'En uzun ırmak hangisidir?', 'En uzun ırmak hangisidir?', 'Amazon', 'Nil', 'Mississippi', 'Yangtze', 'Volga', 'B', NULL, 15],
        ['klasik', 78, 'İpek Yolu\'nun tarihsel önemini açıklayınız.', 'İpek Yolu\'nun tarihsel önemini açıklayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Doğu-Batı ticareti, kültür ve bilgi alışverişi için köprü görevi gördü.', 10],
        ['test', 82, 'Türkiye\'nin başkenti neresidir?', 'Türkiye\'nin başkenti neresidir?', 'İstanbul', 'İzmir', 'Ankara', 'Bursa', 'Antalya', 'C', NULL, 10],
        ['klasik', 81, 'Küresel ısınmanın nedenlerini ve sonuçlarını yazınız.', 'Küresel ısınmanın nedenlerini ve sonuçlarını yazınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Neden: sera gazları. Sonuç: buzul erimesi, deniz yükselmesi, iklim değişikliği.', 15],
        ['klasik', 84, 'Cumhuriyetin ilanının önemini yazınız.', 'Cumhuriyetin ilanının önemini yazınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Milli egemenlik ilkesi yerleşti; halkın yönetime katılımı sağlandı.', 5],
        ['test', 82, 'Cumhuriyet hangi tarihte ilan edilmiştir?', 'Cumhuriyet hangi tarihte ilan edilmiştir?', '19 Mayıs', '29 Ekim', '30 Ağustos', '23 Nisan', '10 Kasım', 'B', NULL, 5],
        ['test', 86, 'Osmanlı Devleti hangi yıl kurulmuştur?', 'Osmanlı Devleti hangi yıl kurulmuştur?', '1299', '1400', '1453', '1517', '1683', 'A', NULL, 10],
        ['klasik', 86, 'Tarih öncesi dönemleri özelliklerine göre karşılaştırınız.', 'Tarih öncesi dönemleri özelliklerine göre karşılaştırınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Taş, Maden ve Tunç Çağları; yazı ve metal kullanımına göre ayrılır.', 10],
        ['klasik', 87, 'Cumhuriyetin ilanının önemini yazınız.', 'Cumhuriyetin ilanının önemini yazınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Milli egemenlik ilkesi yerleşti; halkın yönetime katılımı sağlandı.', 10],
        ['klasik', 88, 'Atatürk\'ün gerçekleştirdiği inkılapları sıralayınız.', 'Atatürk\'ün gerçekleştirdiği inkılapları sıralayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Harf, takvim, kıyafet, hukuk, eğitim, kadın hakları inkılapları.', 20],
        ['klasik', 87, 'Osmanlı Devleti\'nin yıkılış nedenlerini açıklayınız.', 'Osmanlı Devleti\'nin yıkılış nedenlerini açıklayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Savaş yenilgileri, azınlık isyanları, ekonomik çöküş, Batı\'nın müdahalesi.', 20],
        ['test', 90, 'Türkiye kaç komşuya sahiptir?', 'Türkiye kaç komşuya sahiptir?', '5', '6', '7', '8', '9', 'D', NULL, 15],
        ['klasik', 90, 'Atatürk\'ün gerçekleştirdiği inkılapları sıralayınız.', 'Atatürk\'ün gerçekleştirdiği inkılapları sıralayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Harf, takvim, kıyafet, hukuk, eğitim, kadın hakları inkılapları.', 10],
        ['klasik', 90, 'Türkiye\'deki iklim çeşitliliğinin nedenlerini açıklayınız.', 'Türkiye\'deki iklim çeşitliliğinin nedenlerini açıklayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Enlem, yükselti, denizden uzaklık ve dağların uzanışı belirleyicidir.', 10],
        ['test', 94, 'Which sentence is correct?', 'Which sentence is correct?', 'I is happy', 'He are sad', 'She is tired', 'They is late', 'We am here', 'C', NULL, 10],
        ['klasik', 95, 'Write a short letter to a pen pal introducing yourself.', 'Write a short letter to a pen pal introducing yourself.', NULL, NULL, NULL, NULL, NULL, NULL, 'Dear pen pal, My name is... I am ... years old. I live in... I like...', 5],
        ['klasik', 93, 'Demokrasinin temel ilkelerini açıklayınız.', 'Demokrasinin temel ilkelerini açıklayınız.', NULL, NULL, NULL, NULL, NULL, NULL, 'Milli egemenlik, temel haklar, hukukun üstünlüğü, çoğulculuk.', 10],
        ['test', 95, 'What does \'beautiful\' mean?', 'What does \'beautiful\' mean?', 'Çirkin', 'Küçük', 'Büyük', 'Güzel', 'Uzun', 'D', NULL, 15],
        ['test', 98, '\'We ___ to school yesterday.\' Fill in.', '\'We ___ to school yesterday.\' Fill in.', 'go', 'goes', 'gone', 'went', 'going', 'D', NULL, 20],
    ];
    foreach ($qBatch as $q) {
        $qid = ins($db, "INSERT INTO `questions` (`type`,`user_id`,`unit_id`,`title`,`content`,`option_a`,`option_b`,`option_c`,`option_d`,`option_e`,`correct_opt`,`answer`,`points`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)",
            [$q[0], $uid, $unitIds[$q[1]], $q[2], $q[3], $q[4], $q[5], $q[6], $q[7], $q[8], $q[9], $q[10], $q[11]]);
        $questionIds[$qIdx] = ['id'=>$qid, 'ui'=>$q[1], 'type'=>$q[0]];
        $qIdx++;
    }

    $log[] = count($questionIds) . " soru eklendi.";

    // ── SINAVLAR ─────────────────────────────────────────
    $log[] = "Sınavlar ekleniyor...";

    // Sınav 1: Matematik 1. Dönem 1. Yazılı
    $examId_0 = ins($db, "INSERT INTO `exams` (`user_id`,`class_id`,`name`,`duration`,`is_online`,`access_code`) VALUES (?,?,?,?,1,?)",
        [$uid, $classIds[0], 'Matematik 1. Dönem 1. Yazılı', 45, 'NGFH2J']);
    $examUnits_0 = [0,1,2,3,4,5];
    $pool_test_0    = array_filter($questionIds, fn($q) => $q['type']==='test'    && in_array($q['ui'], $examUnits_0));
    $pool_klasik_0  = array_filter($questionIds, fn($q) => $q['type']==='klasik'  && in_array($q['ui'], $examUnits_0));
    shuffle($pool_test_0);   $pool_test_0   = array_slice($pool_test_0,   0, 7);
    shuffle($pool_klasik_0); $pool_klasik_0 = array_slice($pool_klasik_0, 0, 9);
    $ord_0 = 1;
    foreach (array_merge($pool_test_0, $pool_klasik_0) as $q) {
        ins($db, "INSERT IGNORE INTO `exam_questions` (`exam_id`,`question_id`,`order_num`) VALUES (?,?,?)",
            [$examId_0, $q['id'], $ord_0++]);
    }

    // Sınav 2: Matematik 1. Dönem 2. Yazılı
    $examId_1 = ins($db, "INSERT INTO `exams` (`user_id`,`class_id`,`name`,`duration`,`is_online`,`access_code`) VALUES (?,?,?,?,1,?)",
        [$uid, $classIds[1], 'Matematik 1. Dönem 2. Yazılı', 45, '8RPVG6']);
    $examUnits_1 = [6,7,8,9,10,11];
    $pool_test_1    = array_filter($questionIds, fn($q) => $q['type']==='test'    && in_array($q['ui'], $examUnits_1));
    $pool_klasik_1  = array_filter($questionIds, fn($q) => $q['type']==='klasik'  && in_array($q['ui'], $examUnits_1));
    shuffle($pool_test_1);   $pool_test_1   = array_slice($pool_test_1,   0, 11);
    shuffle($pool_klasik_1); $pool_klasik_1 = array_slice($pool_klasik_1, 0, 4);
    $ord_1 = 1;
    foreach (array_merge($pool_test_1, $pool_klasik_1) as $q) {
        ins($db, "INSERT IGNORE INTO `exam_questions` (`exam_id`,`question_id`,`order_num`) VALUES (?,?,?)",
            [$examId_1, $q['id'], $ord_1++]);
    }

    // Sınav 3: Fen Bilimleri 1. Yazılı
    $examId_2 = ins($db, "INSERT INTO `exams` (`user_id`,`class_id`,`name`,`duration`,`is_online`,`access_code`) VALUES (?,?,?,?,1,?)",
        [$uid, $classIds[6], 'Fen Bilimleri 1. Yazılı', 40, '5GQFUC']);
    $examUnits_2 = [32,33,34,35];
    $pool_test_2    = array_filter($questionIds, fn($q) => $q['type']==='test'    && in_array($q['ui'], $examUnits_2));
    $pool_klasik_2  = array_filter($questionIds, fn($q) => $q['type']==='klasik'  && in_array($q['ui'], $examUnits_2));
    shuffle($pool_test_2);   $pool_test_2   = array_slice($pool_test_2,   0, 8);
    shuffle($pool_klasik_2); $pool_klasik_2 = array_slice($pool_klasik_2, 0, 10);
    $ord_2 = 1;
    foreach (array_merge($pool_test_2, $pool_klasik_2) as $q) {
        ins($db, "INSERT IGNORE INTO `exam_questions` (`exam_id`,`question_id`,`order_num`) VALUES (?,?,?)",
            [$examId_2, $q['id'], $ord_2++]);
    }

    // Sınav 4: Fen Bilimleri 2. Yazılı
    $examId_3 = ins($db, "INSERT INTO `exams` (`user_id`,`class_id`,`name`,`duration`,`is_online`,`access_code`) VALUES (?,?,?,?,1,?)",
        [$uid, $classIds[7], 'Fen Bilimleri 2. Yazılı', 40, 'RNJAG9']);
    $examUnits_3 = [36,37,38,39,40,41];
    $pool_test_3    = array_filter($questionIds, fn($q) => $q['type']==='test'    && in_array($q['ui'], $examUnits_3));
    $pool_klasik_3  = array_filter($questionIds, fn($q) => $q['type']==='klasik'  && in_array($q['ui'], $examUnits_3));
    shuffle($pool_test_3);   $pool_test_3   = array_slice($pool_test_3,   0, 8);
    shuffle($pool_klasik_3); $pool_klasik_3 = array_slice($pool_klasik_3, 0, 10);
    $ord_3 = 1;
    foreach (array_merge($pool_test_3, $pool_klasik_3) as $q) {
        ins($db, "INSERT IGNORE INTO `exam_questions` (`exam_id`,`question_id`,`order_num`) VALUES (?,?,?)",
            [$examId_3, $q['id'], $ord_3++]);
    }

    // Sınav 5: Türkçe 1. Dönem Yazılı
    $examId_4 = ins($db, "INSERT INTO `exams` (`user_id`,`class_id`,`name`,`duration`,`is_online`,`access_code`) VALUES (?,?,?,?,1,?)",
        [$uid, $classIds[11], 'Türkçe 1. Dönem Yazılı', 50, 'Q6V9MG']);
    $examUnits_4 = [56,57,58,59,60,61];
    $pool_test_4    = array_filter($questionIds, fn($q) => $q['type']==='test'    && in_array($q['ui'], $examUnits_4));
    $pool_klasik_4  = array_filter($questionIds, fn($q) => $q['type']==='klasik'  && in_array($q['ui'], $examUnits_4));
    shuffle($pool_test_4);   $pool_test_4   = array_slice($pool_test_4,   0, 9);
    shuffle($pool_klasik_4); $pool_klasik_4 = array_slice($pool_klasik_4, 0, 3);
    $ord_4 = 1;
    foreach (array_merge($pool_test_4, $pool_klasik_4) as $q) {
        ins($db, "INSERT IGNORE INTO `exam_questions` (`exam_id`,`question_id`,`order_num`) VALUES (?,?,?)",
            [$examId_4, $q['id'], $ord_4++]);
    }

    // Sınav 6: Türkçe 2. Dönem Yazılı
    $examId_5 = ins($db, "INSERT INTO `exams` (`user_id`,`class_id`,`name`,`duration`,`is_online`,`access_code`) VALUES (?,?,?,?,1,?)",
        [$uid, $classIds[12], 'Türkçe 2. Dönem Yazılı', 50, '3V98XB']);
    $examUnits_5 = [62,63,64,65,66];
    $pool_test_5    = array_filter($questionIds, fn($q) => $q['type']==='test'    && in_array($q['ui'], $examUnits_5));
    $pool_klasik_5  = array_filter($questionIds, fn($q) => $q['type']==='klasik'  && in_array($q['ui'], $examUnits_5));
    shuffle($pool_test_5);   $pool_test_5   = array_slice($pool_test_5,   0, 4);
    shuffle($pool_klasik_5); $pool_klasik_5 = array_slice($pool_klasik_5, 0, 6);
    $ord_5 = 1;
    foreach (array_merge($pool_test_5, $pool_klasik_5) as $q) {
        ins($db, "INSERT IGNORE INTO `exam_questions` (`exam_id`,`question_id`,`order_num`) VALUES (?,?,?)",
            [$examId_5, $q['id'], $ord_5++]);
    }

    // Sınav 7: Sosyal Bilgiler 1. Yazılı
    $examId_6 = ins($db, "INSERT INTO `exams` (`user_id`,`class_id`,`name`,`duration`,`is_online`,`access_code`) VALUES (?,?,?,?,1,?)",
        [$uid, $classIds[15], 'Sosyal Bilgiler 1. Yazılı', 35, 'R4CPCK']);
    $examUnits_6 = [75,76,77,78];
    $pool_test_6    = array_filter($questionIds, fn($q) => $q['type']==='test'    && in_array($q['ui'], $examUnits_6));
    $pool_klasik_6  = array_filter($questionIds, fn($q) => $q['type']==='klasik'  && in_array($q['ui'], $examUnits_6));
    shuffle($pool_test_6);   $pool_test_6   = array_slice($pool_test_6,   0, 6);
    shuffle($pool_klasik_6); $pool_klasik_6 = array_slice($pool_klasik_6, 0, 4);
    $ord_6 = 1;
    foreach (array_merge($pool_test_6, $pool_klasik_6) as $q) {
        ins($db, "INSERT IGNORE INTO `exam_questions` (`exam_id`,`question_id`,`order_num`) VALUES (?,?,?)",
            [$examId_6, $q['id'], $ord_6++]);
    }

    // Sınav 8: Sosyal Bilgiler 2. Yazılı
    $examId_7 = ins($db, "INSERT INTO `exams` (`user_id`,`class_id`,`name`,`duration`,`is_online`,`access_code`) VALUES (?,?,?,?,1,?)",
        [$uid, $classIds[16], 'Sosyal Bilgiler 2. Yazılı', 35, 'LSG3BG']);
    $examUnits_7 = [79,80,81,82];
    $pool_test_7    = array_filter($questionIds, fn($q) => $q['type']==='test'    && in_array($q['ui'], $examUnits_7));
    $pool_klasik_7  = array_filter($questionIds, fn($q) => $q['type']==='klasik'  && in_array($q['ui'], $examUnits_7));
    shuffle($pool_test_7);   $pool_test_7   = array_slice($pool_test_7,   0, 9);
    shuffle($pool_klasik_7); $pool_klasik_7 = array_slice($pool_klasik_7, 0, 5);
    $ord_7 = 1;
    foreach (array_merge($pool_test_7, $pool_klasik_7) as $q) {
        ins($db, "INSERT IGNORE INTO `exam_questions` (`exam_id`,`question_id`,`order_num`) VALUES (?,?,?)",
            [$examId_7, $q['id'], $ord_7++]);
    }

    // Sınav 9: İngilizce 1. Yazılı
    $examId_8 = ins($db, "INSERT INTO `exams` (`user_id`,`class_id`,`name`,`duration`,`is_online`,`access_code`) VALUES (?,?,?,?,1,?)",
        [$uid, $classIds[19], 'İngilizce 1. Yazılı', 40, 'N2MY7E']);
    $examUnits_8 = [94,95,96,97];
    $pool_test_8    = array_filter($questionIds, fn($q) => $q['type']==='test'    && in_array($q['ui'], $examUnits_8));
    $pool_klasik_8  = array_filter($questionIds, fn($q) => $q['type']==='klasik'  && in_array($q['ui'], $examUnits_8));
    shuffle($pool_test_8);   $pool_test_8   = array_slice($pool_test_8,   0, 9);
    shuffle($pool_klasik_8); $pool_klasik_8 = array_slice($pool_klasik_8, 0, 10);
    $ord_8 = 1;
    foreach (array_merge($pool_test_8, $pool_klasik_8) as $q) {
        ins($db, "INSERT IGNORE INTO `exam_questions` (`exam_id`,`question_id`,`order_num`) VALUES (?,?,?)",
            [$examId_8, $q['id'], $ord_8++]);
    }

    // Sınav 10: İngilizce 2. Yazılı
    $examId_9 = ins($db, "INSERT INTO `exams` (`user_id`,`class_id`,`name`,`duration`,`is_online`,`access_code`) VALUES (?,?,?,?,1,?)",
        [$uid, $classIds[20], 'İngilizce 2. Yazılı', 40, 'VPDEFF']);
    $examUnits_9 = [98,99,100,101];
    $pool_test_9    = array_filter($questionIds, fn($q) => $q['type']==='test'    && in_array($q['ui'], $examUnits_9));
    $pool_klasik_9  = array_filter($questionIds, fn($q) => $q['type']==='klasik'  && in_array($q['ui'], $examUnits_9));
    shuffle($pool_test_9);   $pool_test_9   = array_slice($pool_test_9,   0, 5);
    shuffle($pool_klasik_9); $pool_klasik_9 = array_slice($pool_klasik_9, 0, 7);
    $ord_9 = 1;
    foreach (array_merge($pool_test_9, $pool_klasik_9) as $q) {
        ins($db, "INSERT IGNORE INTO `exam_questions` (`exam_id`,`question_id`,`order_num`) VALUES (?,?,?)",
            [$examId_9, $q['id'], $ord_9++]);
    }

    // Sınav 11: Matematik Genel Deneme
    $examId_10 = ins($db, "INSERT INTO `exams` (`user_id`,`class_id`,`name`,`duration`,`is_online`,`access_code`) VALUES (?,?,?,?,1,?)",
        [$uid, $classIds[2], 'Matematik Genel Deneme', 60, '6LRHJ5']);
    $examUnits_10 = [12,13,14,15];
    $pool_test_10    = array_filter($questionIds, fn($q) => $q['type']==='test'    && in_array($q['ui'], $examUnits_10));
    $pool_klasik_10  = array_filter($questionIds, fn($q) => $q['type']==='klasik'  && in_array($q['ui'], $examUnits_10));
    shuffle($pool_test_10);   $pool_test_10   = array_slice($pool_test_10,   0, 7);
    shuffle($pool_klasik_10); $pool_klasik_10 = array_slice($pool_klasik_10, 0, 8);
    $ord_10 = 1;
    foreach (array_merge($pool_test_10, $pool_klasik_10) as $q) {
        ins($db, "INSERT IGNORE INTO `exam_questions` (`exam_id`,`question_id`,`order_num`) VALUES (?,?,?)",
            [$examId_10, $q['id'], $ord_10++]);
    }

    // Sınav 12: Fen Bilimleri Genel Deneme
    $examId_11 = ins($db, "INSERT INTO `exams` (`user_id`,`class_id`,`name`,`duration`,`is_online`,`access_code`) VALUES (?,?,?,?,1,?)",
        [$uid, $classIds[8], 'Fen Bilimleri Genel Deneme', 60, 'XSA4LN']);
    $examUnits_11 = [42,43,44,45,46,47];
    $pool_test_11    = array_filter($questionIds, fn($q) => $q['type']==='test'    && in_array($q['ui'], $examUnits_11));
    $pool_klasik_11  = array_filter($questionIds, fn($q) => $q['type']==='klasik'  && in_array($q['ui'], $examUnits_11));
    shuffle($pool_test_11);   $pool_test_11   = array_slice($pool_test_11,   0, 11);
    shuffle($pool_klasik_11); $pool_klasik_11 = array_slice($pool_klasik_11, 0, 4);
    $ord_11 = 1;
    foreach (array_merge($pool_test_11, $pool_klasik_11) as $q) {
        ins($db, "INSERT IGNORE INTO `exam_questions` (`exam_id`,`question_id`,`order_num`) VALUES (?,?,?)",
            [$examId_11, $q['id'], $ord_11++]);
    }

    // Sınav 13: Türkçe Genel Deneme
    $examId_12 = ins($db, "INSERT INTO `exams` (`user_id`,`class_id`,`name`,`duration`,`is_online`,`access_code`) VALUES (?,?,?,?,1,?)",
        [$uid, $classIds[13], 'Türkçe Genel Deneme', 60, 'N4TFBM']);
    $examUnits_12 = [67,68,69,70];
    $pool_test_12    = array_filter($questionIds, fn($q) => $q['type']==='test'    && in_array($q['ui'], $examUnits_12));
    $pool_klasik_12  = array_filter($questionIds, fn($q) => $q['type']==='klasik'  && in_array($q['ui'], $examUnits_12));
    shuffle($pool_test_12);   $pool_test_12   = array_slice($pool_test_12,   0, 13);
    shuffle($pool_klasik_12); $pool_klasik_12 = array_slice($pool_klasik_12, 0, 3);
    $ord_12 = 1;
    foreach (array_merge($pool_test_12, $pool_klasik_12) as $q) {
        ins($db, "INSERT IGNORE INTO `exam_questions` (`exam_id`,`question_id`,`order_num`) VALUES (?,?,?)",
            [$examId_12, $q['id'], $ord_12++]);
    }

    // Sınav 14: Sosyal Bilgiler Genel Deneme
    $examId_13 = ins($db, "INSERT INTO `exams` (`user_id`,`class_id`,`name`,`duration`,`is_online`,`access_code`) VALUES (?,?,?,?,1,?)",
        [$uid, $classIds[17], 'Sosyal Bilgiler Genel Deneme', 60, 'H5HZDY']);
    $examUnits_13 = [83,84,85,86,87];
    $pool_test_13    = array_filter($questionIds, fn($q) => $q['type']==='test'    && in_array($q['ui'], $examUnits_13));
    $pool_klasik_13  = array_filter($questionIds, fn($q) => $q['type']==='klasik'  && in_array($q['ui'], $examUnits_13));
    shuffle($pool_test_13);   $pool_test_13   = array_slice($pool_test_13,   0, 8);
    shuffle($pool_klasik_13); $pool_klasik_13 = array_slice($pool_klasik_13, 0, 7);
    $ord_13 = 1;
    foreach (array_merge($pool_test_13, $pool_klasik_13) as $q) {
        ins($db, "INSERT IGNORE INTO `exam_questions` (`exam_id`,`question_id`,`order_num`) VALUES (?,?,?)",
            [$examId_13, $q['id'], $ord_13++]);
    }

    // Sınav 15: İngilizce Genel Deneme
    $examId_14 = ins($db, "INSERT INTO `exams` (`user_id`,`class_id`,`name`,`duration`,`is_online`,`access_code`) VALUES (?,?,?,?,1,?)",
        [$uid, $classIds[21], 'İngilizce Genel Deneme', 60, '2YYNQ8']);
    $examUnits_14 = [102,103,104,105,106,107];
    $pool_test_14    = array_filter($questionIds, fn($q) => $q['type']==='test'    && in_array($q['ui'], $examUnits_14));
    $pool_klasik_14  = array_filter($questionIds, fn($q) => $q['type']==='klasik'  && in_array($q['ui'], $examUnits_14));
    shuffle($pool_test_14);   $pool_test_14   = array_slice($pool_test_14,   0, 12);
    shuffle($pool_klasik_14); $pool_klasik_14 = array_slice($pool_klasik_14, 0, 5);
    $ord_14 = 1;
    foreach (array_merge($pool_test_14, $pool_klasik_14) as $q) {
        ins($db, "INSERT IGNORE INTO `exam_questions` (`exam_id`,`question_id`,`order_num`) VALUES (?,?,?)",
            [$examId_14, $q['id'], $ord_14++]);
    }

    $db->commit();
    $log[] = "✅ Tüm veriler başarıyla eklendi!";

} catch (Exception $e) {
    $db->rollBack();
    $errors[] = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="tr">
<head><meta charset="UTF-8"><title>Test Veri Kurulumu</title>
<style>
  body{font-family:monospace;background:#0f172a;color:#e2e8f0;padding:2rem}
  .ok{color:#4ade80}.err{color:#f87171}
  pre{background:#1e293b;padding:1rem;border-radius:.5rem;line-height:1.8}
</style></head>
<body>
<h2>SınıfPro — Test Veri Kurulumu</h2>
<pre>
<?php foreach($log as $l) echo "<span class=\"ok\">✓</span> $l\n"; ?>
<?php foreach($errors as $e) echo "<span class=\"err\">✗ HATA: $e</span>\n"; ?>
</pre>
<?php if(!$errors): ?>
<p class="ok" style="font-size:1.2rem">🎉 Kurulum tamamlandı! Bu dosyayı (test_data_yukle.php) silebilirsiniz.</p>
<?php else: ?>
<p class="err">Kurulum başarısız. Hata mesajını kontrol edin.</p>
<?php endif; ?>
</body></html>
