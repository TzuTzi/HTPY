# Vulnerabilities — How to Trigger Each One

> All vulnerabilities are **intentional** and exist for educational/demo purposes.
> The project must be running locally in XAMPP at `http://localhost/HTPY/`

---

## VULNERABILITY 1 — SQL Injection
**File:** `php/auth.php` line ~86
**Page:** `http://localhost/HTPY/login.php`

### How to trigger
1. Go to the login page
2. Enter the following in the **Username** field:
   ```
   ' OR '1'='1' #
   ```
3. Enter **anything** in the Password field
4. Solve the CAPTCHA and click **Sign In**

### What happens
The SQL query becomes:
```sql
SELECT id, role FROM users WHERE username = '' OR '1'='1' # LIMIT 1
```
`'1'='1'` is always true → the first user in the database is returned → **login bypass**.

### How to patch
In `php/auth.php`, comment out the vulnerable block and uncomment the `SECURE version` block below it (prepared statement + `password_verify`).

---

## VULNERABILITY 2 — XSS (Cross-Site Scripting)
**File:** `profile.php` line ~366
**Page:** `http://localhost/HTPY/profile.php` (must be logged in)

### How to trigger
1. Log in and go to your profile
2. In the **Add Film** form, enter the following in the **Title** field:
   ```
   <script>alert('XSS')</script>
   ```
3. Fill in any valid year (e.g. `2020`) and click **Add Film**
4. The film title is rendered without `htmlspecialchars` → **alert box fires**

### What happens
The title is printed raw: `<?= $f['title'] ?>` instead of `<?= htmlspecialchars($f['title']) ?>`.
Any HTML/JavaScript in the title executes in the browser.

### How to patch
In `profile.php` around line 366, replace:
```php
<?= $f['title'] ?>
```
with:
```php
<?= htmlspecialchars($f['title']) ?>
```

---

## VULNERABILITY 3 — CSRF (Cross-Site Request Forgery)
**File:** `profile.php` lines 25–52
**Page:** Any page — attack is triggered from an **external HTML file**

### How to trigger
1. Log in to the app
2. Save the following as `attacker.html` anywhere on your computer:
   ```html
   <form action="http://localhost/HTPY/profile.php" method="post" id="f">
       <input type="hidden" name="action" value="delete_film">
       <input type="hidden" name="film_id" value="1">
   </form>
   <script>document.getElementById('f').submit();</script>
   ```
3. Open `attacker.html` in your browser **while still logged in**
4. The form auto-submits → **film with id=1 is deleted silently**

### What happens
The delete form has no CSRF token. Any external page can forge a POST request on behalf of a logged-in user.

### How to patch
In `profile.php`, uncomment the two blocks marked `SECURE token generation` and `SECURE token validation`, then add `<?= $csrfField ?>` inside every sensitive form.

---

## VULNERABILITY 4 — Unrestricted File Upload
**File:** `profile.php` lines 71–98, `uploads/.htaccess`
**Page:** `http://localhost/HTPY/profile.php` (must be logged in)

### How to trigger
1. Log in and go to your profile
2. Create a file named `shell.php` with this content:
   ```
   <?php echo "FILE UPLOAD WORKS — PHP executes in uploads/"; ?>
   ```
3. Upload it as your **profile picture** (no extension check is done)
4. Visit: `http://localhost/HTPY/uploads/shell.php`
5. The PHP file executes → **Remote Code Execution possible**

### What makes it work
- No MIME type / extension check in `profile.php`
- `uploads/.htaccess` does NOT block PHP execution (comment out its 3 lines to demo)

### How to patch
In `profile.php`, uncomment the `elseif` MIME check block.
In `uploads/.htaccess`, make sure the 3 lines are **uncommented** (active).

---

## VULNERABILITY 5 — Path Traversal (Directory Traversal)
**File:** `download.php` line 10
**Page:** `http://localhost/HTPY/download.php` (must be logged in)

### How to trigger
1. Log in to the app
2. Visit the following URL in your browser:
   ```
   http://localhost/HTPY/download.php?file=../php/config.php
   ```
3. The server reads `uploads/../php/config.php` = `php/config.php`
4. **config.php is downloaded** — exposes DB credentials

### Other targets to try
```
download.php?file=../php/auth.php
download.php?file=../sql/schema.sql
download.php?file=../../xampp/passwords.txt
```

### What happens
`$path = __DIR__ . '/uploads/' . $file` — no sanitization of `../` sequences.
An attacker can walk up the directory tree and read any file the web server can access.

### How to patch
In `download.php`, comment out the vulnerable block and uncomment the `SECURE version` block (`basename($file)` strips all `../` traversal).

---

## Quick Reference Table

| # | Vulnerability      | Page            | Payload / Action                        |
|---|--------------------|-----------------|-----------------------------------------|
| 1 | SQL Injection      | login.php       | Username: `' OR '1'='1' #`             |
| 2 | XSS                | profile.php     | Film title: `<script>alert('XSS')</script>` |
| 3 | CSRF               | profile.php     | Open `attacker.html` while logged in    |
| 4 | File Upload        | profile.php     | Upload a `.php` file as avatar          |
| 5 | Path Traversal     | download.php    | `?file=../php/config.php`               |
