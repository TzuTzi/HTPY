<?php
/* ============================================================
   Cerința 1 — Paginare cu Vanilla JS + AJAX JSON
   Backend: api/films.php?format=json&page=X&k=3
   ============================================================ */
require_once __DIR__ . '/../php/auth.php';
require_login('../login.php');
$pageTitle = 'C1 – Vanilla JS + JSON';
require_once __DIR__ . '/../php/header.php';
?>

<table class="section-panel">
  <tr><td>
    <table class="section-inner">
      <tr class="section-head-row">
        <td>
          <h2>Cerința 1 — Vanilla JS + JSON</h2>
          <small style="color:var(--color-text-muted);font-weight:400;">
            AJAX cu XMLHttpRequest, date în format JSON, câte 3 înregistrări pe pagină
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
/* ── Cerința 1: Vanilla JS + AJAX JSON ─────────────────────── */
const K = 3;
let currentPage = 1;

function loadPage(page) {
    document.getElementById('status').textContent = 'Loading…';
    const xhr = new XMLHttpRequest();
    xhr.open('GET', '../api/films.php?format=json&page=' + page + '&k=' + K, true);
    xhr.onload = function () {
        if (xhr.status !== 200) {
            document.getElementById('status').textContent = 'Error: ' + xhr.status;
            return;
        }
        const data = JSON.parse(xhr.responseText);
        const tbody = document.getElementById('films-body');
        tbody.innerHTML = '';

        if (data.films.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" style="padding:0.75rem;color:var(--color-text-muted);">No films found.</td></tr>';
        } else {
            data.films.forEach(function (f) {
                const tr = document.createElement('tr');
                tr.style.borderBottom = '1px solid var(--color-border)';
                tr.innerHTML =
                    '<td style="padding:0.5rem 0.75rem;">' + f.id + '</td>' +
                    '<td style="padding:0.5rem 0.75rem;">' + f.title + '</td>' +
                    '<td style="padding:0.5rem 0.75rem;">' + f.year + '</td>' +
                    '<td style="padding:0.5rem 0.75rem;">' + (f.genre  || '–') + '</td>' +
                    '<td style="padding:0.5rem 0.75rem;">' + (f.rating || '–') + '</td>' +
                    '<td style="padding:0.5rem 0.75rem;">' + (f.mood   || '–') + '</td>';
                tbody.appendChild(tr);
            });
        }

        const totalPages = Math.max(1, Math.ceil(data.total / K));
        currentPage = page;
        document.getElementById('page-info').textContent =
            'Page ' + currentPage + ' / ' + totalPages + '  (' + data.total + ' total)';
        document.getElementById('btn-prev').disabled = (currentPage <= 1);
        document.getElementById('btn-next').disabled = (currentPage >= totalPages);
        document.getElementById('status').textContent = '';
    };
    xhr.onerror = function () {
        document.getElementById('status').textContent = 'Network error.';
    };
    xhr.send();
}

document.getElementById('btn-prev').addEventListener('click', function () {
    if (currentPage > 1) loadPage(currentPage - 1);
});
document.getElementById('btn-next').addEventListener('click', function () {
    loadPage(currentPage + 1);
});

loadPage(1);
</script>

<?php require_once __DIR__ . '/../php/footer.php'; ?>
