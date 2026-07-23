FROM alpine:latest

RUN apk add --no-cache \
      composer git \
      npm nodejs \
      php84 php84-fpm php84-mysqli php84-pdo_mysql \
      php84-mbstring php84-xml php84-curl php84-fileinfo \
      php84-bcmath php84-tokenizer php84-session php84-ctype \
      php84-dom php84-xmlwriter php84-simplexml php84-pcntl \
      php84-intl php84-pecl-imagick php84-gd;

RUN echo "extension=imagick" > /etc/php84/conf.d/imagick.ini

# PHP settings that are nice to have
RUN cat >> /etc/php84/php.ini <<EOF
error_reporting = E_ALL
display_errors = On
display_startup_errors = On
# Avoid local timezones or other weirdness
date.timezone = UTC
# Resource limit
upload_max_filesize = 100M
post_max_size = 100M
memory_limit = 2G
EOF

# ENV PATH="/root/.composer/vendor/bin:${PATH}"

# The application may come from a volume
RUN mkdir -p /app/storage /app/bootstrap/cache && chmod -R 775 /app
VOLUME /app

# HTTP, Vite
EXPOSE 8000 5173

# For remote monitoring
HEALTHCHECK --interval=5m --timeout=3s --start-period=30s --retries=5 \
  CMD curl --head --fail-with-body http://localhost:8000

WORKDIR /app
ARG IN_PRODUCTION
CMD ["/bin/sh", "-c", "if [ \"$IN_PRODUCTION\" = 'yes' ]; then composer run dev; else php artisan migrate:fresh --force --seed && composer run dev; fi"]
