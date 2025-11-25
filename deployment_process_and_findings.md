# Deployment Process and Findings
**Project:** newUrCost (Laravel 12)
**Target Environment:** Ubuntu 22.04 Production Server
**Deployment Path:** `/var/www/newUrCost`
**Public Access:** `https://pwduk.in/ukSor/` (Subdirectory Deployment)

---

## 1. Deployment Architecture
The application is deployed in a subdirectory (`/ukSor`) using a symlink strategy.
- **Codebase:** `/var/www/newUrCost`
- **Web Root Symlink:** `/var/www/html/ukSor` -> `/var/www/newUrCost/public`
- **Web Server:** Apache

## 2. Deployment Process

### Step 1: Initial Setup
1.  **Clone Repository:** Code cloned to `/var/www/newUrCost`.
2.  **Install Dependencies:**
    ```bash
    composer install --optimize-autoloader --no-dev
    npm install && npm run build
    ```
3.  **Environment Configuration:**
    - Copied `.env.example` to `.env`.
    - Configured Database and App URL (`APP_URL=https://pwduk.in/ukSor`).
    - Generated key: `php artisan key:generate`.

### Step 2: Symlink Creation
To serve the app from a subdirectory without exposing the entire project:
```bash
sudo ln -s /var/www/newUrCost/public /var/www/html/ukSor
```
*Correction:* Initially, the symlink pointed to the project root. It **MUST** point to the `public` directory.

### Step 3: Permissions
Ensure the web server can write to storage and cache:
```bash
sudo chown -R www-data:www-data /var/www/newUrCost
sudo chmod -R 775 /var/www/newUrCost/storage
sudo chmod -R 775 /var/www/newUrCost/bootstrap/cache
```

---

## 3. Issues Encountered & Solutions

### Issue 1: 403 Forbidden / AH01276 Error
**Symptom:** Accessing `/ukSor/` resulted in a 403 error. Apache logs showed `AH01276: Cannot serve directory /var/www/html/ukSor/: No matching DirectoryIndex`.
**Root Cause:**
1.  Symlink was pointing to the project root, not `public`.
2.  Apache configuration didn't allow following symlinks or overriding indexes.
**Solution:**
1.  Corrected symlink: `ln -s .../public .../ukSor`.
2.  Updated `public/.htaccess` (see below).

### Issue 2: Git Pull Permission Denied
**Symptom:** `error: unable to unlink old 'package.json': Permission denied` during `git pull`.
**Root Cause:** Files owned by `www-data` (Apache) could not be modified by the deployment user (`ubuntu` or `root`).
**Solution:**
1.  Add directory to safe list: `git config --global --add safe.directory /var/www/newUrCost`.
2.  Temporarily change ownership to deploy user, pull, then revert to `www-data`.
    ```bash
    sudo chown -R $USER:$USER /var/www/newUrCost
    git pull origin main
    sudo chown -R www-data:www-data /var/www/newUrCost
    ```

### Issue 3: Missing Data in Frontend (Hardcoded URLs)
**Symptom:** Rate Analysis and Skeleton pages loaded but showed "No resources found" or empty tables.
**Root Cause:** The JavaScript code had hardcoded API paths like `/api/sors/...`.
- In a root deployment, this hits `domain.com/api/...` (Correct).
- In a subdirectory deployment (`/ukSor`), this hits `domain.com/api/...` (Incorrect, should be `domain.com/ukSor/api/...`).
**Solution:**
Replaced all hardcoded paths in `scripts.blade.php` with Laravel's `route()` helper:
```javascript
// Before
url: `/api/sors/${sorId}/items/${itemId}/skeleton`

// After
url: "{{ route('api.sors.items.skeleton.show', ['sor' => $sor->id, 'item' => $item->id]) }}"
```
This ensures the URL automatically includes the correct base path (`/ukSor`).

### Issue 4: 405 Method Not Allowed on Root URL
**Symptom:** Visiting `https://pwduk.in/ukSor/` returned "405 Method Not Allowed", but `/ukSor/index.php` worked.
**Root Cause:**
1.  **Stale Route Cache:** Laravel's route cache was outdated or corrupted, causing it to misinterpret the root request.
2.  **Apache Handling:** Apache might not have been strictly handing off the empty path `^$` to `index.php`.
**Solution:**
1.  Added explicit rewrite rule to `.htaccess`:
    ```apache
    RewriteRule ^$ index.php [L]
    ```
2.  **CRITICAL FIX:** Cleared the route cache.
    ```bash
    php artisan route:clear
    ```

---

## 4. Final Configuration Files

### `.htaccess` (in `/var/www/newUrCost/public/`)
```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    DirectoryIndex index.php

    RewriteEngine On
    RewriteBase /ukSor/
    RewriteRule ^$ index.php [L]

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

---

## 5. Standard Deployment Checklist
For future updates, follow this procedure to avoid permissions and cache issues:

1.  **Permissions:**
    ```bash
    sudo chown -R $USER:$USER /var/www/newUrCost
    ```
2.  **Update Code:**
    ```bash
    git pull origin main
    ```
3.  **Dependencies (if changed):**
    ```bash
    composer install --no-dev
    npm install && npm run build
    ```
4.  **Database:**
    ```bash
    php artisan migrate --force
    ```
5.  **Caches (CRITICAL):**
    ```bash
    php artisan optimize:clear
    php artisan view:cache
    # Do NOT run route:cache if you have closure routes, but route:clear is safe
    php artisan route:clear
    ```
6.  **Restore Permissions:**
    ```bash
    sudo chown -R www-data:www-data /var/www/newUrCost
    sudo chmod -R 775 /var/www/newUrCost/storage
    ```
