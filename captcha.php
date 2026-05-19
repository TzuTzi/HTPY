<?php
/* ============================================================
   captcha.php  –  Generează imagine CAPTCHA cu PHP GD
   Utilizare: <img src="captcha.php" alt="CAPTCHA">
   Răspunsul corect se stochează în $_SESSION['captcha_answer'].
   ============================================================ */

/* Capturăm orice output accidental (notices, warnings din fișierele
   incluse cu display_errors=ON) înainte de a trimite headerele HTTP */
ob_start();

require_once __DIR__ . '/php/auth.php';   // pornește sesiunea

regenerate_captcha();
$code = $_SESSION['captcha_answer'];

/* ── Verifică extensia GD ──────────────────────────────────
   Dacă GD nu este disponibil, returnăm o imagine placeholder  */
if (!extension_loaded('gd') || !function_exists('imagecreatetruecolor')) {
    ob_end_clean();
    header('Content-Type: image/svg+xml');
    header('Cache-Control: no-store');
    echo '<svg xmlns="http://www.w3.org/2000/svg" width="140" height="40">'
       . '<rect width="140" height="40" fill="#0e1117"/>'
       . '<text x="10" y="26" fill="#f85149" font-size="12" font-family="monospace">'
       . 'GD not loaded</text></svg>';
    exit;
}

// ── Dimensiuni imagine ──────────────────────────────────────
$width  = 140;
$height = 40;

$img = imagecreatetruecolor($width, $height);

// ── Culori ──────────────────────────────────────────────────
$bg       = imagecolorallocate($img, 14, 17, 23);
$border   = imagecolorallocate($img, 48, 54, 61);
$textCol  = imagecolorallocate($img, 200, 210, 220);
$noiseCol = imagecolorallocate($img, 40, 50, 60);

// ── Fundal ──────────────────────────────────────────────────
imagefill($img, 0, 0, $bg);
imagerectangle($img, 0, 0, $width - 1, $height - 1, $border);

// ── Zgomot (linii) ──────────────────────────────────────────
for ($i = 0; $i < 6; $i++) {
    imageline($img,
        random_int(0, $width), random_int(0, $height),
        random_int(0, $width), random_int(0, $height),
        $noiseCol
    );
}

// ── Puncte de zgomot ────────────────────────────────────────
for ($i = 0; $i < 80; $i++) {
    imagesetpixel($img, random_int(0, $width), random_int(0, $height), $noiseCol);
}

// ── Text (fiecare caracter cu offset vertical aleatoriu) ────
$charWidth = 22;
$startX    = 12;
for ($i = 0; $i < strlen($code); $i++) {
    $y = random_int(10, 20);
    imagestring($img, 5, $startX + $i * $charWidth, $y, $code[$i], $textCol);
}

// ── Trimite imaginea (golit buffer-ul de erori înainte) ─────
ob_end_clean();
header('Content-Type: image/png');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Pragma: no-cache');
imagepng($img);
imagedestroy($img);
exit;
