if [ ! -f /etc/alpine-release ]; then
  echo 'This script is meant to be run in an ephemeral Alpine container.'
  exit 1
fi

if [ ! -f /app/artisan ]; then
  echo 'Expected a Laravel project on the /app folder'
  exit 1
fi

echo "
--- Installing required distro packages ---
"

apk add --no-cache \
  composer git \
  npm nodejs \
  php84 php84-fpm php84-mysqli php84-pdo_mysql \
  php84-mbstring php84-xml php84-curl php84-fileinfo \
  php84-bcmath php84-tokenizer php84-session php84-ctype \
  php84-dom php84-xmlwriter php84-simplexml php84-pcntl \
  php84-intl php84-pecl-imagick php84-gd;

echo "
--- Installing required PHP packages ---
"
composer install || exit 1

echo "
--- Installing required Javascript packages ---
"
npm install --ignore-scripts || exit 1

echo "
--- Building initial bundle ---
"
npm run build || exit 1

echo "
--- Generating development .env configuration ---
"

cp .env.example .env
php artisan key:generate --ansi

echo "
 +---------------------------------------------+
 | Done! Reverse this process with:            |
 |   rm -r node_modules/ public/build/ vendor/ |
 +---------------------------------------------+
"
