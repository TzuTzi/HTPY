/* jQuery copy of mood-picker.js
   Requires jQuery + data.js (films, moods) */

$(function () {
    var $moodButtons = $(".mood-btn");
    var $filmGrid = $("#mood-film-grid");
    var $resultTitle = $("#mood-result-title");
    var $resetBtn = $("#mood-reset");
    var activeMood = null;

    function renderFilms(filtered) {
        $filmGrid.html("");

        if (!filtered.length) {
            $filmGrid.html("<p style='color:var(--color-text-muted);grid-column:1/-1;'>No films found for this mood.</p>");
            return;
        }

        filtered.forEach(function (f, index) {
            var initials = f.title.split(" ").map(function (w) { return w[0]; }).join("").slice(0, 3);
            var stars = "★".repeat(Math.round(f.rating / 2)) + "☆".repeat(5 - Math.round(f.rating / 2));

            var $card = $("<div>")
                .addClass("mood-film-card")
                .css("animationDelay", (index * 0.06) + "s")
                .html(
                    "<div class='mood-card-poster mood-poster-" + f.mood + "'>" + initials + "</div>" +
                    "<div class='mood-card-title'>" + f.title + "</div>" +
                    "<div class='mood-card-meta'>" + f.year + " &middot; " + f.genre + "</div>" +
                    "<div class='mood-card-stars'>" + stars + " <small>" + f.rating + "</small></div>"
                );

            $filmGrid.append($card);
        });
    }

    function resetFilter() {
        activeMood = null;
        $moodButtons.removeClass("active").attr("aria-pressed", "false");
        $resultTitle.text("All Films").css("color", "");
        $resetBtn.hide();
        $filmGrid.addClass("animating");
        setTimeout(function () {
            renderFilms(films);
            $filmGrid.removeClass("animating");
        }, 200);
    }

    $moodButtons.on("click", function () {
        var mood = $(this).data("mood");

        if (activeMood === mood) {
            resetFilter();
            return;
        }

        activeMood = mood;
        $moodButtons.removeClass("active").attr("aria-pressed", "false");
        $(this).addClass("active").attr("aria-pressed", "true");
        $resetBtn.css("display", "inline-block");

        var filtered = films.filter(function (f) { return f.mood === mood; });
        var moodObj = moods.find(function (m) { return m.key === mood; });
        $resultTitle.text(moodObj ? moodObj.label + " — " + filtered.length + " films" : "");
        $resultTitle.css("color", moodObj ? moodObj.color : "");

        $filmGrid.addClass("animating");
        setTimeout(function () {
            renderFilms(filtered);
            $filmGrid.removeClass("animating");
        }, 200);
    });

    $resetBtn.on("click", resetFilter);

    $resultTitle.text("All Films");
    renderFilms(films);
});
