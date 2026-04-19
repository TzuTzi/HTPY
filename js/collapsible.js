/* ============================================================
   collapsible.js  –  Liste imbricate colapsabile
   Folosit în: nested-list.html
   Funcționare: adaugă/elimină clasa "open" / "open2" pe <li>
   CSS din pagină controlează vizibilitatea sublistei.
   ============================================================ */

document.addEventListener("DOMContentLoaded", function () {

    /* ── Nivel 1: butoanele .tree-toggle ── */
    document.querySelectorAll(".tree-toggle").forEach(function (btn) {
        btn.addEventListener("click", function () {
            var li     = this.parentElement;
            var isOpen = li.classList.toggle("open");

            this.setAttribute("aria-expanded", isOpen);

            var sublist = li.querySelector(".tree-sublist");
            if (sublist) sublist.setAttribute("aria-hidden", !isOpen);
        });
    });

    /* ── Nivel 2: butoanele .tree-toggle2 ── */
    document.querySelectorAll(".tree-toggle2").forEach(function (btn) {
        btn.addEventListener("click", function (e) {
            e.stopPropagation(); /* nu declanșa și toggle-ul de nivel 1 */
            var li     = this.parentElement;
            var isOpen = li.classList.toggle("open2");

            this.setAttribute("aria-expanded", isOpen);

            var sub2 = li.querySelector(".tree-sub2");
            if (sub2) sub2.setAttribute("aria-hidden", !isOpen);
        });
    });

});
