/* jQuery copy of carousel.js
   Requires jQuery + data.js (carouselSlides) */

$(function () {
    var $container = $("#carousel");
    if (!$container.length) return;

    var $title = $("#carousel-title");
    var $year = $("#carousel-year");
    var $genre = $("#carousel-genre");
    var $link = $("#carousel-link");
    var $dots = $("#carousel-dots");
    var $prev = $("#carousel-prev");
    var $next = $("#carousel-next");
    var $bar = $("#carousel-bar");
    var $img = $("#carousel-image");
    var $bg = $(".carousel-bg-gradient").first();

    var cur = 0;
    var total = carouselSlides.length;
    var AUTO_MS = 4000;
    var autoInterval = null;
    var resumeTimeout = null;
    var barPausedPct = 0;

    carouselSlides.forEach(function (_, i) {
        var $dot = $("<button>")
            .addClass("carousel-dot")
            .attr("aria-label", "Slide " + (i + 1))
            .on("click", function () { goTo(i); });
        $dots.append($dot);
    });

    function startBar(duration) {
        if (!$bar.length) return;
        $bar.css({ transition: "none", width: "0%" });
        void $bar[0].offsetWidth;
        $bar.css({ transition: "width " + duration + "ms linear", width: "100%" });
        barPausedPct = 0;
    }

    function render() {
        var s = carouselSlides[cur];
        if ($bg.length) $bg.css("background", "linear-gradient(135deg, " + s.color + " 0%, #0d1117 70%)");
        if ($bar.length) $bar.css("backgroundColor", s.color);

        $title.text(s.title);
        $year.text(s.year);
        $genre.text(s.genre);
        $link.attr("href", s.link);

        if ($img.length && s.imageFile) {
            $img.attr({ src: "images/" + s.imageFile, alt: s.imageAlt || s.title }).css("display", "block");
        } else if ($img.length) {
            $img.css("display", "none");
        }

        $dots.find(".carousel-dot").each(function (i) {
            $(this).toggleClass("active", i === cur);
        });

        startBar(AUTO_MS);
    }

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
        autoInterval = null;
        resumeTimeout = null;
        if (!$bar.length) return;

        var barPx = parseFloat(window.getComputedStyle($bar[0]).width);
        var parentPx = parseFloat(window.getComputedStyle($bar[0].parentElement).width);
        barPausedPct = parentPx > 0 ? (barPx / parentPx) * 100 : 0;
        $bar.css({ transition: "none", width: barPausedPct + "%" });
    }

    function resumeCarousel() {
        var remaining = Math.max(0, AUTO_MS * (1 - barPausedPct / 100));
        if ($bar.length) {
            $bar.css({ transition: "width " + remaining + "ms linear", width: "100%" });
        }
        resumeTimeout = setTimeout(function () {
            cur = (cur + 1) % total;
            render();
            restartAuto();
        }, remaining);
    }

    $prev.on("click", function () { goTo(cur - 1); });
    $next.on("click", function () { goTo(cur + 1); });
    $container.on("mouseenter", pauseCarousel);
    $container.on("mouseleave", resumeCarousel);

    render();
    restartAuto();
});
