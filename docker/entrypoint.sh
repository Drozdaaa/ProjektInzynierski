#!/bin/bash
set -e

echo "Uruchamianie kontenera..."

# 1. Automatyczne tworzenie .env
if [ ! -f ".env" ]; then
    echo "Brak pliku .env - tworzę go z .env.example..."
    cp .env.example .env
    GENERATE_KEY=true
else
    echo "Plik .env już istnieje."
fi

# 2. Oczekiwanie na bazę danych
echo "Oczekiwanie na bazę danych..."
until php -r "try { new PDO('mysql:host=db;dbname=${DB_DATABASE}', '${DB_USERNAME}', '${DB_PASSWORD}'); } catch (PDOException \$e) { exit(1); }" > /dev/null 2>&1; do
  echo "   ...baza nie gotowa, czekam 2s..."
  sleep 2
done

# 3. Composer
if [ ! -f "vendor/autoload.php" ]; then
    echo "Instalowanie zależności Composer..."
    composer install --no-interaction --optimize-autoloader
fi

# 4. Generowanie klucza
if [ "$GENERATE_KEY" = true ]; then
    echo "Generowanie klucza aplikacji..."
    php artisan key:generate
    php artisan config:clear
fi

# 5. Uprawnienia
chown -R www-data:www-data storage bootstrap/cache

# 6. Migracje
echo "Migracje..."
php artisan migrate --force

# 7. Seedowanie
echo "Weryfikacja zawartości bazy danych..."

ROW_COUNT=$(php -r "try {
    \$pdo = new PDO('mysql:host=db;dbname=${DB_DATABASE}', '${DB_USERNAME}', '${DB_PASSWORD}');
    \$stmt = \$pdo->query('SELECT count(*) FROM users');
    echo \$stmt->fetchColumn();
} catch (PDOException \$e) {
    echo 0;
}")

if [ "$ROW_COUNT" -eq 0 ]; then
    echo "Wykryto pustą bazę danych. Uruchamiam seedowanie..."
    php artisan db:seed --force
else
    echo "Baza danych już zawiera dane. Pomijam seedowanie."
fi

# 8. Storage Link
echo "Tworzenie dowiązania storage..."
php artisan storage:link
mkdir -p storage/app/public/restaurants
mkdir -p storage/app/public/dishes
chown -R www-data:www-data storage
chmod -R 775 storage

echo "Start serwera Apache..."
exec docker-php-entrypoint apache2-foreground
