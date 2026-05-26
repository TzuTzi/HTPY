<?php
/* ============================================================
   api/film.php  –  Single film CRUD
   GET ?action=list          → [{id, title}, ...]  (for <select>)
   GET ?action=get&id=X      → {id, title, year, genre, rating, mood}
   POST (JSON body)          → update film, returns {success: bool}
   ============================================================ */
require_once __DIR__ . '/../php/auth.php';
require_login('../login.php');

header('Content-Type: application/json; charset=utf-8');
$db = get_db();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = $_GET['action'] ?? 'get';

    if ($action === 'list') {
        $rows = $db->query('SELECT id, title FROM films ORDER BY id')->fetchAll();
        echo json_encode($rows);
        exit;
    }

    $id = (int)($_GET['id'] ?? 0);
    if (!$id) { echo json_encode(null); exit; }
    $stmt = $db->prepare(
        'SELECT id, title, year, genre, rating, mood FROM films WHERE id = ?'
    );
    $stmt->execute([$id]);
    echo json_encode($stmt->fetch() ?: null);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $d      = json_decode(file_get_contents('php://input'), true) ?? [];
    $id     = (int)($d['id']     ?? 0);
    $title  = trim($d['title']   ?? '');
    $year   = (int)($d['year']   ?? 0);
    $genre  = trim($d['genre']   ?? '');
    $rating = (float)($d['rating'] ?? 0);
    $mood   = trim($d['mood']    ?? '');

    if (!$id || !$title) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid data']);
        exit;
    }
    $db->prepare(
        'UPDATE films SET title=?, year=?, genre=?, rating=?, mood=? WHERE id=?'
    )->execute([$title, $year, $genre, $rating, $mood, $id]);

    log_activity($_SESSION['user_id'], $_SESSION['username'], 'edit_film', "id=$id");
    echo json_encode(['success' => true]);
    exit;
}

http_response_code(405);
echo json_encode(['error' => 'Method not allowed']);
