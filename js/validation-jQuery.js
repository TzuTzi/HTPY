/* jQuery copy of validation.js
   Requires jQuery */

$(function () {
    function showError($el, msg) {
        $el.addClass("input-error");
        $el.parent().find(".field-error-msg").remove();
        $("<span>").addClass("field-error-msg").text(msg).appendTo($el.parent());
    }

    function clearError($el) {
        $el.removeClass("input-error");
        $el.parent().find(".field-error-msg").remove();
    }

    $("input, select, textarea").on("input change", function () {
        clearError($(this));
    });

    $("form").on("submit", function (e) {
        var $form = $(this);
        var valid = true;

        $form.find("[required]").each(function () {
            var $el = $(this);
            clearError($el);
            if (!($.trim($el.val() || ""))) {
                showError($el, "Câmpul este obligatoriu.");
                valid = false;
            }
        });

        $form.find("input[type='number']").each(function () {
            var $el = $(this);
            var raw = $.trim($el.val() || "");
            var v = Number(raw);
            var minAttr = $el.attr("min");
            var maxAttr = $el.attr("max");
            var min = minAttr !== undefined && minAttr !== "" ? Number(minAttr) : -Infinity;
            var max = maxAttr !== undefined && maxAttr !== "" ? Number(maxAttr) : Infinity;
            if (raw !== "" && (isNaN(v) || v < min || v > max)) {
                showError($el, "Valoare invalidă (interval " + (minAttr || "-∞") + " – " + (maxAttr || "+∞") + ").");
                valid = false;
            }
        });

        $form.find("input[type='email']").each(function () {
            var $el = $(this);
            var val = $.trim($el.val() || "");
            if (val && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val)) {
                showError($el, "Adresă de e-mail invalidă.");
                valid = false;
            }
        });

        var $email = $form.find("[name='email']");
        var $emailConfirm = $form.find("[name='email_confirm']");
        if ($email.length && $emailConfirm.length && $email.val() && $emailConfirm.val()) {
            if ($email.val() !== $emailConfirm.val()) {
                showError($emailConfirm, "E-mail-urile nu coincid.");
                valid = false;
            }
        }

        var $parola = $form.find("[name='parola']");
        var $parolaConfirm = $form.find("[name='parola_confirm']");
        if ($parola.length && $parolaConfirm.length && $parola.val() && $parolaConfirm.val()) {
            if ($parola.val() !== $parolaConfirm.val()) {
                showError($parolaConfirm, "Parolele nu coincid.");
                valid = false;
            }
        }

        if ($parola.length && $parola.val() && $parola.val().length < 8) {
            showError($parola, "Parola trebuie să aibă cel puțin 8 caractere.");
            valid = false;
        }

        if (!valid) {
            e.preventDefault();
            var firstErr = $form.find(".input-error").first();
            if (firstErr.length) {
                firstErr[0].scrollIntoView({ behavior: "smooth", block: "center" });
                firstErr.trigger("focus");
            }
        }
    });
});
