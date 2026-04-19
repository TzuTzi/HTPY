/* ============================================================
   table-sort.js  –  Tabel sortabil cu click pe header
   Folosit în: widgets.html
   Depinde de: data.js (films)
   ============================================================ */

document.addEventListener("DOMContentLoaded", function () {
    var table = document.getElementById("film-table");
    if (!table) return;

    var tbody    = table.querySelector("tbody");
    var sortCol  = -1;   /* nicio coloană sortată inițial */
    var sortAsc  = true;

    /* Coloanele corespund ordinii din array-ul films:
       0=title, 1=year, 2=genre, 3=rating, 4=reviews, 5=mood */
    var cols = ["title", "year", "genre", "rating", "reviews", "mood"];

    /* ── Construiește rândurile tabelului ── */
    function renderTable() {
        var sorted = films.slice(); /* copie pentru a nu modifica originalul */

        if (sortCol >= 0) {
            sorted.sort(function (a, b) {
                var key = cols[sortCol];
                var va  = a[key];
                var vb  = b[key];
                var cmp = (typeof va === "string")
                    ? va.localeCompare(vb)
                    : (va > vb ? 1 : va < vb ? -1 : 0);
                return sortAsc ? cmp : -cmp;
            });
        }

        tbody.innerHTML = sorted.map(function (f) {
            var stars = "★".repeat(Math.round(f.rating / 2)) + "☆".repeat(5 - Math.round(f.rating / 2));
            return "<tr>" +
                "<td>" + f.title + "</td>" +
                "<td>" + f.year  + "</td>" +
                "<td><span class='genre-badge'>" + f.genre + "</span></td>" +
                "<td><span class='rating-stars' title='" + f.rating + "/10'>" + stars + "</span> <small>" + f.rating + "</small></td>" +
                "<td>" + f.reviews.toLocaleString() + "</td>" +
                "<td><span class='mood-tag mood-" + f.mood + "'>" + f.mood + "</span></td>" +
                "</tr>";
        }).join("");

        /* Actualizează indicatoarele de sortare pe headere */
        table.querySelectorAll("th[data-col]").forEach(function (th, i) {
            th.classList.remove("sorted-asc", "sorted-desc");
            if (i === sortCol) {
                th.classList.add(sortAsc ? "sorted-asc" : "sorted-desc");
            }
        });
    }

    /* ── Ascultători click pe headere ── */
    table.querySelectorAll("th[data-col]").forEach(function (th, i) {
        th.addEventListener("click", function () {
            if (sortCol === i) {
                sortAsc = !sortAsc; /* inversează direcția */
            } else {
                sortCol = i;
                sortAsc = true;
            }
            renderTable();
        });
    });

    /* ── Randare inițială ── */
    renderTable();
});
