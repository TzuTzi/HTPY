/* jQuery copy of dependencies.js
   Requires jQuery + data.js (cities) */

$(function () {
    var $taraSelect = $("[name='tara']");
    var $orasSelect = $("[name='oras_select']");

    if ($taraSelect.length && $orasSelect.length) {
        $taraSelect.on("change", function () {
            var selectedCountry = $(this).val();
            var oraseCurente = cities[selectedCountry] || [];

            $orasSelect.empty();

            if (oraseCurente.length === 0) {
                $orasSelect.html("<option value=''>-- Selectează mai întâi țara --</option>").prop("disabled", true);
            } else {
                $("<option>").val("").text("-- Selectează orașul --").appendTo($orasSelect);
                oraseCurente.forEach(function (oras) {
                    $("<option>").val(oras).text(oras).appendTo($orasSelect);
                });
                $orasSelect.prop("disabled", false);
            }
        });
    }

    var $dataInput = $("[name='data_nasterii']");
    var $varstaInput = $("[name='varsta']");

    if ($dataInput.length && $varstaInput.length) {
        $dataInput.on("change", function () {
            var birthDate = new Date($(this).val());
            if (isNaN(birthDate.getTime())) {
                $varstaInput.val("");
                return;
            }
            var today = new Date();
            var age = today.getFullYear() - birthDate.getFullYear();
            var m = today.getMonth() - birthDate.getMonth();
            if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) age--;
            $varstaInput.val(age >= 0 ? age : "");
        });
    }
});
