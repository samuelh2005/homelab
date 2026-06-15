FROM php:8.5.7-fpm

ARG INSTALLER_URL=https://github.com/Azuriom/AzuriomInstaller/releases/latest/download/AzuriomInstaller.zip

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        ca-certificates \
        curl \
        libzip-dev \
        unzip \
    && docker-php-ext-install -j"$(nproc)" bcmath zip pdo_mysql \
    && rm -rf /var/lib/apt/lists/*

RUN mkdir -p /usr/src/azuriom-template \
    && curl -fsSL "$INSTALLER_URL" -o /tmp/AzuriomInstaller.zip \
    && unzip -q /tmp/AzuriomInstaller.zip -d /usr/src/azuriom-template \
    && rm -f /tmp/AzuriomInstaller.zip

COPY php/entrypoint.sh /usr/local/bin/azuriom-entrypoint
RUN chmod +x /usr/local/bin/azuriom-entrypoint

WORKDIR /var/www/html
ENTRYPOINT ["azuriom-entrypoint"]
CMD ["php-fpm"]
