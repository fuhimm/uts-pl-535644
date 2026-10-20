FROM dunglas/frankenphp:1.4-php8.3-alpine

WORKDIR /app

# Install paket sistem dan ekstensi PHP yang dibutuhkan Laravel
RUN apk add --no-cache git unzip zip \
    && install-php-extensions pdo_mysql pdo_pgsql bcmath zip opcache pcntl

# Ambil binary Composer dengan versi spesifik
COPY --from=composer:2.8 /usr/bin/composer /usr/bin/composer

# 1. Salin composer.json & composer.lock terlebih dahulu agar Docker layer caching bekerja
COPY composer.json composer.lock ./

# 2. Install dependensi PHP tanpa menyalin kode aplikasi terlebih dahulu
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --no-scripts

# 3. Salin seluruh kode aplikasi Laravel
COPY . .

# 4. Optimasi autoloader dan atur izin direktori penyimpanan Laravel
RUN composer dump-autoload --optimize --no-dev \
    && mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 8000

HEALTHCHECK --interval=10s --timeout=5s --retries=5 \
    CMD curl -fsS http://localhost:8000/up || exit 1

# Jalankan Laravel menggunakan FrankenPHP sebagai application server (Nilai Tambahan Soal 7)
CMD ["frankenphp", "php-server", "--listen", ":8000", "--root", "/app/public", "--access-log"]
