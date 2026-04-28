/* jQuery copy of table-sort.js
   Requires jQuery + data.js (films) */

$(function () {
    var $table = $("#film-table");
    if (!$table.length) return;

    var sortCol = -1;
    var sortAsc = true;
    var cols = ["title", "year", "genre", "rating", "reviews", "mood"];

    function renderTable() {
        var sorted = films.slice();

        if (sortCol >= 0) {
            sorted.sort(function (a, b) {
                var key = cols[sortCol];
                var va = a[key];
                var vb = b[key];
                var cmp = (typeof va === "string")
                    ? va.localeCompare(vb)
                    : (va > vb ? 1 : va < vb ? -1 : 0);
                return sortAsc ? cmp : -cmp;
            });
        }

        var rowsHtml = sorted.map(function (f) {
            var stars = "★".repeat(Math.round(f.rating / 2)) + "☆".repeat(5 - Math.round(f.rating / 2));
            return "<tr>" +
                "<td>" + f.title + "</td>" +
                "<td>" + f.year + "</td>" +
                "<td><span class='genre-badge'>" + f.genre + "</span></td>" +
                "<td><span class='rating-stars' title='" + f.rating + "/10'>" + stars + "</span> <small>" + f.rating + "</small></td>" +
                "<td>" + f.reviews.toLocaleString() + "</td>" +
                "<td><span class='mood-tag mood-" + f.mood + "'>" + f.mood + "</span></td>" +
                "</tr>";
        }).join("");

        $table.find("tbody").html(rowsHtml);

        $table.find("th[data-col]").each(function (i) {
            $(this).removeClass("sorted-asc sorted-desc");
            if (i === sortCol) {
                $(this).addClass(sortAsc ? "sorted-asc" : "sorted-desc");
            }
        });
    }

    $table.find("th[data-col]").each(function (i) {
        $(this).on("click", function () {
            if (sortCol === i) {
                sortAsc = !sortAsc;
            } else {
                sortCol = i;
                sortAsc = true;
            }
            renderTable();
        });
    });

    renderTable();
});
