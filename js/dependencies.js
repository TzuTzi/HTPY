/* ============================================================
   dependencies.js  –  Dependențe între câmpuri
   1. Țară → Oraș dropdown (populat dinamic din data.js)
   2. Data nașterii → Vârstă (calculată automat)
   ============================================================ */

document.addEventListener("DOMContentLoaded", function () {

    /* ── 1. Țară → Oraș ── */
    var taraSelect  = document.querySelector("[name='tara']");
    var orasSelect  = document.querySelector("[name='oras_select']");

    if (taraSelect && orasSelect) {
        taraSelect.addEventListener("change", function () {
            var selectedCountry = this.value;
            var oraseCurente    = cities[selectedCountry] || [];

            /* Resetează dropdown-ul de orașe */
            orasSelect.innerHTML = "";

            if (oraseCurente.length === 0) {
                orasSelect.innerHTML = "<option value=''>-- Selectează mai întâi țara --</option>";
                orasSelect.disabled  = true;
            } else {
                var defaultOpt       = document.createElement("option");
                defaultOpt.value     = "";
                defaultOpt.textContent = "-- Selectează orașul --";
                orasSelect.appendChild(defaultOpt);

                oraseCurente.forEach(function (oras) {
                    var opt       = document.createElement("option");
                    opt.value     = oras;
                    opt.textContent = oras;
                    orasSelect.appendChild(opt);
                });

                orasSelect.disabled = false;
            }
        });
    }

    /* ── 2. Data nașterii → Vârstă (calculat automat, readonly) ── */
    var dataInput   = document.querySelector("[name='data_nasterii']");
    var varstaInput = document.querySelector("[name='varsta']");

    if (dataInput && varstaInput) {
        dataInput.addEventListener("change", function () {
            var birthDate = new Date(this.value);
            if (isNaN(birthDate.getTime())) {
                varstaInput.value = "";
                return;
            }
            var today = new Date();
            var age   = today.getFullYear() - birthDate.getFullYear();
            var m     = today.getMonth() - birthDate.getMonth();
            if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
                age--;
            }
            varstaInput.value = age >= 0 ? age : "";
        });
    }

});
