<?php
/* ============================================================
   Cerința 2 — Paginare cu Vanilla JS + AJAX XML
   Backend: api/films.php?format=xml&page=X&k=3
   ============================================================ */
require_once __DIR__ . '/../php/auth.php';
require_login('../login.php');
$pageTitle = 'C2 – Vanilla JS + XML';
require_once __DIR__ . '/../php/header.php';
?>

<table class="section-panel">
  <tr><td>
    <table class="section-inner">
      <tr class="section-head-row">
        <td>
          <h2>Cerința 2 — Vanilla JS + XML</h2>
          <small style="color:var(--color-text-muted);font-weight:400;">
            AJAX cu XMLHttpRequest, date în format XML, câte 3 înregistrări pe pagină
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
/* ── Cerința 2: Vanilla JS + AJAX XML ─────────────────────── */
const K = 3;
let currentPage = 1;

function getText(node, tag) {
    var el = node.getElementsByTagName(tag)[0];
    return el ? el.textContent : '–';
}

function loadPage(page) {
    document.getElementById('status').textContent = 'Loading…';
    const xhr = new XMLHttpRequest();
    xhr.open('GET', '../api/films.php?format=xml&page=' + page + '&k=' + K, true);
    xhr.onload = function () {
        if (xhr.status !== 200) {
            document.getElementById('status').textContent = 'Error: ' + xhr.status;
            return;
        }
        const xml        = xhr.responseXML;
        const root       = xml.documentElement;
        const total      = parseInt(root.getAttribute('total'), 10);
        const filmNodes  = xml.getElementsByTagName('film');
        const tbody      = document.getElementById('films-body');
        tbody.innerHTML  = '';

        if (filmNodes.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" style="padding:0.75rem;color:var(--color-text-muted);">No films found.</td></tr>';
        } else {
            Array.from(filmNodes).forEach(function (f) {
                const tr = document.createElement('tr');
                tr.style.borderBottom = '1px solid var(--color-border)';
                tr.innerHTML =
                    '<td style="padding:0.5rem 0.75rem;">' + getText(f, 'id')     + '</td>' +
                    '<td style="padding:0.5rem 0.75rem;">' + getText(f, 'title')  + '</td>' +
                    '<td style="padding:0.5rem 0.75rem;">' + getText(f, 'year')   + '</td>' +
                    '<td style="padding:0.5rem 0.75rem;">' + getText(f, 'genre')  + '</td>' +
                    '<td style="padding:0.5rem 0.75rem;">' + getText(f, 'rating') + '</td>' +
                    '<td style="padding:0.5rem 0.75rem;">' + getText(f, 'mood')   + '</td>';
                tbody.appendChild(tr);
            });
        }

        const totalPages = Math.max(1, Math.ceil(total / K));
        currentPage = page;
        document.getElementById('page-info').textContent =
            'Page ' + currentPage + ' / ' + totalPages + '  (' + total + ' total)';
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
