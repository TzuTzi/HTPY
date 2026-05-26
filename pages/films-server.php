<?php
/* ============================================================
   Cerința 4 — Paginare Server-Side (fără JavaScript)
   Navigare exclusiv prin linkuri PHP cu parametri GET
   ============================================================ */
require_once __DIR__ . '/../php/auth.php';
require_login('../login.php');

$K      = 3;
$page   = max(1, (int)($_GET['page'] ?? 1));
$offset = ($page - 1) * $K;

$db         = get_db();
$total      = (int)$db->query('SELECT COUNT(*) FROM films')->fetchColumn();
$totalPages = max(1, (int)ceil($total / $K));
$page       = min($page, $totalPages);
$offset     = ($page - 1) * $K;

$stmt = $db->prepare(
    'SELECT id, title, year, genre, rating, mood FROM films ORDER BY id LIMIT ? OFFSET ?'
);
$stmt->execute([$K, $offset]);
$films = $stmt->fetchAll();

$pageTitle = 'C4 – Server-Side Pagination';
require_once __DIR__ . '/../php/header.php';
?>

<table class="section-panel">
  <tr><td>
    <table class="section-inner">
      <tr class="section-head-row">
        <td>
          <h2>Cerința 4 — Server-Side Pagination</h2>
          <small style="color:var(--color-text-muted);font-weight:400;">
            Paginare exclusiv PHP, fără JavaScript, câte <?= $K ?> înregistrări pe pagină
          </small>
        </td>
      </tr>
      <tr><td>

        <?php if ($total === 0): ?>
          <p style="color:var(--color-text-muted);">No films found. Add some from your <a href="../profile.php" style="color:var(--color-blue);">profile</a>.</p>
        <?php else: ?>

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
              <tbody>
                <?php foreach ($films as $f): ?>
                  <tr style="border-bottom:1px solid var(--color-border);">
                    <td style="padding:0.5rem 0.75rem;"><?= (int)$f['id'] ?></td>
                    <td style="padding:0.5rem 0.75rem;"><?= htmlspecialchars($f['title']) ?></td>
                    <td style="padding:0.5rem 0.75rem;"><?= (int)$f['year'] ?></td>
                    <td style="padding:0.5rem 0.75rem;"><?= htmlspecialchars($f['genre']  ?? '–') ?></td>
                    <td style="padding:0.5rem 0.75rem;"><?= htmlspecialchars((string)($f['rating'] ?? '–')) ?></td>
                    <td style="padding:0.5rem 0.75rem;"><?= htmlspecialchars($f['mood']   ?? '–') ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>

          <div style="margin-top:1rem;display:flex;gap:0.75rem;align-items:center;">
            <?php if ($page <= 1): ?>
              <button class="btn-secondary" disabled>← Previous <?= $K ?></button>
            <?php else: ?>
              <a href="films-server.php?page=<?= $page - 1 ?>" class="btn-secondary"
                 style="text-decoration:none;">← Previous <?= $K ?></a>
            <?php endif; ?>

            <span style="color:var(--color-text-muted);font-size:0.875rem;">
              Page <?= $page ?> / <?= $totalPages ?> &nbsp;(<?= $total ?> total)
            </span>

            <?php if ($page >= $totalPages): ?>
              <button class="btn-secondary" disabled>Next <?= $K ?> →</button>
            <?php else: ?>
              <a href="films-server.php?page=<?= $page + 1 ?>" class="btn-secondary"
                 style="text-decoration:none;">Next <?= $K ?> →</a>
            <?php endif; ?>
          </div>

        <?php endif; ?>

        <p style="margin-top:1rem;">
          <a href="lab.php" style="color:var(--color-blue);font-size:0.875rem;">← Back to Lab</a>
        </p>

      </td></tr>
    </table>
  </td></tr>
</table>

<?php require_once __DIR__ . '/../php/footer.php'; ?>
