# Deploy Kasiva POS — kasiva.biz.id

Ringkasan panduan deploy produksi untuk domain **kasiva.biz.id** (opsi VPS murah + gratis).

## 1) Ringkasan Domain & DNS

- **Domain pilihan**: `kasiva.biz.id` (efisien biaya vs `.id`).
- **Registrar**: DomaiNesia/Rumahweb/JagoanHosting — beli `kasiva.biz.id` dan arahkan A record ke IP VPS.
- **DNS yang dibutuhkan**:
  - `A kasiva.biz.id -> <IP VPS>`
  - `A www.kasiva.biz.id -> <IP VPS>` (atau CNAME ke `kasiva.biz.id`)
  - `CAA` (opsional) untuk Let's Encrypt.

## 2) Opsi VPS Murah (IDR) — Rekomendasi untuk Kasiva POS

| Provider | Paket Murah | Spek | Harga/bln | Kelebihan |
|---|---|---|---|---|
| **IDCloudHost** | Lite | 1 vCPU, 1GB RAM, 20GB SSD | ~Rp 60k | Lokal, support bagus |
| **Biznet Gio** | NEO Lite | 1 vCPU, 1GB RAM, 10GB | ~Rp 50k | Jaringan stabil |
| **DigitalOcean** | Basic | 1 vCPU, 1GB, 25GB | ~$6 | Global, dok luas |
| **Hetzner** | CX11 | 1 vCPU, 2GB, 20GB | ~€3.5 | Paling murah Eropa |
| **Oracle Free Tier** | Always Free | 4 ARM OCPU, 24GB RAM | Gratis | Paling murah (butuh kartu kredit) |

> **Rekomendasi**: IDCloudHost/Biznet Gio untuk gen Z lokal; Hetzner/DigitalOcean jika target global; Oracle Free untuk eksperimen gratis.

## 3) Stack Produksi (Laravel 12 + Livewire 4)

- PHP 8.4 (wajib >=8.4.1), Composer 2.8
- Node 20 + npm, Vite build
- SQLite (MVP) atau MySQL 8
- Nginx, PHP-FPM, Let's Encrypt (certbot), Redis (opsional), Supervisor untuk queue (jika ada), fail2ban.

## 4) Langkah Deploy — Bare Metal / VPS Ubuntu 22.04

```bash
# 1. Server prep
sudo apt update && sudo apt install -y nginx php8.4-fpm php8.4-mbstring php8.4-xml php8.4-sqlite3 php8.4-curl php8.4-zip composer nodejs npm git certbot python3-certbot-nginx
php -v

# 2. Clone & install
git clone https://github.com/riod94/kasiva.git /var/www/kasiva
cd /var/www/kasiva
composer install --no-dev --optimize-autoloader
npm ci && npm run build

# 3. Env
cp .env.example .env
# Isi:
# APP_NAME="Kasiva POS"
# APP_ENV=production
# APP_DEBUG=false
# APP_URL=https://kasiva.biz.id
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=kasiva
DB_USERNAME=kasiva
DB_PASSWORD=change-me
# SESSION_DRIVER=file
# QUEUE_CONNECTION=database

php artisan key:generate
php artisan migrate --force
php artisan db:seed --class=KasivaProductionSeeder --force
php artisan optimize:clear && php artisan config:cache && php artisan route:cache && php artisan view:cache

# 4. Permissions
sudo chown -R www-data:www-data /var/www/kasiva
sudo chmod -R 775 storage bootstrap/cache

# 5. Nginx
sudo nano /etc/nginx/sites-available/kasiva
# (lihat file referensi docs/nginx-kasiva.conf)

sudo ln -s /etc/nginx/sites-available/kasiva /etc/nginx/sites-enabled/kasiva
sudo nginx -t && sudo systemctl reload nginx

# 6. SSL
sudo certbot --nginx -d kasiva.biz.id -d www.kasiva.biz.id --redirect

# 7. Verify
curl -I https://kasiva.biz.id
```

## 5) Nginx Conf Referensi

Lihat `docs/nginx-kasiva.conf`.

## 6) Verifikasi Pasca-Deploy

```bash
php artisan test --compact    # 41 tests passed
npm run build                 # Vite OK
php artisan route:list | head
```

Login production:

- `owner@kasiva.pos` / `password123` (Owner)
- `kasir@kasiva.pos` / `password123` (Kasir — hanya POS & History)

## 7) Backup & Maintenance

- Backup PostgreSQL terjadwal (`pg_dump`) + `storage/app` + `.env`
- Contoh: `pg_dump --format=custom --file=/backups/kasiva-$(date +\%F).dump kasiva`
- Keep 7 hari terakhir.

## 8) Alternatif Zero-Cost

- **Railway / Render / Fly.io free tier** — cocok untuk staging/demo, bukan untuk data produksi kritikal.
- **Cloudflare Tunnel** — expose localhost tanpa buka port (gratis, untuk demo).
```

cat > "/Users/riodprabowo/Projects/riod94/kasiva/docs/nginx-kasiva.conf" << 'NGINX'
server {
    listen 80;
    server_name kasiva.biz.id www.kasiva.biz.id;
    root /var/www/kasiva/public;
    index index.php index.html;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.4-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    client_max_body_size 20M;
    gzip on;
    gzip_types text/plain text/css application/json application/javascript text/xml;
}
NGINX

cat > "/Users/riodprabowo/Projects/riod94/kasiva/.env.kasiva.biz.id.example" << 'ENV'
APP_NAME="Kasiva POS"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://kasiva.biz.id
APP_KEY=base64:GENERATE_VIA_php_artisan_key_generate

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=kasiva
DB_USERNAME=kasiva
DB_PASSWORD=change-me
DB_URL=

SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=sync

MAIL_MAILER=smtp
MAIL_HOST=smtp.mailgun.org
MAIL_PORT=587

LOG_CHANNEL=stack
LOG_LEVEL=warning
ENV

echo "docs written: $?"
ls -lh /Users/riodprabowo/Projects/riod94/kasiva/docs/ 2>&1 | cat
