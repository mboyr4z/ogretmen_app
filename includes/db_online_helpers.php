<?php
// Bu fonksiyonları includes/db.php dosyasının SONUNA ekleyin

// Türkiye saatini zorla
if (!ini_get('date.timezone') || ini_get('date.timezone') !== 'Europe/Istanbul') {
    date_default_timezone_set('Europe/Istanbul');
}

function generateAccessCode() {
    return strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));
}

function getExamByCode($code) {
    $db = getDB();
    $s  = $db->prepare("SELECT * FROM exams WHERE access_code = ?");
    $s->execute([$code]);
    return $s->fetch();
}

function isExamOpen($exam) {
    if (!$exam['is_online']) return false;

    // MySQL NOW() ile karşılaştır — timezone sorununu ortadan kaldırır
    $db  = getDB();
    $sql = "SELECT 1 FROM exams WHERE id = ?
            AND is_online = 1
            AND (start_time IS NULL OR start_time <= NOW())
            AND (end_time   IS NULL OR end_time   >= NOW())";
    $s = $db->prepare($sql);
    $s->execute([$exam['id']]);
    return (bool)$s->fetchColumn();
}

function isExamEnded($exam) {
    if (!$exam['is_online'] || !$exam['end_time']) return false;
    $db = getDB();
    $s  = $db->prepare("SELECT end_time < NOW() FROM exams WHERE id=?");
    $s->execute([$exam['id']]);
    return (bool)$s->fetchColumn();
}

function getSessionAnswers($sessionId) {
    $db = getDB();
    $s  = $db->prepare("SELECT ea.*, q.type, q.correct_opt, q.points, q.title
                         FROM exam_answers ea
                         JOIN questions q ON q.id = ea.question_id
                         WHERE ea.session_id = ?
                         ORDER BY q.id");
    $s->execute([$sessionId]);
    return $s->fetchAll();
}

function calcTestScore($sessionId) {
    $db = getDB();
    $s  = $db->prepare("
        SELECT SUM(CASE WHEN ea.is_correct = 1 THEN q.points ELSE 0 END) as score
        FROM exam_answers ea
        JOIN questions q ON q.id = ea.question_id
        WHERE ea.session_id = ? AND q.type = 'test'
    ");
    $s->execute([$sessionId]);
    return (float)($s->fetchColumn() ?? 0);
}
