<?php
/* ============================================================
   Cerința 3 — Paginare cu jQuery + AJAX JSON
   Backend: api/films.php?format=json&page=X&k=3
   ============================================================ */
require_once __DIR__ . '/../php/auth.php';
require_login('../login.php');
$pageTitle = 'C3 – jQuery + JSON';
require_once __DIR__ . '/../php/header.php';
?>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<table class="section-panel">
  <tr><td>
    <table class="section-inner">
      <tr class="section-head-row">
        <td>
          <h2>Cerința 3 — jQuery + JSON</h2>
          <small style="color:var(--color-text-muted);font-weight:400;">
            AJAX cu jQuery $.ajax(), date în format JSON, câte 3 înregistrări pe pagină
          </small>
        </td>
      </tr>
      <tr><td>

        <div id="status" style="color:var(--color-text-muted);font-size:0.875rem;margin-bottom:0.5rem;"></div>

        <div style="overflow-x:auto;">
          <table style="width:100%;border-collapse:collapse;min-width:420px;">
            <thead>
              <tr style="background:var(--color-surface);border-bottom:2px solid var(--color-border);">
                <th style="padding:0.5rem 0.75rem;text-align:left;">ID</th>
                <th style="padding:0.5rem 0.75rem;text-align:left;">Title</th>
                <th style="padding:0.5rem 0.75rem;text-align:left;">Year</th>
                <th style="padding:0.5rem 0.75rem;text-align:left;">Genre</th>
                <th style="padding:0.5rem 0.75rem;text-align:left;">Rating</th>
                <th style="padding:0.5rem 0.75rem;text-align:left;">Mood</th>
              </tr>
            </thead>
            <tbody id="films-body">
              <tr><td colspan="6" style="padding:0.75rem;color:var(--color-text-muted);">Loading…</td></tr>
            </tbody>
          </table>
        </div>

        <div style="margin-top:1rem;display:flex;gap:0.75rem;align-items:center;">
          <button id="btn-prev" class="btn-secondary" disabled>← Previous 3</button>
          <span id="page-info" style="color:var(--color-text-muted);font-size:0.875rem;">Page 1</span>
          <button id="btn-next" class="btn-secondary">Next 3 →</button>
        </div>
        <p style="margin-top:1rem;">
          <a href="lab.php" style="color:var(--color-blue);font-size:0.875rem;">← Back to Lab</a>
        </p>

      </td></tr>
    </table>
  </td></tr>
</table>

<script>
/* ── Cerința 3: jQuery + AJAX JSON ─────────────────────────── */
var K = 3;
var currentPage = 1;

function loadPage(page) {
    $('#status').text('Loading…');
    $.ajax({
        url: '../api/films.php',
        method: 'GET',
        data: { format: 'json', page: page, k: K },
        dataType: 'json',
        success: function (data) {
            var $tbody = $('#films-body');
            $tbody.empty();

            if (data.films.length === 0) {
                $tbody.append('<tr><td colspan="6" style="padding:0.75rem;color:var(--color-text-muted);">No films found.</td></tr>');
            } else {
                $.each(data.films, function (i, f) {
                    var $tr = $('<tr>').css('border-bottom', '1px solid var(--color-border)');
                    $tr.append($('<td>').css('padding', '0.5rem 0.75rem').text(f.id));
                    $tr.append($('<td>').css('padding', '0.5rem 0.75rem').text(f.title));
                    $tr.append($('<td>').css('padding', '0.5rem 0.75rem').text(f.year));
                    $tr.append($('<td>').css('padding', '0.5rem 0.75rem').text(f.genre  || '–'));
                    $tr.append($('<td>').css('padding', '0.5rem 0.75rem').text(f.rating || '–'));
                    $tr.append($('<td>').css('padding', '0.5rem 0.75rem').text(f.mood   || '–'));
                    $tbody.append($tr);
                });
            }

            var totalPages = Math.max(1, Math.ceil(data.total / K));
            currentPage = page;
            $('#page-info').text('Page ' + currentPage + ' / ' + totalPages + '  (' + data.total + ' total)');
            $('#btn-prev').prop('disabled', currentPage <= 1);
            $('#btn-next').prop('disabled', currentPage >= totalPages);
            $('#status').text('');
        },
        error: function () {
            $('#status').text('AJAX error.');
        }
    });
}

$('#btn-prev').on('click', function () {
    if (currentPage > 1) loadPage(currentPage - 1);
});
$('#btn-next').on('click', function () {
    loadPage(currentPage + 1);
});

$(document).ready(function () {
    loadPage(1);
});
</script>

<?php require_once __DIR__ . '/../php/footer.php'; ?>
