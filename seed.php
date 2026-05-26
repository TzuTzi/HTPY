<?php
/* ============================================================
   seed.php  –  Adaugă date de test în baza de date
   Accesează: http://localhost/HTPY/seed.php
   ȘTERGE acest fișier după ce l-ai rulat!
   ============================================================ */
require_once __DIR__ . '/php/db.php';

$db = get_db();

$films = [
    ['The Shawshank Redemption', 1994, 'Drama',     9.3, 'happy'],
    ['The Godfather',            1972, 'Crime',      9.2, 'tense'],
    ['The Dark Knight',          2008, 'Action',     9.0, 'tense'],
    ['Pulp Fiction',             1994, 'Crime',      8.9, 'excited'],
    ['Schindler\'s List',        1993, 'Drama',      8.9, 'sad'],
    ['The Lord of the Rings',    2003, 'Fantasy',    8.9, 'excited'],
    ['Forrest Gump',             1994, 'Drama',      8.8, 'happy'],
    ['Inception',                2010, 'Sci-Fi',     8.8, 'confused'],
    ['Fight Club',               1999, 'Drama',      8.8, 'tense'],
    ['Goodfellas',               1990, 'Crime',      8.7, 'excited'],
    ['The Matrix',               1999, 'Sci-Fi',     8.7, 'excited'],
    ['Interstellar',             2014, 'Sci-Fi',     8.6, 'sad'],
    ['The Silence of the Lambs', 1991, 'Thriller',   8.6, 'tense'],
    ['City of God',              2002, 'Crime',      8.6, 'sad'],
    ['Parasite',                 2019, 'Thriller',   8.5, 'tense'],
];

$stmt = $db->prepare(
    'INSERT INTO films (title, year, genre, rating, mood, user_id)
     VALUES (?, ?, ?, ?, ?, 1)'
);

$count = 0;
foreach ($films as $f) {
    $stmt->execute($f);
    $count++;
}

echo '<h2 style="font-family:sans-serif;padding:2rem;">
        ✅ ' . $count . ' filme adăugate cu succes!<br><br>
        <a href="/HTPY/pages/lab.php">→ Mergi la Lab</a><br><br>
        <strong style="color:red;">⚠️ Șterge acest fișier după rulare: seed.php</strong>
      </h2>';
