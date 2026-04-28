/* jQuery copy of collapsible.js
   Requires jQuery */

$(function () {
    $(".tree-toggle").on("click", function () {
        var $li = $(this).parent();
        $li.toggleClass("open");
        var isOpen = $li.hasClass("open");

        $(this).attr("aria-expanded", String(isOpen));
        var $sublist = $li.find(".tree-sublist").first();
        if ($sublist.length) $sublist.attr("aria-hidden", String(!isOpen));
    });

    $(".tree-toggle2").on("click", function (e) {
        e.stopPropagation();
        var $li = $(this).parent();
        $li.toggleClass("open2");
        var isOpen = $li.hasClass("open2");

        $(this).attr("aria-expanded", String(isOpen));
        var $sub2 = $li.find(".tree-sub2").first();
        if ($sub2.length) $sub2.attr("aria-hidden", String(!isOpen));
    });
});
