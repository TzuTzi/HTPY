/* ============================================================
   carousel.js  –  Carousel automat cu butoane prev/next
   Folosit în: LoggedOutPage-html5.html
   Depinde de: data.js (carouselSlides)
   ============================================================ */

document.addEventListener("DOMContentLoaded", function () {
    var container   = document.getElementById("carousel");
    if (!container) return;

    var titleEl  = document.getElementById("carousel-title");
    var yearEl   = document.getElementById("carousel-year");
    var genreEl  = document.getElementById("carousel-genre");
    var linkEl   = document.getElementById("carousel-link");
    var dotsEl   = document.getElementById("carousel-dots");
    var prevBtn  = document.getElementById("carousel-prev");
    var nextBtn  = document.getElementById("carousel-next");
    var barEl    = document.getElementById("carousel-bar");

    var cur         = 0;
    var total       = carouselSlides.length;
    var autoInterval = null;
    var barInterval  = null;
    var AUTO_MS      = 4000;

    /* ── Construiește dot-urile indicatoare ── */
    carouselSlides.forEach(function (_, i) {
        var dot       = document.createElement("button");
        dot.className = "carousel-dot";
        dot.setAttribute("aria-label", "Slide " + (i + 1));
        dot.addEventListener("click", function () { goTo(i); });
        dotsEl.appendChild(dot);
    });

    /* ── Randează slide-ul curent ── */
    function render() {
        var s = carouselSlides[cur];
        container.style.setProperty("--slide-color", s.color);
        titleEl.textContent = s.title;
        yearEl.textContent  = s.year;
        genreEl.textContent = s.genre;
        linkEl.href         = s.link;

        /* Actualizează dots */
        dotsEl.querySelectorAll(".carousel-dot").forEach(function (d, i) {
            d.classList.toggle("active", i === cur);
        });

        /* Reset bara de progres */
        if (barEl) {
            barEl.style.transition = "none";
            barEl.style.width      = "0%";
            /* Forțează reflow pentru a reporni animația */
            void barEl.offsetWidth;
            barEl.style.transition = "width " + AUTO_MS + "ms linear";
            barEl.style.width      = "100%";
        }
    }

    /* ── Mergi la slide-ul n ── */
    function goTo(n) {
        cur = (n + total) % total;
        render();
        restartAuto();
    }

    /* ── Auto-play ── */
    function restartAuto() {
        clearInterval(autoInterval);
        autoInterval = setInterval(function () {
            cur = (cur + 1) % total;
            render();
        }, AUTO_MS);
    }

    /* ── Butoane prev / next ── */
    prevBtn.addEventListener("click", function () { goTo(cur - 1); });
    nextBtn.addEventListener("click", function () { goTo(cur + 1); });

    /* ── Pauză la hover ── */
    container.addEventListener("mouseenter", function () { clearInterval(autoInterval); });
    container.addEventListener("mouseleave", function () { restartAuto(); });

    /* ── Pornire ── */
    render();
    restartAuto();
});
