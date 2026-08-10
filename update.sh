#!/bin/bash
#===============================================================================
# ForMysha — Daily Update & Bug Fix Script
# Target: VPS aaPanel Ubuntu — Production Environment
#===============================================================================

set -euo pipefail

#-------------------------------------------------------------------------------
# Configuration
#-------------------------------------------------------------------------------
DOMAIN="formysha.my.id"
APP_DIR="/www/wwwroot/${DOMAIN}"
APP_USER="www"
PHP_VERSION="8.4"
BRANCH="main"
LOG_DIR="${APP_DIR}/storage/logs"

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

timestamp() { date '+%Y-%m-%d %H:%M:%S'; }

backup_before_update() {
    local backup_dir="/www/backup/formysha/pre-update"
    mkdir -p "${backup_dir}"
    local date=$(date +%Y%m%d_%H%M%S)

    log_info "Backup .env sebelum update..."
    cp "${APP_DIR}/.env" "${backup_dir}/env_${date}.bak"

    log_info "Snapshot git commit saat ini..."
    cd "${APP_DIR}"
    git rev-parse HEAD > "${backup_dir}/commit_${date}.txt"

    log_success "Backup tersimpan di ${backup_dir}"
}

enable_maintenance() {
    cd "${APP_DIR}"
    php artisan down --render="errors::503" --retry=60 --secret="formysha_update_$(date +%s)"
    log_info "Maintenance mode AKTIF."
}

disable_maintenance() {
    cd "${APP_DIR}"
    php artisan up
    log_info "Maintenance mode NONAKTIF."
}

clear_all_caches() {
    cd "${APP_DIR}"

    log_info "Membersihkan semua cache..."
    php artisan config:clear
    php artisan route:clear
    php artisan view:clear
    php artisan event:clear
    php artisan cache:clear

    log_info "Rebuilding cache..."
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    php artisan event:cache

    log_success "Cache berhasil dibersihkan dan di-rebuild."
}

restart_queue_workers() {
    supervisorctl stop "formysha:*" 2>/dev/null || true
    sleep 2
    supervisorctl start "formysha:*" 2>/dev/null || true
    log_success "Queue worker direstart."
}

#-------------------------------------------------------------------------------
# Parse Arguments
#-------------------------------------------------------------------------------
MODE="daily"   # daily | bugfix | full | rollback
SKIP_TESTS=false
FORCE=false

while [[ $# -gt 0 ]]; do
    case $1 in
        --mode)
            MODE="$2"
            shift 2
            ;;
        --skip-tests)
            SKIP_TESTS=true
            shift
            ;;
        --force)
            FORCE=true
            shift
            ;;
        --rollback)
            MODE="rollback"
            shift
            ;;
        --help|-h)
            echo "ForMysha Update Script"
            echo ""
            echo "Usage: bash update.sh [OPTIONS]"
            echo ""
            echo "Options:"
            echo "  --mode daily       Update harian dari git (default)"
            echo "  --mode bugfix      Update dengan cherry-pick fix tertentu"
            echo "  --mode full        Full update: git + composer + npm + migrate + cache"
            echo "  --rollback         Rollback ke commit sebelumnya"
            echo "  --skip-tests       Skip test sebelum deploy"
            echo "  --force            Skip konfirmasi"
            echo "  --help             Tampilkan bantuan"
            echo ""
            echo "Examples:"
            echo "  bash update.sh                        # Update harian"
            echo "  bash update.sh --mode full            # Full update"
            echo "  bash update.sh --mode bugfix          # Bug fix update"
            echo "  bash update.sh --rollback             # Rollback"
            echo "  bash update.sh --mode daily --skip-tests  # Update tanpa test"
            exit 0
            ;;
        *)
            log_error "Unknown option: $1. Gunakan --help untuk bantuan."
            ;;
    esac
done

#-------------------------------------------------------------------------------
# Header
#-------------------------------------------------------------------------------
echo ""
echo -e "${BLUE}╔══════════════════════════════════════════════════╗${NC}"
echo -e "${BLUE}║       ForMysha Update Script — Production        ║${NC}"
echo -e "${BLUE}╚══════════════════════════════════════════════════╝${NC}"
echo ""
log_info "Mode: ${MODE}"
log_info "Timestamp: $(timestamp)"
log_info "App Directory: ${APP_DIR}"
echo ""

# Cek root
if [[ $EUID -ne 0 ]]; then
    log_error "Script ini harus dijalankan sebagai root."
fi

# Cek app directory
if [[ ! -d "${APP_DIR}/.git" ]]; then
    log_error "Repository tidak ditemukan di ${APP_DIR}. Jalankan deploy.sh terlebih dahulu."
fi

cd "${APP_DIR}"

# Composer root safety
export COMPOSER_ALLOW_SUPERUSER=1

#-------------------------------------------------------------------------------
# MODE: ROLLBACK
#-------------------------------------------------------------------------------
if [[ "${MODE}" == "rollback" ]]; then
    log_info "=== ROLLBACK MODE ==="
    echo ""

    # Cari backup terakhir
    BACKUP_DIR="/www/backup/formysha/pre-update"
    if [[ ! -d "${BACKUP_DIR}" ]]; then
        log_error "Backup directory tidak ditemukan: ${BACKUP_DIR}"
    fi

    LATEST_COMMIT=$(ls -t "${BACKUP_DIR}"/commit_*.txt 2>/dev/null | head -1)
    if [[ -z "${LATEST_COMMIT}" ]]; then
        log_error "Tidak ada commit backup yang ditemukan."
    fi

    PREV_COMMIT=$(cat "${LATEST_COMMIT}")
    log_info "Commit sebelumnya: ${PREV_COMMIT}"

    echo -en "${YELLOW}Rollback ke commit ${PREV_COMMIT}? [y/N]: ${NC}"
    if [[ "${FORCE}" != "true" ]]; then
        read -r reply
        [[ "$reply" =~ ^[Yy]$ ]] || exit 0
    fi

    enable_maintenance

    # Restore .env
    LATEST_ENV=$(ls -t "${BACKUP_DIR}"/env_*.bak 2>/dev/null | head -1)
    if [[ -n "${LATEST_ENV}" ]]; then
        cp "${LATEST_ENV}" "${APP_DIR}/.env"
        log_success ".env restored dari backup."
    fi

    # Git rollback
    git checkout "${PREV_COMMIT}"
    git checkout "${BRANCH}" -- .
    git reset --hard "${PREV_COMMIT}"

    # Restore dependencies
    composer install --no-dev --optimize-autoloader --no-interaction
    npm ci --ignore-scripts 2>/dev/null || true
    npm run build 2>/dev/null || true

    # Restore caches
    clear_all_caches

    # Restart services
    restart_queue_workers

    disable_maintenance

    log_success "Rollback selesai ke commit: ${PREV_COMMIT}"
    exit 0
fi

#-------------------------------------------------------------------------------
# MODE: DAILY UPDATE
#-------------------------------------------------------------------------------
if [[ "${MODE}" == "daily" ]]; then
    log_info "=== DAILY UPDATE MODE ==="
    echo ""

    # Backup sebelum update
    backup_before_update

    # Fetch latest
    log_info "Fetching latest changes..."
    git fetch origin "${BRANCH}"

    # Cek apakah ada update
    LOCAL=$(git rev-parse HEAD)
    REMOTE=$(git rev-parse "origin/${BRANCH}")

    if [[ "${LOCAL}" == "${REMOTE}" ]]; then
        log_success "Sudah versi terbaru. Tidak ada yang perlu di-update."
        exit 0
    fi

    log_info "Local:  ${LOCAL}"
    log_info "Remote: ${REMOTE}"
    echo ""

    # Tampilkan commit yang akan di-pull
    log_info "Commits yang akan di-pull:"
    git log --oneline "${LOCAL}..${REMOTE}" | head -10
    echo ""

    if [[ "${FORCE}" != "true" ]]; then
        echo -en "${YELLOW}Lanjutkan update? [y/N]: ${NC}"
        read -r reply
        [[ "$reply" =~ ^[Yy]$ ]] || exit 0
    fi

    # Enable maintenance mode
    enable_maintenance

    # Pull changes
    log_info "Pulling changes..."
    git stash 2>/dev/null || true
    git pull origin "${BRANCH}" --ff-only

    # Install dependencies jika ada perubahan
    if git diff "${LOCAL}" "${REMOTE}" --name-only | grep -q "composer.json"; then
        log_info "composer.json berubah. Menjalankan composer install..."
        composer install --no-dev --optimize-autoloader --no-interaction
    fi

    if git diff "${LOCAL}" "${REMOTE}" --name-only | grep -q "package.json"; then
        log_info "package.json berubah. Menjalankan npm install..."
        npm ci --ignore-scripts
        npm run build
    fi

    # Selalu rebuild frontend (untuk memastikan asset terbaru)
    log_info "Building frontend assets..."
    npm run build 2>/dev/null || log_warn "Frontend build gagal. Cek log."

    # Jalankan migrations jika ada perubahan database
    if git diff "${LOCAL}" "${REMOTE}" --name-only | grep -q "database/migrations"; then
        log_info "Migration baru terdeteksi. Menjalankan migrate..."
        php artisan migrate --force
    fi

    # Clear dan rebuild cache
    clear_all_caches

    # Restart queue workers
    restart_queue_workers

    # Disable maintenance mode
    disable_maintenance

    log_success "Daily update selesai."
    exit 0
fi

#-------------------------------------------------------------------------------
# MODE: BUGFIX UPDATE
#-------------------------------------------------------------------------------
if [[ "${MODE}" == "bugfix" ]]; then
    log_info "=== BUGFIX MODE ==="
    echo ""

    backup_before_update

    echo -en "${YELLOW}Masukkan commit hash untuk cherry-pick (atau 'pull' untuk pull terbaru): ${NC}"
    read -r fix_ref

    enable_maintenance

    if [[ "${fix_ref}" == "pull" ]]; then
        # Sama seperti daily update
        git stash 2>/dev/null || true
        git pull origin "${BRANCH}" --ff-only
    else
        # Cherry-pick commit tertentu
        log_info "Cherry-pick commit: ${fix_ref}"
        git fetch origin
        git cherry-pick "${fix_ref}" --no-edit || {
            log_warn "Cherry-pick conflict. Menampilkan status..."
            git status
            echo ""
            echo -en "${YELLOW}Resolve conflict lalu tekan Enter, atau ketik 'abort' untuk membatalkan: ${NC}"
            read -r conflict_reply
            if [[ "${conflict_reply}" == "abort" ]]; then
                git cherry-pick --abort
                disable_maintenance
                log_error "Bugfix dibatalkan."
            fi
            git add .
            git cherry-pick --continue --no-edit
        }
    fi

    # Build & optimize
    npm run build 2>/dev/null || true
    clear_all_caches
    restart_queue_workers

    disable_maintenance

    log_success "Bugfix berhasil di-deploy."
    exit 0
fi

#-------------------------------------------------------------------------------
# MODE: FULL UPDATE
#-------------------------------------------------------------------------------
if [[ "${MODE}" == "full" ]]; then
    log_info "=== FULL UPDATE MODE ==="
    echo ""

    backup_before_update

    echo -en "${YELLOW}Full update akan menjalankan: git pull + composer + npm + migrate + cache. Lanjutkan? [y/N]: ${NC}"
    if [[ "${FORCE}" != "true" ]]; then
        read -r reply
        [[ "$reply" =~ ^[Yy]$ ]] || exit 0
    fi

    enable_maintenance

    # Git pull
    log_info "Pulling latest changes..."
    git stash 2>/dev/null || true
    git pull origin "${BRANCH}" --ff-only || {
        log_warn "Fast-forward gagal. Mencoba merge..."
        git merge "origin/${BRANCH}" --no-edit || {
            git merge --abort
            disable_maintenance
            log_error "Merge conflict! Resolve manual atau gunakan --mode bugfix."
        }
    }

    # Composer install
    log_info "Installing PHP dependencies..."
    composer install --no-dev --optimize-autoloader --no-interaction

    # NPM install & build
    log_info "Installing Node dependencies..."
    npm ci --ignore-scripts
    npm run build

    # Migrations
    log_info "Running migrations..."
    php artisan migrate --force

    # Seed (opsional)
    echo -en "${YELLOW}Jalankan seeder? [y/N]: ${NC}"
    read -r seed_reply
    if [[ "$seed_reply" =~ ^[Yy]$ ]]; then
        php artisan db:seed --force
    fi

    # Permissions (skip .user.ini yang dikunci aaPanel)
    find "${APP_DIR}/storage" -not -name ".user.ini" -exec chown -R "${APP_USER}:${APP_USER}" {} + 2>/dev/null || true
    find "${APP_DIR}/bootstrap/cache" -not -name ".user.ini" -exec chown -R "${APP_USER}:${APP_USER}" {} + 2>/dev/null || true

    # Cache
    clear_all_caches

    # Restart queue
    restart_queue_workers

    # Storage link
    php artisan storage:link --force 2>/dev/null || true
    find "${APP_DIR}/public/storage" -not -name ".user.ini" -exec chown -R "${APP_USER}:${APP_USER}" {} + 2>/dev/null || true

    # Disable maintenance
    disable_maintenance

    log_success "Full update selesai."
    exit 0
fi

#-------------------------------------------------------------------------------
# Default: Tidak ada mode yang cocok
#-------------------------------------------------------------------------------
log_error "Mode tidak dikenali: ${MODE}. Gunakan --help untuk bantuan."
