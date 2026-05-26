<?php
/* ============================================================
   api/films.php  –  Paginated film list
   GET ?format=json&page=1&k=3  → JSON
   GET ?format=xml&page=1&k=3   → XML
   ============================================================ */
require_once __DIR__ . '/../php/auth.php';
require_login('../login.php');

$format = $_GET['format'] ?? 'json';
$page   = max(1, (int)($_GET['page'] ?? 1));
$k      = max(1, (int)($_GET['k']    ?? 3));
$offset = ($page - 1) * $k;

$db    = get_db();
$total = (int)$db->query('SELECT COUNT(*) FROM films')->fetchColumn();
$stmt  = $db->prepare(
    'SELECT id, title, year, genre, rating, mood FROM films ORDER BY id LIMIT ? OFFSET ?'
);
$stmt->execute([$k, $offset]);
$films = $stmt->fetchAll();

if ($format === 'xml') {
    header('Content-Type: application/xml; charset=utf-8');
    $xml = new SimpleXMLElement('<films/>');
    $xml->addAttribute('total',  (string)$total);
    $xml->addAttribute('page',   (string)$page);
    $xml->addAttribute('k',      (string)$k);
    foreach ($films as $f) {
        $item = $xml->addChild('film');
        $item->addChild('id',     (string)$f['id']);
        $item->addChild('title',  htmlspecialchars((string)$f['title']));
        $item->addChild('year',   (string)$f['year']);
        $item->addChild('genre',  htmlspecialchars((string)($f['genre']  ?? '')));
        $item->addChild('rating', (string)($f['rating'] ?? ''));
        $item->addChild('mood',   htmlspecialchars((string)($f['mood']   ?? '')));
    }
    echo $xml->asXML();
} else {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'total' => $total,
        'page'  => $page,
        'k'     => $k,
        'films' => $films,
    ]);
}
