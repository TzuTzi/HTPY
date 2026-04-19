// carousel.js - Carousel automat cu butoane prev/next

document.addEventListener("DOMContentLoaded", function () {
    var container = document.getElementById("carousel");
    if (!container) return;

    var titleEl = document.getElementById("carousel-title");
    var yearEl = document.getElementById("carousel-year");
    var genreEl = document.getElementById("carousel-genre");
    var linkEl = document.getElementById("carousel-link");
    var dotsEl = document.getElementById("carousel-dots");
    var prevBtn = document.getElementById("carousel-prev");
    var nextBtn = document.getElementById("carousel-next");
    var barEl = document.getElementById("carousel-bar");
    var imgEl = document.getElementById("carousel-image");
    var bgGradient = document.querySelector(".carousel-bg-gradient");

    var cur = 0;
    var total = carouselSlides.length;
    var autoInterval = null;
    var AUTO_MS = 4000;

    // Build dots
    carouselSlides.forEach(function (_, i) {
        var dot = document.createElement("button");
        dot.className = "carousel-dot";
        dot.setAttribute("aria-label", "Slide " + (i + 1));
        dot.addEventListener("click", function () { goTo(i); });
        dotsEl.appendChild(dot);
    });

    function render() {
        var s = carouselSlides[cur];

        // Set color variable for gradients and bars
        if (bgGradient) {
            bgGradient.style.background = `linear-gradient(135deg, ${s.color} 0%, #0d1117 70%)`;
        }
        if (barEl) {
            barEl.style.backgroundColor = s.color;
        }

        // Update text content
        if (titleEl) titleEl.textContent = s.title;
        if (yearEl) yearEl.textContent = s.year;
        if (genreEl) genreEl.textContent = s.genre;
        if (linkEl) linkEl.href = s.link;

        // Update image if it exists
        if (imgEl && s.imageFile) {
            imgEl.src = "images/" + s.imageFile;
            imgEl.alt = s.imageAlt || s.title;
            imgEl.style.display = "block";
        } else if (imgEl) {
            imgEl.style.display = "none";
        }

        // Update active dot
        var dots = dotsEl.querySelectorAll(".carousel-dot");
        dots.forEach(function (d, i) {
            if (i === cur) {
                d.classList.add("active");
            } else {
                d.classList.remove("active");
            }
        });

        // Reset progress bar
        if (barEl) {
            barEl.style.transition = "none";
            barEl.style.width = "0%";
            void barEl.offsetWidth;
            barEl.style.transition = "width " + AUTO_MS + "ms linear";
            barEl.style.width = "100%";
        }
    }

    function goTo(n) {
        cur = (n + total) % total;
        render();
        restartAuto();
    }

    function restartAuto() {
        if (autoInterval) clearInterval(autoInterval);
        autoInterval = setInterval(function () {
            cur = (cur + 1) % total;
            render();
        }, AUTO_MS);
    }

    if (prevBtn) prevBtn.addEventListener("click", function () { goTo(cur - 1); });
    if (nextBtn) nextBtn.addEventListener("click", function () { goTo(cur + 1); });

    if (container) {
        container.addEventListener("mouseenter", function () { if (autoInterval) clearInterval(autoInterval); });
        container.addEventListener("mouseleave", function () { restartAuto(); });
    }

    render();
    restartAuto();
});