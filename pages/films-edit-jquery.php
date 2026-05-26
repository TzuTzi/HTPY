<?php
/* ============================================================
   Cerința 6 — Editare film cu jQuery AJAX
   Aceeași funcționalitate ca Cerința 5, implementată cu jQuery
   ============================================================ */
require_once __DIR__ . '/../php/auth.php';
require_login('../login.php');
$pageTitle = 'C6 – jQuery AJAX Edit';
require_once __DIR__ . '/../php/header.php';
?>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<table class="section-panel">
  <tr><td>
    <table class="section-inner">
      <tr class="section-head-row">
        <td>
          <h2>Cerința 6 — Editare film – jQuery AJAX</h2>
          <small style="color:var(--color-text-muted);font-weight:400;">
            Aceeași funcționalitate ca C5, implementată cu jQuery
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
            <td class="form-field"><input type="text" id="f-title" style="width:100%;"></td>
          </tr>
          <tr>
            <td class="form-label"><label for="f-year">Year</label></td>
            <td class="form-field"><input type="number" id="f-year" min="1888" max="2099" style="width:100%;"></td>
          </tr>
          <tr>
            <td class="form-label"><label for="f-genre">Genre</label></td>
            <td class="form-field"><input type="text" id="f-genre" style="width:100%;"></td>
          </tr>
          <tr>
            <td class="form-label"><label for="f-rating">Rating</label></td>
            <td class="form-field"><input type="number" id="f-rating" min="0" max="10" step="0.1" style="width:100%;"></td>
          </tr>
          <tr>
            <td class="form-label"><label for="f-mood">Mood</label></td>
            <td class="form-field"><input type="text" id="f-mood" style="width:100%;"></td>
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
/* ── Cerința 6: jQuery AJAX Edit ────────────────────────────── */
$(function () {
    var isDirty   = false;
    var currentId = null;

    function showMsg(text, ok) {
        $('#msg').text(text).css('color', ok ? '#3fb950' : '#f85149');
    }

    function markClean() {
        isDirty = false;
        $('#btn-save').prop('disabled', true);
    }

    function markDirty() {
        isDirty = true;
        $('#btn-save').prop('disabled', false);
    }

    function fillForm(film) {
        $('#f-title').val(film.title  || '');
        $('#f-year').val(film.year    || '');
        $('#f-genre').val(film.genre  || '');
        $('#f-rating').val(film.rating || '');
        $('#f-mood').val(film.mood    || '');
        markClean();
    }

    /* ── load single film ── */
    function loadFilm(id) {
        $.ajax({
            url: '../api/film.php',
            method: 'GET',
            data: { action: 'get', id: id },
            dataType: 'json',
            success: function (film) {
                if (film) {
                    currentId = id;
                    fillForm(film);
                    showMsg('', true);
                }
            },
            error: function () { showMsg('Failed to load film.', false); }
        });
    }

    /* ── save film ── */
    function saveFilm(callback) {
        if (!currentId) return;
        var data = {
            id:     parseInt(currentId, 10),
            title:  $('#f-title').val(),
            year:   parseInt($('#f-year').val(), 10),
            genre:  $('#f-genre').val(),
            rating: parseFloat($('#f-rating').val()),
            mood:   $('#f-mood').val()
        };
        $.ajax({
            url: '../api/film.php',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(data),
            dataType: 'json',
            success: function (res) {
                if (res.success) {
                    markClean();
                    showMsg('Saved successfully.', true);
                    $('#film-select option[value="' + currentId + '"]')
                        .text(data.title + ' (id ' + currentId + ')');
                    if (typeof callback === 'function') callback();
                } else {
                    showMsg('Save failed.', false);
                }
            },
            error: function () { showMsg('Save error.', false); }
        });
    }

    /* ── populate select ── */
    function loadFilmList() {
        $.ajax({
            url: '../api/film.php',
            method: 'GET',
            data: { action: 'list' },
            dataType: 'json',
            success: function (films) {
                var $sel = $('#film-select').empty();
                if (films.length === 0) {
                    $sel.append($('<option>').val('').text('No films — add some from your profile.'));
                    return;
                }
                $.each(films, function (i, f) {
                    $sel.append($('<option>').val(f.id).text(f.title + ' (id ' + f.id + ')'));
                });
                loadFilm(films[0].id);
            },
            error: function () { showMsg('Failed to load film list.', false); }
        });
    }

    /* ── on select change ── */
    $('#film-select').on('change', function () {
        var newId = $(this).val();
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

    /* ── on field input ── */
    $('#f-title, #f-year, #f-genre, #f-rating, #f-mood').on('input', markDirty);

    /* ── on save click ── */
    $('#btn-save').on('click', function () {
        saveFilm(null);
    });

    /* ── init ── */
    loadFilmList();
});
</script>

<?php require_once __DIR__ . '/../php/footer.php'; ?>
