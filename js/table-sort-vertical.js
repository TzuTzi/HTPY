/* ============================================================
   table-sort-vertical.js  –  Tabel VERTICAL sortabil
   Folosit în: widgets.html  (#film-table-vertical)
   Depinde de: data.js (films)

   Diferența față de table-sort.js (orizontal):
     - Rândurile = atribute (Title, Year, Genre, Rating, Reviews, Mood)
     - Coloanele = filme
     - Click pe antetul din PRIMA COLOANĂ → sortează coloanele după acel atribut
   ============================================================ */

document.addEventListener("DOMContentLoaded", function () {

    var table = document.getElementById("film-table-vertical");
    if (!table) return;

    /* Proprietățile filmului, în ordinea rândurilor */
    var keys   = ["title", "year", "genre", "rating", "reviews", "mood"];
    var labels = ["Title", "Year", "Genre", "Rating ★", "Reviews", "Mood"];

    var sortRow = -1;   /* indexul rândului după care se sortează (-1 = nesortate) */
    var sortAsc = true; /* true = ascendent, false = descendent */

    /* ── render() ──────────────────────────────────────────────────────────
       Reconstruiește complet tabelul.
       Filmele sunt sortate după atributul rândului selectat,
       apoi fiecare rând este desenat cu: <th> (antet stânga) + <td> per film.
    ─────────────────────────────────────────────────────────────────────── */
    function render() {

        /* 1. Copie sortată a filmelor */
        var sorted = films.slice();

        if (sortRow >= 0) {
            var key = keys[sortRow];
            sorted.sort(function (a, b) {
                var va  = a[key];
                var vb  = b[key];
                var cmp = (typeof va === "string")
                    ? va.localeCompare(vb)
                    : (va > vb ? 1 : va < vb ? -1 : 0);
                return sortAsc ? cmp : -cmp;
            });
        }

        /* 2. Construiește HTML-ul tabelului – un rând per atribut */
        var tbody = table.querySelector("tbody");

        tbody.innerHTML = keys.map(function (key, rowIndex) {

            /* Celula antet din prima coloană (clickabilă) */
            var sortIndicator = "";
            if (rowIndex === sortRow) {
                sortIndicator = sortAsc ? " &#9650;" : " &#9660;"; /* ▲ ▼ */
            }
            var headerCell =
                "<th data-row='" + rowIndex + "' " +
                "class='" + (rowIndex === sortRow ? (sortAsc ? "sorted-asc" : "sorted-desc") : "") + "' " +
                "title='Click pentru a sorta după " + labels[rowIndex] + "'>" +
                labels[rowIndex] + sortIndicator +
                "</th>";

            /* Celulele de date – câte una per film, în ordinea sortată */
            var dataCells = sorted.map(function (film) {
                var val = film[key];

                if (key === "rating") {
                    var stars = "★".repeat(Math.round(val / 2)) +
                                "☆".repeat(5 - Math.round(val / 2));
                    return "<td><span class='v-stars'>" + stars + "</span> <small>" + val + "</small></td>";
                }
                if (key === "mood") {
                    return "<td><span class='mood-tag mood-" + val + "'>" + val + "</span></td>";
                }
                if (key === "genre") {
                    return "<td><span class='genre-badge'>" + val + "</span></td>";
                }
                return "<td>" + val + "</td>";

            }).join("");

            return "<tr>" + headerCell + dataCells + "</tr>";

        }).join("");

        /* 3. Atașează ascultătorii de click pe celulele antet */
        table.querySelectorAll("th[data-row]").forEach(function (th) {
            th.style.cursor = "pointer";
            th.addEventListener("click", function () {
                var i = parseInt(this.getAttribute("data-row"));
                if (sortRow === i) {
                    sortAsc = !sortAsc;     /* al doilea click → inversează direcția */
                } else {
                    sortRow = i;
                    sortAsc = true;
                }
                render();                   /* reconstruiește tabelul cu noua ordine */
            });
        });
    }

    /* Randare inițială (nesortată) */
    render();
});
