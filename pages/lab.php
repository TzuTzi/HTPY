<?php
require_once __DIR__ . '/../php/auth.php';
require_login('../login.php');
$pageTitle = 'Lab – AJAX & Pagination';
require_once __DIR__ . '/../php/header.php';
?>

<table class="section-panel">
  <tr><td>
    <table class="section-inner">
      <tr class="section-head-row">
        <td><h2>Lab – AJAX &amp; Pagination</h2></td>
      </tr>
      <tr><td>
        <table style="width:100%;border-collapse:collapse;">
          <tr style="border-bottom:1px solid var(--color-border);">
            <td style="padding:0.6rem;font-weight:600;">Cerința</td>
            <td style="padding:0.6rem;font-weight:600;">Descriere</td>
            <td style="padding:0.6rem;font-weight:600;">Link</td>
          </tr>
          <tr style="border-bottom:1px solid var(--color-border);">
            <td style="padding:0.6rem;">1</td>
            <td style="padding:0.6rem;">Paginare – Vanilla JS + AJAX JSON</td>
            <td style="padding:0.6rem;"><a href="films-json.php" style="color:var(--color-blue);">films-json.php</a></td>
          </tr>
          <tr style="border-bottom:1px solid var(--color-border);">
            <td style="padding:0.6rem;">2</td>
            <td style="padding:0.6rem;">Paginare – Vanilla JS + AJAX XML</td>
            <td style="padding:0.6rem;"><a href="films-xml.php" style="color:var(--color-blue);">films-xml.php</a></td>
          </tr>
          <tr style="border-bottom:1px solid var(--color-border);">
            <td style="padding:0.6rem;">3</td>
            <td style="padding:0.6rem;">Paginare – jQuery + AJAX JSON</td>
            <td style="padding:0.6rem;"><a href="films-jquery.php" style="color:var(--color-blue);">films-jquery.php</a></td>
          </tr>
          <tr style="border-bottom:1px solid var(--color-border);">
            <td style="padding:0.6rem;">4</td>
            <td style="padding:0.6rem;">Paginare – Server-side (fără JS)</td>
            <td style="padding:0.6rem;"><a href="films-server.php" style="color:var(--color-blue);">films-server.php</a></td>
          </tr>
          <tr style="border-bottom:1px solid var(--color-border);">
            <td style="padding:0.6rem;">5</td>
            <td style="padding:0.6rem;">Editare film – Vanilla JS AJAX</td>
            <td style="padding:0.6rem;"><a href="films-edit.php" style="color:var(--color-blue);">films-edit.php</a></td>
          </tr>
          <tr>
            <td style="padding:0.6rem;">6</td>
            <td style="padding:0.6rem;">Editare film – jQuery AJAX</td>
            <td style="padding:0.6rem;"><a href="films-edit-jquery.php" style="color:var(--color-blue);">films-edit-jquery.php</a></td>
          </tr>
        </table>
        <p style="margin-top:1rem;color:var(--color-text-muted);font-size:0.875rem;">
          Notă: adaugă mai întâi câteva filme din
          <a href="../profile.php" style="color:var(--color-blue);">profilul tău</a>
          pentru a putea vedea paginarea.
        </p>
      </td></tr>
    </table>
  </td></tr>
</table>

<?php require_once __DIR__ . '/../php/footer.php'; ?>
