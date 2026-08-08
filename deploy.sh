#!/bin/bash
#===============================================================================
# ForMysha — First-Time Deployment Script
# Target: VPS aaPanel Ubuntu (22.04/24.04)
# Domain: formysha.my.id
#===============================================================================

set -euo pipefail

#-------------------------------------------------------------------------------
# Configuration — Sesuaikan dengan VPS Anda
#-------------------------------------------------------------------------------
DOMAIN="formysha.my.id"
APP_DIR="/www/wwwroot/${DOMAIN}"
REPO_URL="https://github.com/your-username/formysha.git"  # Ganti dengan repo Anda
BRANCH="main"
APP_USER="www"            # User default aaPanel untuk Nginx
PHP_VERSION="8.3"         # Versi PHP yang digunakan (sesuaikan dengan aaPanel)
DB_NAME="sql_formysha_my_id"
DB_USER="sql_formysha_my_id"
DB_PASS=""                # Isi dengan password database
REDIS_PASS=""             # Isi dengan password Redis (kosongkan jika tidak ada)

# Warna untuk output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

#-------------------------------------------------------------------------------
# Helper Functions
#-------------------------------------------------------------------------------
log_info()    { echo -e "${BLUE}[INFO]${NC} $1"; }
log_success() { echo -e "${GREEN}[OK]${NC} $1"; }
log_warn()    { echo -e "${YELLOW}[WARN]${NC} $1"; }
log_error()   { echo -e "${RED}[ERROR]${NC} $1"; exit 1; }

check_root() {
    if [[ $EUID -ne 0 ]]; then
        log_error "Script ini harus dijalankan sebagai root. Gunakan: sudo bash deploy.sh"
    fi
}

check_ubuntu() {
    if ! grep -qi "ubuntu" /etc/os-release 2>/dev/null; then
        log_warn "Script ini dikhususkan untuk Ubuntu. Beberapa langkah mungkin berbeda."
    fi
}

confirm() {
    local msg="${1:-Lanjutkan?}"
    echo -en "${YELLOW}${msg} [y/N]: ${NC}"
    read -r reply
    [[ "$reply" =~ ^[Yy]$ ]] || exit 0
}

#-------------------------------------------------------------------------------
# Step 0: Validasi Prerequisites
#-------------------------------------------------------------------------------
log_info "=== ForMysha First-Time Deployment ==="
log_info "Domain: ${DOMAIN}"
log_info "App Directory: ${APP_DIR}"
echo ""

check_root
check_ubuntu

# Cek apakah aaPanel terinstall
if ! command -v bt &>/dev/null && [[ ! -d "/www/server/panel" ]]; then
    log_warn "aaPanel tidak terdeteksi di VPS ini."
    log_warn "Pastikan aaPanel sudah terinstall. Jika belum, install dari: https://www.aapanel.com"
    confirm "Lanjutkan meskipun aaPanel belum terdeteksi?"
fi

# Cek PHP
if ! command -v "php${PHP_VERSION}" &>/dev/null && ! command -v php &>/dev/null; then
    log_error "PHP ${PHP_VERSION} tidak ditemukan. Install melalui aaPanel > App Store > PHP ${PHP_VERSION}"
fi

# Cek Composer
if ! command -v composer &>/dev/null; then
    log_info "Composer tidak ditemukan. Menginstall Composer..."
    cd /tmp
    curl -sS https://getcomposer.org/installer | php
    mv composer.phar /usr/local/bin/composer
    chmod +x /usr/local/bin/composer
    log_success "Composer berhasil diinstall."
fi

# Cek Node.js & NPM
if ! command -v node &>/dev/null; then
    log_error "Node.js tidak ditemukan. Install melalui aaPanel > App Store > Node.js"
fi
if ! command -v npm &>/dev/null; then
    log_error "npm tidak ditemukan. Install melalui aaPanel > App Store > Node.js"
fi

# Cek Git
if ! command -v git &>/dev/null; then
    log_info "Git tidak ditemukan. Menginstall Git..."
    apt-get update -qq && apt-get install -y -qq git
    log_success "Git berhasil diinstall."
fi

log_success "Semua prerequisites terpenuhi."
echo ""

#-------------------------------------------------------------------------------
# Step 1: Setup Directory & Clone Repository
#-------------------------------------------------------------------------------
log_info "Step 1: Setup directory dan clone repository..."

if [[ -d "${APP_DIR}/.git" ]]; then
    log_warn "Repository sudah ada di ${APP_DIR}. Melakukan git pull..."
    cd "${APP_DIR}"
    git pull origin "${BRANCH}"
else
    # Buat direktori parent jika belum ada
    mkdir -p "$(dirname "${APP_DIR}")"

    # Clone repository
    git clone -b "${BRANCH}" "${REPO_URL}" "${APP_DIR}"
    cd "${APP_DIR}"
fi

log_success "Repository siap di ${APP_DIR}"
echo ""

#-------------------------------------------------------------------------------
# Step 2: Setup Environment File
#-------------------------------------------------------------------------------
log_info "Step 2: Setup .env file..."

if [[ ! -f "${APP_DIR}/.env" ]]; then
    if [[ -f "${APP_DIR}/.env.production" ]]; then
        cp "${APP_DIR}/.env.production" "${APP_DIR}/.env"
        log_success ".env production template disalin."
    else
        cp "${APP_DIR}/.env.example" "${APP_DIR}/.env"
        log_warn ".env.example disalin. Harap edit manual!"
    fi

    # Generate APP_KEY
    cd "${APP_DIR}"
    php artisan key:generate --force
    log_success "APP_KEY berhasil di-generate."
else
    log_info ".env sudah ada. Melewati pembuatan .env."
fi
echo ""

#-------------------------------------------------------------------------------
# Step 3: Install PHP Dependencies
#-------------------------------------------------------------------------------
log_info "Step 3: Install PHP dependencies (Composer)..."

cd "${APP_DIR}"
composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction

log_success "Composer dependencies terinstall."
echo ""

#-------------------------------------------------------------------------------
# Step 4: Install Node Dependencies & Build Frontend
#-------------------------------------------------------------------------------
log_info "Step 4: Install Node.js dependencies dan build frontend..."

cd "${APP_DIR}"
npm ci --ignore-scripts
npm run build

log_success "Frontend berhasil di-build."
echo ""

#-------------------------------------------------------------------------------
# Step 5: Setup Database
#-------------------------------------------------------------------------------
log_info "Step 5: Setup database..."

if [[ -z "${DB_PASS}" ]]; then
    log_warn "DB_PASS kosong. Silakan edit bagian CONFIGURATION di script ini."
    log_warn "Atau set manual setelah deployment selesai."
    echo -en "${YELLOW}Masukkan password database: ${NC}"
    read -rs DB_PASS
    echo ""
fi

# Cek apakah database bisa diakses
cd "${APP_DIR}"
if php artisan db:monitor 2>/dev/null; then
    log_success "Database connection OK."
else
    log_warn "Tidak dapat memverifikasi database connection. Pastikan database sudah dibuat di aaPanel."
fi

# Jalankan migrations
log_info "Menjalankan migrations..."
php artisan migrate --force
log_success "Migrations berhasil dijalankan."

# Seed data jika diperlukan (hanya untuk fresh install)
echo -en "${YELLOW}Jalankan seeder? (super admin, plans, dll) [y/N]: ${NC}"
read -r seed_reply
if [[ "$seed_reply" =~ ^[Yy]$ ]]; then
    php artisan db:seed --force
    log_success "Seeder berhasil dijalankan."
fi
echo ""

#-------------------------------------------------------------------------------
# Step 6: Setup Permissions
#-------------------------------------------------------------------------------
log_info "Step 6: Setup file permissions..."

cd "${APP_DIR}"

# Set ownership ke user aaPanel (www)
chown -R "${APP_USER}:${APP_USER}" "${APP_DIR}"

# Set direktori permissions
chmod -R 755 "${APP_DIR}/storage"
chmod -R 755 "${APP_DIR}/bootstrap/cache"
chmod -R 755 "${APP_DIR}/public"

# Storage link
php artisan storage:link --force
chown -R "${APP_USER}:${APP_USER}" "${APP_DIR}/public/storage"

log_success "Permissions berhasil diatur."
echo ""

#-------------------------------------------------------------------------------
# Step 7: Optimize Laravel
#-------------------------------------------------------------------------------
log_info "Step 7: Optimize Laravel..."

cd "${APP_DIR}"
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

log_success "Laravel berhasil dioptimasi."
echo ""

#-------------------------------------------------------------------------------
# Step 8: Setup Queue Worker (Supervisor)
#-------------------------------------------------------------------------------
log_info "Step 8: Setup Queue Worker dengan Supervisor..."

SUPERVISOR_CONF="/etc/supervisor/conf.d/formysha-worker.conf"

cat > "${SUPERVISOR_CONF}" << EOF
[program:formysha-worker]
process_name=%(program_name)s_%(process_num)02d
command=php ${APP_DIR}/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=${APP_USER}
numprocs=2
redirect_stderr=true
stdout_logfile=${APP_DIR}/storage/logs/worker.log
stopwaitsecs=3600
EOF

supervisorctl reread
supervisorctl update
supervisorctl start "formysha-worker:*"

log_success "Queue worker berhasil dikonfigurasi."
echo ""

#-------------------------------------------------------------------------------
# Step 9: Setup Cron Job
#-------------------------------------------------------------------------------
log_info "Step 9: Setup Cron Job..."

CRON_LINE="* * * * * cd ${APP_DIR} && php artisan schedule:run >> /dev/null 2>&1"

# Cek apakah cron sudah ada untuk user www
if ! crontab -u "${APP_USER}" -l 2>/dev/null | grep -qF "formysha"; then
    (crontab -u "${APP_USER}" -l 2>/dev/null; echo "${CRON_LINE}") | crontab -u "${APP_USER}" -
    log_success "Cron job berhasil ditambahkan."
else
    log_info "Cron job sudah ada. Melewati."
fi
echo ""

#-------------------------------------------------------------------------------
# Step 10: Setup Nginx Configuration
#-------------------------------------------------------------------------------
log_info "Step 10: Setup Nginx configuration..."

# Cek apakah sudah ada config Nginx untuk domain ini
NGINX_CONF="/www/server/panel/vhost/nginx/${DOMAIN}.conf"
if [[ -f "${NGINX_CONF}" ]]; then
    log_warn "Nginx config sudah ada di ${NGINX_CONF}."
    confirm "Timpa Nginx config?"
fi

# Buat Nginx config
NGINX_CONF_CONTENT=$(cat << 'NGINXEOF'
server {
    listen 80;
    server_name DOMAIN_PLACEHOLDER;

    root /www/wwwroot/DOMAIN_PLACEHOLDER/public;
    index index.php index.html;

    # Security Headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;

    # Max Upload Size
    client_max_body_size 100M;

    # Gzip Compression
    gzip on;
    gzip_vary on;
    gzip_min_length 1024;
    gzip_types
        text/plain
        text/css
        text/javascript
        application/json
        application/javascript
        application/xml
        application/xml+rss
        image/svg+xml;

    # Static Assets Cache
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2|ttf|eot)$ {
        expires 30d;
        add_header Cache-Control "public, immutable";
        access_log off;
    }

    # Laravel Routes
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # PHP-FPM
    location ~ \.php$ {
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        fastcgi_pass unix:/tmp/php-cgi-${PHP_VERSION_PLACEHOLDER}.sock;
        fastcgi_index index.php;
        include fastcgi.conf;
        fastcgi_param PATH_INFO $fastcgi_path_info;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }

    # Deny .htaccess
    location ~ /\.ht {
        deny all;
    }

    # Deny sensitive files
    location ~ /\.(env|git) {
        deny all;
    }

    access_log /www/wwwlogs/DOMAIN_PLACEHOLDER-access.log;
    error_log /www/wwwlogs/DOMAIN_PLACEHOLDER-error.log;
}
NGINXEOF
)

NGINX_CONF_CONTENT=$(echo "${NGINX_CONF_CONTENT}" | sed "s|DOMAIN_PLACEHOLDER|${DOMAIN}|g")
NGINX_CONF_CONTENT=$(echo "${NGINX_CONF_CONTENT}" | sed "s|PHP_VERSION_PLACEHOLDER|${PHP_VERSION}|g")

echo "${NGINX_CONF_CONTENT}" > "${NGINX_CONF}"

# Reload Nginx
if command -v bt &>/dev/null; then
    bt restart nginx 2>/dev/null || nginx -s reload
else
    nginx -s reload 2>/dev/null || systemctl reload nginx
fi

log_success "Nginx berhasil dikonfigurasi."
echo ""

#-------------------------------------------------------------------------------
# Step 11: Setup SSL (Let's Encrypt)
#-------------------------------------------------------------------------------
log_info "Step 11: Setup SSL Certificate..."

echo -en "${YELLOW}Install SSL Certificate (Let's Encrypt)? [y/N]: ${NC}"
read -r ssl_reply
if [[ "$ssl_reply" =~ ^[Yy]$ ]]; then
    # Melalui aaPanel
    if command -v bt &>/dev/null; then
        log_info "Menggunakan aaPanel SSL Manager..."
        log_info "Buka: aaPanel > Website > ${DOMAIN} > SSL > Let's Encrypt"
        log_info "Atau jalankan melalui panel web aaPanel."
    else
        # Manual dengan certbot
        if command -v certbot &>/dev/null; then
            certbot --nginx -d "${DOMAIN}" -d "www.${DOMAIN}" --non-interactive --agree-tos --email "admin@${DOMAIN}"
        else
            log_warn "certbot tidak ditemukan. Install manual atau gunakan aaPanel SSL."
        fi
    fi
else
    log_info "SSL di-skip. Anda bisa mengaktifkan SSL nanti melalui aaPanel."
fi
echo ""

#-------------------------------------------------------------------------------
# Step 12: Setup Backup Cron
#-------------------------------------------------------------------------------
log_info "Step 12: Setup backup harian..."

BACKUP_DIR="/www/backup/formysha"
mkdir -p "${BACKUP_DIR}"

BACKUP_SCRIPT="${BACKUP_DIR}/backup.sh"
cat > "${BACKUP_SCRIPT}" << 'BACKUPEOF'
#!/bin/bash
# ForMysha Daily Backup Script
set -euo pipefail

APP_DIR="__APP_DIR__"
BACKUP_DIR="__BACKUP_DIR__"
DATE=$(date +%Y%m%d_%H%M%S)
DB_NAME="__DB_NAME__"
DB_USER="__DB_USER__"
DB_PASS="__DB_PASS__"

# Backup Database
mysqldump -u"${DB_USER}" -p"${DB_PASS}" "${DB_NAME}" \
    | gzip > "${BACKUP_DIR}/db_${DATE}.sql.gz"

# Backup Storage
tar -czf "${BACKUP_DIR}/storage_${DATE}.tar.gz" \
    -C "${APP_DIR}" storage/app

# Backup .env
cp "${APP_DIR}/.env" "${BACKUP_DIR}/env_${DATE}.bak"

# Cleanup backup lebih dari 30 hari
find "${BACKUP_DIR}" -name "*.sql.gz" -mtime +30 -delete
find "${BACKUP_DIR}" -name "*.tar.gz" -mtime +30 -delete
find "${BACKUP_DIR}" -name "*.bak" -mtime +30 -delete

echo "[$(date)] Backup selesai: ${DATE}" >> "${BACKUP_DIR}/backup.log"
BACKUPEOF

# Replace placeholders
sed -i "s|__APP_DIR__|${APP_DIR}|g" "${BACKUP_SCRIPT}"
sed -i "s|__BACKUP_DIR__|${BACKUP_DIR}|g" "${BACKUP_SCRIPT}"
sed -i "s|__DB_NAME__|${DB_NAME}|g" "${BACKUP_SCRIPT}"
sed -i "s|__DB_USER__|${DB_USER}|g" "${BACKUP_SCRIPT}"
sed -i "s|__DB_PASS__|${DB_PASS}|g" "${BACKUP_SCRIPT}"

chmod +x "${BACKUP_SCRIPT}"
chown -R "${APP_USER}:${APP_USER}" "${BACKUP_DIR}"

# Tambahkan backup cron (jam 3 pagi setiap hari)
BACKUP_CRON="0 3 * * * ${BACKUP_SCRIPT} >> ${BACKUP_DIR}/backup.log 2>&1"
if ! crontab -u "${APP_USER}" -l 2>/dev/null | grep -qF "backup.sh"; then
    (crontab -u "${APP_USER}" -l 2>/dev/null; echo "${BACKUP_CRON}") | crontab -u "${APP_USER}" -
fi

log_success "Backup cron job berhasil diatur."
echo ""

#-------------------------------------------------------------------------------
# Step 13: Verify Deployment
#-------------------------------------------------------------------------------
log_info "Step 13: Verifikasi deployment..."

cd "${APP_DIR}"

# Cek artisan
if php artisan --version &>/dev/null; then
    log_success "Laravel $(php artisan --version) OK"
else
    log_error "Artisan tidak berjalan!"
fi

# Cek database connection
if php artisan db:monitor 2>/dev/null; then
    log_success "Database connection OK"
fi

# Cek queue worker
if supervisorctl status formysha-worker:* 2>/dev/null | grep -q "RUNNING"; then
    log_success "Queue worker OK"
else
    log_warn "Queue worker mungkin belum running. Cek: supervisorctl status"
fi

# Cek storage link
if [[ -L "${APP_DIR}/public/storage" ]]; then
    log_success "Storage link OK"
fi

echo ""
log_success "============================================"
log_success "  DEPLOYMENT FOR MYSHA SELESAI!"
log_success "============================================"
log_success ""
log_success "  URL: https://${DOMAIN}"
log_success "  App Dir: ${APP_DIR}"
log_success "  Backup Dir: ${BACKUP_DIR}"
log_success ""
log_success "  Selanjutnya:"
log_success "  1. Edit .env jika ada yang perlu diubah"
log_success "  2. Akses website untuk memastikan berjalan"
log_success "  3. Setup SSL melalui aaPanel jika belum"
log_success "  4. Test login dengan akun super admin"
log_success ""
log_success "  Useful Commands:"
log_success "  - Lihat logs: tail -f ${APP_DIR}/storage/logs/laravel.log"
log_success "  - Queue status: supervisorctl status"
log_success "  - Restart queue: supervisorctl restart formysha-worker:*"
log_success "  - Clear cache: cd ${APP_DIR} && php artisan cache:clear"
log_success ""
