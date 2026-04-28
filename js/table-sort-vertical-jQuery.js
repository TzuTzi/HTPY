/* jQuery copy of table-sort-vertical.js
   Requires jQuery + data.js (films) */

$(function () {
    var $table = $("#film-table-vertical");
    if (!$table.length) return;

    var keys = ["title", "year", "genre", "rating", "reviews", "mood"];
    var labels = ["Title", "Year", "Genre", "Rating ★", "Reviews", "Mood"];
    var sortRow = -1;
    var sortAsc = true;

    function render() {
        var sorted = films.slice();

        if (sortRow >= 0) {
            var key = keys[sortRow];
            sorted.sort(function (a, b) {
                var va = a[key];
                var vb = b[key];
                var cmp = (typeof va === "string")
                    ? va.localeCompare(vb)
                    : (va > vb ? 1 : va < vb ? -1 : 0);
                return sortAsc ? cmp : -cmp;
            });
        }

        var bodyHtml = keys.map(function (key, rowIndex) {
            var sortIndicator = "";
            if (rowIndex === sortRow) sortIndicator = sortAsc ? " &#9650;" : " &#9660;";

            var headerCell =
                "<th data-row='" + rowIndex + "' " +
                "class='" + (rowIndex === sortRow ? (sortAsc ? "sorted-asc" : "sorted-desc") : "") + "' " +
                "title='Click pentru a sorta după " + labels[rowIndex] + "'>" +
                labels[rowIndex] + sortIndicator +
                "</th>";

            var dataCells = sorted.map(function (film) {
                var val = film[key];
                if (key === "rating") {
                    var stars = "★".repeat(Math.round(val / 2)) + "☆".repeat(5 - Math.round(val / 2));
                    return "<td><span class='v-stars'>" + stars + "</span> <small>" + val + "</small></td>";
                }
                if (key === "mood") return "<td><span class='mood-tag mood-" + val + "'>" + val + "</span></td>";
                if (key === "genre") return "<td><span class='genre-badge'>" + val + "</span></td>";
                return "<td>" + val + "</td>";
            }).join("");

            return "<tr>" + headerCell + dataCells + "</tr>";
        }).join("");

        $table.find("tbody").html(bodyHtml);

        $table.find("th[data-row]").css("cursor", "pointer").on("click", function () {
            var i = parseInt($(this).attr("data-row"), 10);
            if (sortRow === i) {
                sortAsc = !sortAsc;
            } else {
                sortRow = i;
                sortAsc = true;
            }
            render();
        });
    }

    render();
});
