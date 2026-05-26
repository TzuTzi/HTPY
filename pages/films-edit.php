<?php
/* ============================================================
   Cerința 5 — Editare film cu Vanilla JS AJAX
   - Select cu ID-uri filme (populat via AJAX)
   - Câmpuri actualizate la schimbarea selecției (AJAX GET)
   - Buton Save dezactivat până la modificare
   - Avertisment la modificări nesalvate
   ============================================================ */
require_once __DIR__ . '/../php/auth.php';
require_login('../login.php');
$pageTitle = 'C5 – Vanilla JS AJAX Edit';
require_once __DIR__ . '/../php/header.php';
?>

<table class="section-panel">
  <tr><td>
    <table class="section-inner">
      <tr class="section-head-row">
        <td>
          <h2>Cerința 5 — Editare film – Vanilla JS AJAX</h2>
          <small style="color:var(--color-text-muted);font-weight:400;">
            Select populat via AJAX · câmpuri actualizate la schimbare · Save dezactivat până la modificare
          </small>
        </td>
      </tr>
      <tr><td>

        <div id="msg" style="margin-bottom:0.75rem;font-size:0.875rem;"></div>

        <table class="form-table" style="max-width:480px;">
          <tr>
            <td class="form-label"><label for="film-select">Film</label></td>
            <td class="form-field">
              <select id="film-select" style="width:100%;">
                <option value="">Loading…</option>
              </select>
            </td>
          </tr>
          <tr>
            <td class="form-label"><label for="f-title">Title</label></td>
            <td class="form-field"><input type="text" id="f-title" name="title" style="width:100%;"></td>
          </tr>
          <tr>
            <td class="form-label"><label for="f-year">Year</label></td>
            <td class="form-field"><input type="number" id="f-year" name="year" min="1888" max="2099" style="width:100%;"></td>
          </tr>
          <tr>
            <td class="form-label"><label for="f-genre">Genre</label></td>
            <td class="form-field"><input type="text" id="f-genre" name="genre" style="width:100%;"></td>
          </tr>
          <tr>
            <td class="form-label"><label for="f-rating">Rating</label></td>
            <td class="form-field"><input type="number" id="f-rating" name="rating" min="0" max="10" step="0.1" style="width:100%;"></td>
          </tr>
          <tr>
            <td class="form-label"><label for="f-mood">Mood</label></td>
            <td class="form-field"><input type="text" id="f-mood" name="mood" style="width:100%;"></td>
          </tr>
        </table>

        <div class="form-actions" style="margin-top:1rem;">
          <button id="btn-save" class="btn-primary" disabled>Save</button>
        </div>

        <p style="margin-top:1rem;">
          <a href="lab.php" style="color:var(--color-blue);font-size:0.875rem;">← Back to Lab</a>
        </p>

      </td></tr>
    </table>
  </td></tr>
</table>

<script>
/* ── Cerința 5: Vanilla JS AJAX Edit ────────────────────────── */
var isDirty   = false;
var currentId = null;

var select  = document.getElementById('film-select');
var btnSave = document.getElementById('btn-save');
var msgBox  = document.getElementById('msg');
var fields  = ['f-title', 'f-year', 'f-genre', 'f-rating', 'f-mood'];

/* ── helper: show message ── */
function showMsg(text, ok) {
    msgBox.textContent = text;
    msgBox.style.color = ok ? '#3fb950' : '#f85149';
}

/* ── mark form dirty ── */
function markDirty() {
    isDirty = true;
    btnSave.disabled = false;
}

/* ── mark form clean ── */
function markClean() {
    isDirty = false;
    btnSave.disabled = true;
}

/* ── populate fields from film object ── */
function fillForm(film) {
    document.getElementById('f-title').value  = film.title  || '';
    document.getElementById('f-year').value   = film.year   || '';
    document.getElementById('f-genre').value  = film.genre  || '';
    document.getElementById('f-rating').value = film.rating || '';
    document.getElementById('f-mood').value   = film.mood   || '';
    markClean();
}

/* ── load single film via AJAX ── */
function loadFilm(id) {
    var xhr = new XMLHttpRequest();
    xhr.open('GET', '../api/film.php?action=get&id=' + id, true);
    xhr.onload = function () {
        if (xhr.status === 200) {
            var film = JSON.parse(xhr.responseText);
            if (film) {
                currentId = id;
                fillForm(film);
                showMsg('', true);
            }
        }
    };
    xhr.send();
}

/* ── save film via AJAX POST ── */
function saveFilm(callback) {
    if (!currentId) return;
    var data = {
        id:     parseInt(currentId, 10),
        title:  document.getElementById('f-title').value,
        year:   parseInt(document.getElementById('f-year').value, 10),
        genre:  document.getElementById('f-genre').value,
        rating: parseFloat(document.getElementById('f-rating').value),
        mood:   document.getElementById('f-mood').value
    };
    var xhr = new XMLHttpRequest();
    xhr.open('POST', '../api/film.php', true);
    xhr.setRequestHeader('Content-Type', 'application/json');
    xhr.onload = function () {
        if (xhr.status === 200) {
            var res = JSON.parse(xhr.responseText);
            if (res.success) {
                markClean();
                showMsg('Saved successfully.', true);
                /* update select label */
                var opt = select.querySelector('option[value="' + currentId + '"]');
                if (opt) opt.textContent = data.title + ' (' + data.year + ')';
                if (typeof callback === 'function') callback();
            } else {
                showMsg('Save failed.', false);
            }
        }
    };
    xhr.send(JSON.stringify(data));
}

/* ── populate select with all films ── */
function loadFilmList() {
    var xhr = new XMLHttpRequest();
    xhr.open('GET', '../api/film.php?action=list', true);
    xhr.onload = function () {
        if (xhr.status === 200) {
            var films = JSON.parse(xhr.responseText);
            select.innerHTML = '';
            if (films.length === 0) {
                select.innerHTML = '<option value="">No films — add some from your profile.</option>';
                return;
            }
            films.forEach(function (f) {
                var opt = document.createElement('option');
                opt.value       = f.id;
                opt.textContent = f.title + ' (id ' + f.id + ')';
                select.appendChild(opt);
            });
            loadFilm(films[0].id);
        }
    };
    xhr.send();
}

/* ── on select change: check dirty, then load ── */
select.addEventListener('change', function () {
    var newId = this.value;
    if (!newId) return;
    if (isDirty) {
        var doSave = confirm('You have unsaved changes. Save before switching?');
        if (doSave) {
            saveFilm(function () { loadFilm(newId); });
        } else {
            loadFilm(newId);
        }
    } else {
        loadFilm(newId);
    }
});

/* ── on field input: mark dirty ── */
fields.forEach(function (id) {
    document.getElementById(id).addEventListener('input', markDirty);
});

/* ── on save click ── */
btnSave.addEventListener('click', function () {
    saveFilm(null);
});

/* ── init ── */
loadFilmList();
</script>

<?php require_once __DIR__ . '/../php/footer.php'; ?>
