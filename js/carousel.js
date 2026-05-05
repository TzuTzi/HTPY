// carousel.js - Carousel automat cu butoane prev/next

document.addEventListener("DOMContentLoaded", function () {
    var container  = document.getElementById("carousel");
    if (!container) return;

    var titleEl    = document.getElementById("carousel-title");
    var yearEl     = document.getElementById("carousel-year");
    var genreEl    = document.getElementById("carousel-genre");
    var linkEl     = document.getElementById("carousel-link");
    var dotsEl     = document.getElementById("carousel-dots");
    var prevBtn    = document.getElementById("carousel-prev");
    var nextBtn    = document.getElementById("carousel-next");
    var barEl      = document.getElementById("carousel-bar");
    var imgEl      = document.getElementById("carousel-image");
    var bgGradient = document.querySelector(".carousel-bg-gradient");

    var cur           = 0;
    var total         = carouselSlides.length;
    var AUTO_MS       = 4000;


    var autoInterval  = null;
    var resumeTimeout = null;


    var barPausedPct  = 0;

    // ── Build dot buttons ──────────────────────────────────────────────────
    carouselSlides.forEach(function (_, i) {
        var dot = document.createElement("button");
        dot.className = "carousel-dot";
        dot.setAttribute("aria-label", "Slide " + (i + 1));
        dot.addEventListener("click", function () { goTo(i); });
        dotsEl.appendChild(dot);
    });


    function render() {
        var s = carouselSlides[cur];

        if (bgGradient) {
            bgGradient.style.background =
                "linear-gradient(135deg, " + s.color + " 0%, #0d1117 70%)";
        }
        if (barEl) {
            barEl.style.backgroundColor = s.color;
        }

        if (titleEl)  titleEl.textContent = s.title;
        if (yearEl)   yearEl.textContent  = s.year;
        if (genreEl)  genreEl.textContent = s.genre;
        if (linkEl)   linkEl.href         = s.link;

        if (imgEl && s.imageFile) {
            imgEl.src            = "images/" + s.imageFile;
            imgEl.alt            = s.imageAlt || s.title;
            imgEl.style.display  = "block";
        } else if (imgEl) {
            imgEl.style.display = "none";
        }

        dotsEl.querySelectorAll(".carousel-dot").forEach(function (d, i) {
            d.classList.toggle("active", i === cur);
        });

        // Bar always starts fresh when a new slide renders
        startBar(AUTO_MS);
    }

    // ── goTo(n) ────────────────────────────────────────────────────────────
    function goTo(n) {
        cur = (n + total) % total;
        render();
        restartAuto();
    }


    function restartAuto() {
        clearInterval(autoInterval);
        clearTimeout(resumeTimeout);
        autoInterval = setInterval(function () {
            cur = (cur + 1) % total;
            render();
        }, AUTO_MS);
    }


    function pauseCarousel() {
        clearInterval(autoInterval);
        clearTimeout(resumeTimeout);
        autoInterval  = null;
        resumeTimeout = null;

        if (!barEl) return;


        var barPx    = parseFloat(window.getComputedStyle(barEl).width);
        var parentPx = parseFloat(window.getComputedStyle(barEl.parentElement).width);
        barPausedPct = parentPx > 0 ? (barPx / parentPx) * 100 : 0;

        // Freeze the bar: remove transition, lock width at current %
        barEl.style.transition = "none";
        barEl.style.width      = barPausedPct + "%";
    }


    function resumeCarousel() {
        var remaining = Math.max(0, AUTO_MS * (1 - barPausedPct / 100));

        // Resume the CSS transition for only the remaining duration
        if (barEl) {
            barEl.style.transition = "width " + remaining + "ms linear";
            barEl.style.width      = "100%";
        }

        // After the remaining time, advance slide then switch to normal interval
        resumeTimeout = setTimeout(function () {
            cur = (cur + 1) % total;
            render();
            restartAuto();
        }, remaining);
    }

    // ── Button listeners ───────────────────────────────────────────────────
    if (prevBtn) prevBtn.addEventListener("click", function () { goTo(cur - 1); });
    if (nextBtn) nextBtn.addEventListener("click", function () { goTo(cur + 1); });

    // ── Hover listeners ────────────────────────────────────────────────────
    container.addEventListener("mouseenter", pauseCarousel);
    container.addEventListener("mouseleave", resumeCarousel);

    // ── Init ───────────────────────────────────────────────────────────────
    render();
    restartAuto();
});
