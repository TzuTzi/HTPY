<?php
/* ============================================================
   widgets.php  –  Dashboard: carduri statistici, feed
                   activitate, tabele sortabile.
   Demonstrează CSS-ul responsive (animații scaleIn, etc.).
   ============================================================ */

require_once __DIR__ . '/php/auth.php';

$pageTitle = 'Dashboard';
$extraCss  = 'css/style-responsive.css';
require_once __DIR__ . '/php/header.php';
?>

<!-- ── Carousel (same as welcome page) ──────────────────── -->
<table class="section-panel">
    <tr>
        <td style="padding:0 !important;">
            <div id="carousel">
                <div class="carousel-bg-gradient"></div>
                <div class="carousel-content">
                    <div id="carousel-genre"></div>
                    <img id="carousel-image" class="carousel-poster" src="" alt="" style="display:none;">
                    <a id="carousel-link" href="pages/featured-html5.html">
                        <h2 id="carousel-title"></h2>
                    </a>
                    <div id="carousel-year"></div>
                </div>
                <button id="carousel-prev" class="carousel-btn" aria-label="Previous">&#8592;</button>
                <button id="carousel-next" class="carousel-btn" aria-label="Next">&#8594;</button>
                <div id="carousel-dots"></div>
                <div class="carousel-bar-container">
                    <div id="carousel-bar"></div>
                </div>
            </div>
        </td>
    </tr>
</table>

<!-- ── Stat cards ─────────────────────────────────────────── -->
<table class="section-panel">
    <tr><td>
        <table class="section-inner">
            <tr class="section-head-row">
                <td><h2>Overview</h2></td>
                <td class="view-all-cell">
                    <?php if (is_logged_in()): ?>
                        <a href="profile.php" class="view-all-link">My Profile &rarr;</a>
                    <?php endif; ?>
                </td>
            </tr>
            <tr><td colspan="2">
                <table class="stats-grid">
                    <tr>
                        <td class="stat-card">
                            <span class="stat-icon">&#127909;</span>
                            <span class="stat-number">1,247</span>
                            <span class="stat-label">Films Watched</span>
                            <span class="stat-trend up">&#8679; +23 this month</span>
                        </td>
                        <td class="stat-card">
                            <span class="stat-icon">&#11088;</span>
                            <span class="stat-number">342</span>
                            <span class="stat-label">Reviews Written</span>
                            <span class="stat-trend up">&#8679; +8 this month</span>
                        </td>
                        <td class="stat-card">
                            <span class="stat-icon">&#128101;</span>
                            <span class="stat-number">89</span>
                            <span class="stat-label">Following</span>
                            <span class="stat-trend flat">&#8212; unchanged</span>
                        </td>
                        <td class="stat-card">
                            <span class="stat-icon">&#128276;</span>
                            <span class="stat-number">234</span>
                            <span class="stat-label">Followers</span>
                            <span class="stat-trend up">&#8679; +12 this week</span>
                        </td>
                    </tr>
                </table>
            </td></tr>
        </table>
    </td></tr>
</table>

<!-- ── Recent activity feed ──────────────────────────────── -->
<table class="section-panel">
    <tr><td>
        <table class="section-inner">
            <tr class="section-head-row">
                <td><h2>Recent Activity</h2></td>
            </tr>
            <tr><td colspan="2">
                <table class="activity-list">
                    <tr>
                        <td class="activity-film">Inception</td>
                        <td><span class="activity-badge badge-watched">Watched</span></td>
                        <td class="activity-time">2 hours ago</td>
                    </tr>
                    <tr>
                        <td class="activity-film">The Dark Knight</td>
                        <td><span class="activity-badge badge-reviewed">Reviewed</span></td>
                        <td class="activity-time">Yesterday</td>
                    </tr>
                    <tr>
                        <td class="activity-film">Parasite</td>
                        <td><span class="activity-badge badge-liked">Liked</span></td>
                        <td class="activity-time">2 days ago</td>
                    </tr>
                    <tr>
                        <td class="activity-film">Interstellar</td>
                        <td><span class="activity-badge badge-watched">Watched</span></td>
                        <td class="activity-time">4 days ago</td>
                    </tr>
                </table>
            </td></tr>
        </table>
    </td></tr>
</table>

<!-- ── Responsive many-column table ─────────────────────── -->
<table class="section-panel">
    <tr><td>
        <table class="section-inner">
            <tr class="section-head-row">
                <td><h2>Film Statistics — Responsive Table</h2></td>
                <td class="view-all-cell"><a href="pages/featured-html5.html" class="view-all-link">All films &rarr;</a></td>
            </tr>
            <tr><td colspan="2">
                <div class="table-scroll-wrap">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Film</th><th>An</th><th>Gen</th><th>Regizor</th>
                                <th>Rating</th><th>IMDb</th><th>Vizionări</th>
                                <th>Recenzii</th><th>Durata</th><th>Limbă</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td>Inception</td><td>2010</td><td>Sci-Fi</td><td>C. Nolan</td><td>&#9733; 4.8</td><td>8.8</td><td>14.2M</td><td>87K</td><td>148 min</td><td>EN</td></tr>
                            <tr><td>The Dark Knight</td><td>2008</td><td>Action</td><td>C. Nolan</td><td>&#9733; 4.9</td><td>9.0</td><td>18.7M</td><td>112K</td><td>152 min</td><td>EN</td></tr>
                            <tr><td>Parasite</td><td>2019</td><td>Thriller</td><td>Bong Joon-ho</td><td>&#9733; 4.7</td><td>8.5</td><td>9.3M</td><td>64K</td><td>132 min</td><td>KO</td></tr>
                            <tr><td>Interstellar</td><td>2014</td><td>Sci-Fi</td><td>C. Nolan</td><td>&#9733; 4.6</td><td>8.6</td><td>13.1M</td><td>78K</td><td>169 min</td><td>EN</td></tr>
                            <tr><td>Blade Runner 2049</td><td>2017</td><td>Sci-Fi</td><td>D. Villeneuve</td><td>&#9733; 4.5</td><td>8.0</td><td>7.8M</td><td>52K</td><td>164 min</td><td>EN</td></tr>
                            <tr><td>Pulp Fiction</td><td>1994</td><td>Crime</td><td>Q. Tarantino</td><td>&#9733; 4.9</td><td>8.9</td><td>16.4M</td><td>95K</td><td>154 min</td><td>EN</td></tr>
                        </tbody>
                    </table>
                </div>
            </td></tr>
        </table>
    </td></tr>
</table>

<!-- ── Sortable film table (populated by table-sort.js) ─── -->
<table class="section-panel">
    <tr><td>
        <table class="section-inner">
            <tr class="section-head-row">
                <td><h2>Film Database — Sortable</h2></td>
                <td class="view-all-cell"><span style="font-size:0.75rem;color:var(--color-text-muted);">Click column header to sort &#8597;</span></td>
            </tr>
            <tr><td colspan="2">
                <style>
                    #film-table{width:100%;border-collapse:collapse;font-size:.86rem}
                    #film-table th{padding:.55rem .8rem;background:var(--color-surface);color:var(--color-text-muted);font-weight:600;font-size:.75rem;text-transform:uppercase;letter-spacing:.05em;border-bottom:2px solid var(--color-border);cursor:pointer;white-space:nowrap;user-select:none}
                    #film-table th:hover{color:var(--color-blue)}
                    #film-table th.sorted-asc::after{content:" ▲";color:var(--color-blue)}
                    #film-table th.sorted-desc::after{content:" ▼";color:var(--color-blue)}
                    #film-table td{padding:.5rem .8rem;border-bottom:1px solid var(--color-border-subtle);color:var(--color-text-muted);vertical-align:middle}
                    #film-table tr:hover td{background:rgba(255,255,255,.03);color:var(--color-text)}
                    .genre-badge{background:var(--color-surface-elevated);border:1px solid var(--color-border-subtle);border-radius:999px;padding:1px 8px;font-size:.75rem}
                    .rating-stars{color:#f0b429;letter-spacing:-1px}
                    .mood-tag{border-radius:999px;padding:2px 8px;font-size:.72rem;font-weight:600;text-transform:capitalize}
                    .mood-happy{background:rgba(240,180,41,.15);color:#f0b429}
                    .mood-tense{background:rgba(224,92,42,.15);color:#e05c2a}
                    .mood-melancholic{background:rgba(90,127,168,.15);color:#7eb3e0}
                    .mood-adventurous{background:rgba(63,185,80,.15);color:#3fb950}
                </style>
                <div style="overflow-x:auto;">
                    <table id="film-table">
                        <thead>
                            <tr>
                                <th data-col="0">Title</th>
                                <th data-col="1">Year</th>
                                <th data-col="2">Genre</th>
                                <th data-col="3">Rating</th>
                                <th data-col="4">Reviews</th>
                                <th data-col="5">Mood</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </td></tr>
        </table>
    </td></tr>
</table>

<!-- ── Vertical sortable table (cerința b) ───────────────── -->
<table class="section-panel">
    <tr><td>
        <table class="section-inner">
            <tr class="section-head-row">
                <td><h2>Film Database — Vertical Sortable</h2></td>
                <td class="view-all-cell"><span style="font-size:0.75rem;color:var(--color-text-muted);">Click row header to sort &#8597;</span></td>
            </tr>
            <tr><td colspan="2">
                <style>
                    #film-table-vertical{width:100%;border-collapse:collapse;font-size:.83rem}
                    #film-table-vertical th{background:var(--color-surface);color:var(--color-text-muted);font-weight:600;font-size:.75rem;text-transform:uppercase;letter-spacing:.05em;border-right:2px solid var(--color-border);border-bottom:1px solid var(--color-border-subtle);padding:.5rem .9rem;white-space:nowrap;user-select:none;min-width:100px;text-align:left}
                    #film-table-vertical th:hover{color:var(--color-blue);background:var(--color-btn)}
                    #film-table-vertical th.sorted-asc,#film-table-vertical th.sorted-desc{color:var(--color-blue)}
                    #film-table-vertical td{padding:.45rem .75rem;border-bottom:1px solid var(--color-border-subtle);border-right:1px solid var(--color-border-subtle);color:var(--color-text-muted);vertical-align:middle;white-space:nowrap;text-align:center}
                    #film-table-vertical tr:hover th{color:var(--color-blue)}
                    #film-table-vertical tr:hover td{background:rgba(255,255,255,.03);color:var(--color-text)}
                    .v-stars{color:#f0b429;letter-spacing:-1px}
                </style>
                <div style="overflow-x:auto;">
                    <table id="film-table-vertical"><tbody></tbody></table>
                </div>
            </td></tr>
        </table>
    </td></tr>
</table>

<!-- ── Trending rec cards ─────────────────────────────────── -->
<table class="section-panel">
    <tr><td>
        <table class="section-inner">
            <tr class="section-head-row">
                <td><h2>Trending This Week</h2></td>
                <td class="view-all-cell"><a href="pages/recommendations-html5.html" class="view-all-link">View all &rarr;</a></td>
            </tr>
            <tr><td colspan="2">
                <table class="rec-grid">
                    <tr>
                        <td class="rec-card"><div class="rec-poster">OP</div><b class="rec-title">Oppenheimer</b><small class="rec-genre">Drama / History</small></td>
                        <td class="rec-card"><div class="rec-poster">D2</div><b class="rec-title">Dune: Part Two</b><small class="rec-genre">Sci-Fi</small></td>
                        <td class="rec-card"><div class="rec-poster">PT</div><b class="rec-title">Poor Things</b><small class="rec-genre">Comedy / Drama</small></td>
                        <td class="rec-card"><div class="rec-poster">BA</div><b class="rec-title">The Batman</b><small class="rec-genre">Action</small></td>
                        <td class="rec-card"><div class="rec-poster">EE</div><b class="rec-title">Everything Everywhere</b><small class="rec-genre">Action / Comedy</small></td>
                    </tr>
                </table>
            </td></tr>
        </table>
    </td></tr>
</table>

<?php require_once __DIR__ . '/php/footer.php'; ?>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="js/data.js"></script>
<script src="js/carousel-jQuery.js" defer></script>
<script src="js/table-sort.js" defer></script>
<script src="js/table-sort-vertical-jQuery.js" defer></script>
