# Fix Error `tempnam()` di Production

## Error

```
ErrorException: tempnam(): file created in the system's temporary directory
```

HTTP 500 Internal Server Error — terjadi saat upload media di production.

## Stack Trace

```
Filesystem.php (line 222) → tempnam()
BladeCompiler.php (line 199) → replace()
CompilerEngine.php → compile
```

## Root Cause Analysis

### Apa yang Terjadi

Error ini terjadi saat **kompilasi Blade template** — bukan saat upload file-nya. Laravel's Blade compiler memanggil `Filesystem::tempnam()` untuk membuat file temporary saat mengkompilasi view. PHP's `tempnam()` gagal membuat file di temporary directory yang ditentukan.

### Mengapa Muncul Saat Upload Media

Halaman upload media menggunakan Blade template yang perlu dikompilasi ulang. Ketika:
1. Template belum di-cache, ATAU
2. Cache-nya invalid/ expired

Laravel mencoba kompilasi ulang → memanggil `tempnam()` → gagal → error 500.

### Penyebab Kemungkinan

Ini adalah masalah **server-side configuration**, kemungkinan salah satu:

1. **System temporary directory tidak writable** oleh PHP-FPM process
2. **`storage/framework/views` tidak writable**
3. **Disk space server penuh**
4. **PHP `open_basedir` restriction** memblokir akses ke temp directory
5. **PHP 8.1+ `tempnam()` behavior** — lebih strict dalam menangani warning

### Verifikasi

- [`MediaService.php`](app/Services/MediaService.php) — tidak ada masalah di kode
- [`ImageOptimizationService.php`](app/Services/ImageOptimizationService.php) — tidak ada masalah di kode
- [`media-upload.blade.php`](resources/views/components/media-upload.blade.php) — tidak ada masalah di kode
- Semua view yang menggunakan `<x-media-upload>` — tidak ada masalah di kode

**Kesimpulan: Ini murni masalah server configuration, bukan bug kode aplikasi.**

## Rencana Perbaikan

### Langkah 1 — Diagnosa Server (SSH ke Production)

```bash
# 1. Cek system temp directory
php -r "echo sys_get_temp_dir();"
ls -la $(php -r "echo sys_get_temp_dir();")

# 2. Cek permission storage/framework/views
ls -la /www/wwwroot/formysha.my.id/storage/framework/views/

# 3. Cek disk space
df -h

# 4. Cek PHP version
php -v

# 5. Cek open_basedir
php -i | grep open_basedir

# 6. Cek upload_tmp_dir
php -i | grep upload_tmp_dir

# 7. Cek writable test
php -r "var_dump(is_writable(sys_get_temp_dir()));"
php -r "var_dump(is_writable('/www/wwwroot/formysha.my.id/storage/framework/views/'));"
```

### Langkah 2 — Fix Permission (Jika Permission Issue)

```bash
# Fix permission untuk storage directory
chmod -R 775 /www/wwwroot/formysha.my.id/storage/
chown -R www:www /www/wwwroot/formysha.my.id/storage/

# Pastikan views directory writable
chmod 775 /www/wwwroot/formysha.my.id/storage/framework/views/

# Jika menggunakan PHP-FPM dengan user tertentu (ganti 'www' dengan user yang tepat)
# Cek user PHP-FPM:
ps aux | grep php-fpm
```

### Langkah 3 — Fix Temporary Directory (Jika Temp Directory Issue)

```bash
# Cek apakah /tmp writable
ls -la /tmp
chmod 1777 /tmp

# Jika /tmp tidak ada, buat
mkdir -p /tmp
chmod 1777 /tmp

# Atau buat custom temp directory
mkdir -p /www/wwwroot/formysha.my.id/storage/tmp
chmod 775 /www/wwwroot/formysha.my.id/storage/tmp
```

### Langkah 4 — Fix open_basedir (Jika Ada Restriction)

Jika PHP-FPM menggunakan `open_basedir` yang membatasi akses ke `/tmp`:

```bash
# Cari konfigurasi PHP-FPM pool
find /etc/php -name "*.conf" -path "*/pool.d/*"

# Edit pool config, tambahkan /tmp ke open_basedir
# Contoh:
# open_basedir = /www/wwwroot/formysha.my.id/:/tmp/:/proc/
```

### Langkah 5 — Clear Cache & Compile Ulang

```bash
cd /www/wwwroot/formysha.my.id

# Clear semua cache
php artisan view:clear
php artisan cache:clear
php artisan config:clear
php artisan route:clear

# Recache
php artisan view:cache
php artisan config:cache
php artisan route:cache

# Verify
php artisan about
```

### Langkah 6 — Fix Disk Space (Jika Penuh)

```bash
# Cek disk usage
df -h
du -sh /www/wwwroot/formysha.my.id/storage/*

# Bersihkan logs
php artisan log:clear
truncate -s 0 /www/wwwroot/formysha.my.id/storage/logs/*.log

# Bersihkan cache lama
find /www/wwwroot/formysha.my.id/storage/framework/cache -type f -mtime +30 -delete
find /www/wwwroot/formysha.my.id/storage/framework/views -name "*.tmp" -delete
```

### Langkah 7 — Cleanup .tmp Files di Views Directory

Ada beberapa file `.tmp` di `storage/framework/views/` yang seharusnya tidak ada:

```
1d8731A.tmp
5b933EE.tmp
07d2E27.tmp
27cF0EE.tmp
91bC7F8.tmp
20518CF.tmp
f4391B8.tmp
```

File-file ini adalah residual dari `tempnam()` yang gagal. Bersihkan:

```bash
find /www/wwwroot/formysha.my.id/storage/framework/views/ -name "*.tmp" -delete
```

### Langkah 8 — Verifikasi Fix

```bash
# Test upload media melalui browser
# Atau test via artisan:
php artisan tinker --execute '
$app = app();
$uploader = $app->make(\App\Services\MediaService::class);
echo "MediaService loaded successfully";
'
```

### Langkah 9 — Monitoring Pasca-Fix

```bash
# Monitor error log
tail -f /www/wwwroot/formysha.my.id/storage/logs/laravel.log

# Atau jika pakai Nginx error log
tail -f /var/log/nginx/formysha.my.id-error.log
```

## Prevention

### 1. Storage Permission di Deploy Script

Tambahkan ke [`update.sh`](update.sh) atau deploy script:

```bash
# Fix permissions
chmod -R 775 storage/ bootstrap/cache/
chown -R www:www storage/ bootstrap/cache/

# Clear and rebuild cache
php artisan view:clear
php artisan view:cache
```

### 2. Disk Space Monitoring

```bash
# Tambahkan cron job untuk monitoring disk
# Cek setiap jam, alert jika > 90%
*/60 * * * * df -h / | awk 'NR==2 {if ($5 > 90) print "DISK WARNING: " $5 " used"}' | mail -s "Disk Alert" admin@formysha.my.id
```

### 3. Health Check Endpoint

Buat endpoint `/health` yang memverifikasi:
- Database connection
- Storage writable
- Cache working
- Temp directory accessible

## Referensi

- PHP `tempnam()` documentation: https://www.php.net/manual/en/function.tempnam.php
- Laravel Blade compilation: `Illuminate\View\Compilers\BladeCompiler`
- Laravel Filesystem: `Illuminate\Filesystem\Filesystem::tempnam()`
