#!/bin/bash
#===============================================================================
# ForMysha — First-Time Deployment Script
# Target: VPS aaPanel Ubuntu (22.04/24.04)
# Domain: formysha.my.id
#
# Fitur:
#   - Resume mechanism: jika script gagal di tengah, jalankan ulang untuk
#     melanjutkan dari step terakhir yang berhasil.
#   - Error handling: chown/touch pada file aaPanel yang dikunci di-skip.
#   - Step tracking: setiap step ditandai di /tmp/formysha_deploy_step
#===============================================================================

set -euo pipefail

#-------------------------------------------------------------------------------
# Configuration — Sesuaikan dengan VPS Anda
#-------------------------------------------------------------------------------
DOMAIN="formysha.my.id"
APP_DIR="/www/wwwroot/${DOMAIN}"
REPO_URL="https://github.com/wahyudedik/formysha.git"
BRANCH="main"
APP_USER="www"
PHP_VERSION="8.3"
DB_NAME="sql_formysha_my_id"
DB_USER="sql_formysha_my_id"
DB_PASS="3aaf5594628808"
REDIS_PASS="cdfe97af2103606c"

# Step tracking file untuk resume
STEP_FILE="/tmp/formysha_deploy_step"

# Warna untuk output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

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

# Resume mechanism: tandai step selesai
mark_step() {
    local step_num=$1
    echo "${step_num}" > "${STEP_FILE}"
    log_success "Step ${step_num} selesai."
}

# Resume mechanism: cek apakah step sudah dilakukan
is_step_done() {
    local step_num=$1
    if [[ -f "${STEP_FILE}" ]]; then
        local last_step
        last_step=$(cat "${STEP_FILE}")
        if [[ "${last_step}" -ge "${step_num}" ]]; then
            return 0  # sudah selesai
        fi
    fi
    return 1  # belum selesai
}

# Safe chown: skip file yang dikunci aaPanel
safe_chown() {
    local target="$1"
    find "${target}" \
        -not -path "*/.user.ini" \
        -not -name ".user.ini" \
        -exec chown -R "${APP_USER}:${APP_USER}" {} + 2>/dev/null || true

    # Log warning untuk file yang di-skip
    local skipped
    skipped=$(find "${target}" -name ".user.ini" 2>/dev/null | wc -l)
    if [[ "${skipped}" -gt 0 ]]; then
        log_warn "Dilewati ${skipped} file .user.ini (dikunci oleh aaPanel)."
    fi
}

#-------------------------------------------------------------------------------
# Header
#-------------------------------------------------------------------------------
echo ""
echo -e "${BLUE}╔══════════════════════════════════════════════════╗${NC}"
echo -e "${BLUE}║     ForMysha First-Time Deployment               ║${NC}"
echo -e "${BLUE}╚══════════════════════════════════════════════════╝${NC}"
echo ""

# Resume check
if [[ -f "${STEP_FILE}" ]]; then
    local_last_step=$(cat "${STEP_FILE}")
    log_info "Resume terdeteksi! Step terakhir yang selesai: ${local_last_step}"
    log_info "Script akan melanjutkan dari step berikutnya."
    echo ""
else
    log_info "Fresh deployment dimulai."
fi

log_info "Domain: ${DOMAIN}"
log_info "App Directory: ${APP_DIR}"
echo ""

check_root
check_ubuntu

#-------------------------------------------------------------------------------
# Step 0: Validasi Prerequisites
#-------------------------------------------------------------------------------
if ! is_step_done 0; then
    log_info "Step 0: Validasi prerequisites..."

    # Cek aaPanel
    if ! command -v bt &>/dev/null && [[ ! -d "/www/server/panel" ]]; then
        log_warn "aaPanel tidak terdeteksi di VPS ini."
    fi

    # Cek PHP
    if ! command -v "php${PHP_VERSION}" &>/dev/null && ! command -v php &>/dev/null; then
        log_error "PHP ${PHP_VERSION} tidak ditemukan. Install via aaPanel > App Store > PHP ${PHP_VERSION}"
    fi

    # Cek Composer
    if ! command -v composer &>/dev/null; then
        log_info "Composer tidak ditemukan. Menginstall..."
        cd /tmp
        curl -sS https://getcomposer.org/installer | php
        mv composer.phar /usr/local/bin/composer
        chmod +x /usr/local/bin/composer
        log_success "Composer terinstall."
    fi

    # Cek Node.js
    if ! command -v node &>/dev/null; then
        log_error "Node.js tidak ditemukan. Install via aaPanel > App Store > Node.js"
    fi

    # Cek Git
    if ! command -v git &>/dev/null; then
        log_info "Git tidak ditemukan. Menginstall..."
        apt-get update -qq && apt-get install -y -qq git
    fi

    mark_step 0
fi

#-------------------------------------------------------------------------------
# Step 1: Setup Directory & Clone Repository
#-------------------------------------------------------------------------------
if ! is_step_done 1; then
    log_info "Step 1: Setup directory dan clone repository..."

    if [[ -d "${APP_DIR}/.git" ]]; then
        log_info "Repository sudah ada. Melakukan git pull..."
        cd "${APP_DIR}"
        git pull origin "${BRANCH}" || log_warn "Git pull gagal. Mencoba fetch + reset..."
        git fetch origin "${BRANCH}" 2>/dev/null || true
    else
        mkdir -p "$(dirname "${APP_DIR}")"
        git clone -b "${BRANCH}" "${REPO_URL}" "${APP_DIR}"
        cd "${APP_DIR}"
    fi

    mark_step 1
fi

#-------------------------------------------------------------------------------
# Step 2: Setup Environment File
#-------------------------------------------------------------------------------
if ! is_step_done 2; then
    log_info "Step 2: Setup .env file..."

    cd "${APP_DIR}"

    if [[ ! -f "${APP_DIR}/.env" ]]; then
        if [[ -f "${APP_DIR}/.env.production" ]]; then
            cp "${APP_DIR}/.env.production" "${APP_DIR}/.env"
            log_success ".env production template disalin."
        else
            cp "${APP_DIR}/.env.example" "${APP_DIR}/.env"
            log_warn ".env.example disalin. Harap edit manual!"
        fi

        php artisan key:generate --force
        log_success "APP_KEY di-generate."
    else
        log_info ".env sudah ada. Melewati."
    fi

    mark_step 2
fi

#-------------------------------------------------------------------------------
# Step 3: Install PHP Dependencies
#-------------------------------------------------------------------------------
if ! is_step_done 3; then
    log_info "Step 3: Install PHP dependencies (Composer)..."

    cd "${APP_DIR}"

    export COMPOSER_ALLOW_SUPERUSER=1
    composer install \
        --no-dev \
        --optimize-autoloader \
        --no-interaction

    mark_step 3
fi

#-------------------------------------------------------------------------------
# Step 4: Install Node Dependencies & Build Frontend
#-------------------------------------------------------------------------------
if ! is_step_done 4; then
    log_info "Step 4: Install Node.js dependencies dan build frontend..."

    cd "${APP_DIR}"
    npm ci --ignore-scripts
    npm run build

    mark_step 4
fi

#-------------------------------------------------------------------------------
# Step 5: Setup Database
#-------------------------------------------------------------------------------
if ! is_step_done 5; then
    log_info "Step 5: Setup database..."

    cd "${APP_DIR}"

    # Jalankan migrations
    log_info "Menjalankan migrations..."
    php artisan migrate --force
    log_success "Migrations berhasil."

    # Seed (opsional)
    echo -en "${YELLOW}Jalankan seeder? (super admin, plans, dll) [y/N]: ${NC}"
    read -r seed_reply
    if [[ "$seed_reply" =~ ^[Yy]$ ]]; then
        php artisan db:seed --force
        log_success "Seeder berhasil."
    fi

    mark_step 5
fi

#-------------------------------------------------------------------------------
# Step 6: Setup Permissions & Storage Link
#-------------------------------------------------------------------------------
if ! is_step_done 6; then
    log_info "Step 6: Setup file permissions..."

    cd "${APP_DIR}"

    # Set ownership (safe_chown skip file .user.ini yang dikunci aaPanel)
    safe_chown "${APP_DIR}"

    # Set direktori permissions
    chmod -R 755 "${APP_DIR}/storage" 2>/dev/null || true
    chmod -R 755 "${APP_DIR}/bootstrap/cache" 2>/dev/null || true
    chmod -R 755 "${APP_DIR}/public" 2>/dev/null || true

    # Storage link
    php artisan storage:link --force 2>/dev/null || log_warn "Storage link mungkin sudah ada."
    safe_chown "${APP_DIR}/public/storage"

    mark_step 6
fi

#-------------------------------------------------------------------------------
# Step 7: Optimize Laravel
#-------------------------------------------------------------------------------
if ! is_step_done 7; then
    log_info "Step 7: Optimize Laravel..."

    cd "${APP_DIR}"
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    php artisan event:cache

    mark_step 7
fi

#-------------------------------------------------------------------------------
# Step 8: Setup Queue Worker (Supervisor)
#-------------------------------------------------------------------------------
if ! is_step_done 8; then
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

    supervisorctl reread 2>/dev/null || true
    supervisorctl update 2>/dev/null || true
    supervisorctl start "formysha-worker:*" 2>/dev/null || true

    mark_step 8
fi

#-------------------------------------------------------------------------------
# Step 9: Setup Cron Job
#-------------------------------------------------------------------------------
if ! is_step_done 9; then
    log_info "Step 9: Setup Cron Job..."

    CRON_LINE="* * * * * cd ${APP_DIR} && php artisan schedule:run >> /dev/null 2>&1"

    if ! crontab -u "${APP_USER}" -l 2>/dev/null | grep -qF "formysha"; then
        (crontab -u "${APP_USER}" -l 2>/dev/null; echo "${CRON_LINE}") | crontab -u "${APP_USER}" -
        log_success "Cron job ditambahkan."
    else
        log_info "Cron job sudah ada. Melewati."
    fi

    mark_step 9
fi

#-------------------------------------------------------------------------------
# Step 10: Setup Nginx Configuration
#-------------------------------------------------------------------------------
if ! is_step_done 10; then
    log_info "Step 10: Setup Nginx configuration..."

    NGINX_CONF="/www/server/panel/vhost/nginx/${DOMAIN}.conf"

    # Buat Nginx config dengan heredoc langsung (bukan variable)
    mkdir -p "$(dirname "${NGINX_CONF}")"

    cat > "${NGINX_CONF}" << NGINXEOF
server {
    listen 80;
    server_name ${DOMAIN};

    root ${APP_DIR}/public;
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
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    # PHP-FPM
    location ~ \.php\$ {
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        fastcgi_pass unix:/tmp/php-cgi-${PHP_VERSION}.sock;
        fastcgi_index index.php;
        include fastcgi.conf;
        fastcgi_param PATH_INFO \$fastcgi_path_info;
        fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
    }

    # Deny .htaccess
    location ~ /\.ht {
        deny all;
    }

    # Deny sensitive files
    location ~ /\.(env|git) {
        deny all;
    }

    access_log /www/wwwlogs/${DOMAIN}-access.log;
    error_log /www/wwwlogs/${DOMAIN}-error.log;
}
NGINXEOF

    # Reload Nginx
    if command -v bt &>/dev/null; then
        bt restart nginx 2>/dev/null || nginx -s reload 2>/dev/null || true
    else
        nginx -s reload 2>/dev/null || systemctl reload nginx 2>/dev/null || true
    fi

    log_success "Nginx dikonfigurasi."
    mark_step 10
fi

#-------------------------------------------------------------------------------
# Step 11: Setup SSL (Let's Encrypt)
#-------------------------------------------------------------------------------
if ! is_step_done 11; then
    log_info "Step 11: Setup SSL Certificate..."

    echo -en "${YELLOW}Install SSL Certificate (Let's Encrypt)? [y/N]: ${NC}"
    read -r ssl_reply
    if [[ "$ssl_reply" =~ ^[Yy]$ ]]; then
        if command -v bt &>/dev/null; then
            log_info "Menggunakan aaPanel SSL Manager..."
            log_info "Buka: aaPanel > Website > ${DOMAIN} > SSL > Let's Encrypt"
        else
            if command -v certbot &>/dev/null; then
                certbot --nginx -d "${DOMAIN}" -d "www.${DOMAIN}" \
                    --non-interactive --agree-tos --email "admin@${DOMAIN}" || true
            else
                log_warn "certbot tidak ditemukan. Install manual atau gunakan aaPanel."
            fi
        fi
    else
        log_info "SSL di-skip."
    fi

    mark_step 11
fi

#-------------------------------------------------------------------------------
# Step 12: Setup Backup Cron
#-------------------------------------------------------------------------------
if ! is_step_done 12; then
    log_info "Step 12: Setup backup harian..."

    BACKUP_DIR="/www/backup/formysha"
    mkdir -p "${BACKUP_DIR}"

    BACKUP_SCRIPT="${BACKUP_DIR}/backup.sh"
    cat > "${BACKUP_SCRIPT}" << BACKUPEOF
#!/bin/bash
# ForMysha Daily Backup Script
set -euo pipefail

APP_DIR="${APP_DIR}"
BACKUP_DIR="${BACKUP_DIR}"
DATE=\$(date +%Y%m%d_%H%M%S)
DB_NAME="${DB_NAME}"
DB_USER="${DB_USER}"
DB_PASS="${DB_PASS}"

# Backup Database
mysqldump -u"\${DB_USER}" -p"\${DB_PASS}" "\${DB_NAME}" \
    | gzip > "\${BACKUP_DIR}/db_\${DATE}.sql.gz"

# Backup Storage
tar -czf "\${BACKUP_DIR}/storage_\${DATE}.tar.gz" \
    -C "\${APP_DIR}" storage/app

# Backup .env
cp "\${APP_DIR}/.env" "\${BACKUP_DIR}/env_\${DATE}.bak"

# Cleanup backup lebih dari 30 hari
find "\${BACKUP_DIR}" -name "*.sql.gz" -mtime +30 -delete
find "\${BACKUP_DIR}" -name "*.tar.gz" -mtime +30 -delete
find "\${BACKUP_DIR}" -name "*.bak" -mtime +30 -delete

echo "[\$(date)] Backup selesai: \${DATE}" >> "\${BACKUP_DIR}/backup.log"
BACKUPEOF

    chmod +x "${BACKUP_SCRIPT}"
    safe_chown "${BACKUP_DIR}"

    # Tambahkan backup cron (jam 3 pagi)
    BACKUP_CRON="0 3 * * * ${BACKUP_SCRIPT} >> ${BACKUP_DIR}/backup.log 2>&1"
    if ! crontab -u "${APP_USER}" -l 2>/dev/null | grep -qF "backup.sh"; then
        (crontab -u "${APP_USER}" -l 2>/dev/null; echo "${BACKUP_CRON}") | crontab -u "${APP_USER}" -
    fi

    mark_step 12
fi

#-------------------------------------------------------------------------------
# Step 13: Verify Deployment
#-------------------------------------------------------------------------------
if ! is_step_done 13; then
    log_info "Step 13: Verifikasi deployment..."

    cd "${APP_DIR}"

    if php artisan --version &>/dev/null; then
        log_success "Laravel $(php artisan --version) OK"
    else
        log_warn "Artisan tidak berjalan. Cek PHP-FPM."
    fi

    if php artisan db:monitor 2>/dev/null; then
        log_success "Database connection OK"
    else
        log_warn "Database connection tidak dapat diverifikasi."
    fi

    if supervisorctl status formysha-worker:* 2>/dev/null | grep -q "RUNNING"; then
        log_success "Queue worker OK"
    else
        log_warn "Queue worker belum running. Cek: supervisorctl status"
    fi

    if [[ -L "${APP_DIR}/public/storage" ]]; then
        log_success "Storage link OK"
    fi

    mark_step 13
fi

#-------------------------------------------------------------------------------
# Cleanup & Summary
#-------------------------------------------------------------------------------
# Hapus step file setelah berhasil
rm -f "${STEP_FILE}"

echo ""
log_success "============================================"
log_success "  DEPLOYMENT FORMYSHA SELESAI!"
log_success "============================================"
log_success ""
log_success "  URL:      https://${DOMAIN}"
log_success "  App Dir:  ${APP_DIR}"
log_success "  Backup:   /www/backup/formysha"
log_success ""
log_success "  Selanjutnya:"
log_success "  1. Akses website untuk memastikan berjalan"
log_success "  2. Setup SSL melalui aaPanel jika belum"
log_success "  3. Test login dengan akun super admin"
log_success ""
log_success "  Useful Commands:"
log_success "  - Logs:      tail -f ${APP_DIR}/storage/logs/laravel.log"
log_success "  - Queue:     supervisorctl status"
log_success "  - Restart:   supervisorctl restart formysha-worker:*"
log_success "  - Cache:     cd ${APP_DIR} && php artisan cache:clear"
log_success ""
