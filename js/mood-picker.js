/* ============================================================
   mood-picker.js  –  Cinematic Mood Picker
   Folosit în: mood-picker.html
   Depinde de: data.js (films, moods)
   ============================================================ */

document.addEventListener("DOMContentLoaded", function () {
    var moodButtons  = document.querySelectorAll(".mood-btn");
    var filmGrid     = document.getElementById("mood-film-grid");
    var resultTitle  = document.getElementById("mood-result-title");
    var resetBtn     = document.getElementById("mood-reset");
    var activeMood   = null;

    /* ── Randează cardurile de film filtrate ── */
    function renderFilms(filtered) {
        filmGrid.innerHTML = "";

        if (filtered.length === 0) {
            filmGrid.innerHTML = "<p style='color:var(--color-text-muted);grid-column:1/-1;'>No films found for this mood.</p>";
            return;
        }

        filtered.forEach(function (f, index) {
            var card       = document.createElement("div");
            card.className = "mood-film-card";
            card.style.animationDelay = (index * 0.06) + "s";

            var stars = "★".repeat(Math.round(f.rating / 2)) + "☆".repeat(5 - Math.round(f.rating / 2));
            card.innerHTML =
                "<div class='mood-card-poster mood-poster-" + f.mood + "'>" +
                    f.title.split(" ").map(function(w){ return w[0]; }).join("").slice(0,3) +
                "</div>" +
                "<div class='mood-card-title'>" + f.title + "</div>" +
                "<div class='mood-card-meta'>" + f.year + " &middot; " + f.genre + "</div>" +
                "<div class='mood-card-stars'>" + stars + " <small>" + f.rating + "</small></div>";

            filmGrid.appendChild(card);
        });
    }

    /* ── Click pe buton de mood ── */
    moodButtons.forEach(function (btn) {
        btn.addEventListener("click", function () {
            var mood = this.dataset.mood;

            if (activeMood === mood) {
                /* Al doilea click pe același mood → reset */
                resetFilter();
                return;
            }

            activeMood = mood;

            /* Actualizează starea butoanelor */
            moodButtons.forEach(function (b) {
                b.classList.remove("active");
                b.setAttribute("aria-pressed", "false");
            });
            this.classList.add("active");
            this.setAttribute("aria-pressed", "true");
            resetBtn.style.display = "inline-block";

            /* Filtrare + animație */
            var filtered = films.filter(function (f) { return f.mood === mood; });
            var moodObj  = moods.find(function (m) { return m.key === mood; });

            resultTitle.textContent = moodObj ? moodObj.label + " — " + filtered.length + " films" : "";
            resultTitle.style.color = moodObj ? moodObj.color : "";

            filmGrid.classList.add("animating");
            setTimeout(function () {
                renderFilms(filtered);
                filmGrid.classList.remove("animating");
            }, 200);
        });
    });

    /* ── Reset ── */
    function resetFilter() {
        activeMood = null;
        moodButtons.forEach(function (b) {
            b.classList.remove("active");
            b.setAttribute("aria-pressed", "false");
        });
        resultTitle.textContent = "All Films";
        resultTitle.style.color = "";
        resetBtn.style.display  = "none";
        filmGrid.classList.add("animating");
        setTimeout(function () {
            renderFilms(films);
            filmGrid.classList.remove("animating");
        }, 200);
    }

    resetBtn.addEventListener("click", resetFilter);

    /* ── Randare inițială (toate filmele) ── */
    resultTitle.textContent = "All Films";
    renderFilms(films);
});
