/* ============================================================
   validation.js  –  Form validation pentru BlimBlau
   Atașat pe: form-html5.html, LoggedInPage-html5.html,
              LoggedOutPage-html5.html
   ============================================================ */

document.addEventListener("DOMContentLoaded", function () {

    /* ── Adaugă mesaje de eroare inline sub câmpuri ── */
    function showError(el, msg) {
        el.classList.add("input-error");
        var old = el.parentElement.querySelector(".field-error-msg");
        if (old) old.remove();
        var span = document.createElement("span");
        span.className = "field-error-msg";
        span.textContent = msg;
        el.parentElement.appendChild(span);
    }

    function clearError(el) {
        el.classList.remove("input-error");
        var old = el.parentElement.querySelector(".field-error-msg");
        if (old) old.remove();
    }

    /* ── Elimină eroarea în timp real când utilizatorul corectează ── */
    document.querySelectorAll("input, select, textarea").forEach(function (el) {
        el.addEventListener("input", function () { clearError(this); });
        el.addEventListener("change", function () { clearError(this); });
    });

    /* ── Validare la submit pentru fiecare formular din pagină ── */
    document.querySelectorAll("form").forEach(function (form) {
        form.addEventListener("submit", function (e) {
            var valid = true;

            /* 1. Câmpuri required goale */
            form.querySelectorAll("[required]").forEach(function (el) {
                clearError(el);
                if (!el.value.trim()) {
                    showError(el, "Câmpul este obligatoriu.");
                    valid = false;
                }
            });

            /* 2. Câmpuri number – verificare min/max */
            form.querySelectorAll("input[type='number']").forEach(function (el) {
                var v = Number(el.value);
                var min = el.min !== "" ? Number(el.min) : -Infinity;
                var max = el.max !== "" ? Number(el.max) : Infinity;
                if (el.value.trim() !== "" && (isNaN(v) || v < min || v > max)) {
                    showError(el, "Valoare invalidă (interval " + el.min + " – " + el.max + ").");
                    valid = false;
                }
            });

            /* 3. Email – format de bază */
            form.querySelectorAll("input[type='email']").forEach(function (el) {
                if (el.value.trim() && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(el.value)) {
                    showError(el, "Adresă de e-mail invalidă.");
                    valid = false;
                }
            });

            /* 4. Confirmare e-mail */
            var email        = form.querySelector("[name='email']");
            var emailConfirm = form.querySelector("[name='email_confirm']");
            if (email && emailConfirm && email.value && emailConfirm.value) {
                if (email.value !== emailConfirm.value) {
                    showError(emailConfirm, "E-mail-urile nu coincid.");
                    valid = false;
                }
            }

            /* 5. Confirmare parolă */
            var parola        = form.querySelector("[name='parola']");
            var parolaConfirm = form.querySelector("[name='parola_confirm']");
            if (parola && parolaConfirm && parola.value && parolaConfirm.value) {
                if (parola.value !== parolaConfirm.value) {
                    showError(parolaConfirm, "Parolele nu coincid.");
                    valid = false;
                }
            }

            /* 6. Parolă – lungime minimă */
            if (parola && parola.value && parola.value.length < 8) {
                showError(parola, "Parola trebuie să aibă cel puțin 8 caractere.");
                valid = false;
            }

            if (!valid) {
                e.preventDefault();
                /* Scroll la primul câmp cu eroare */
                var firstErr = form.querySelector(".input-error");
                if (firstErr) {
                    firstErr.scrollIntoView({ behavior: "smooth", block: "center" });
                    firstErr.focus();
                }
            }
        });
    });
});
