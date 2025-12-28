FROM php:8.3-fpm

# システムの依存関係をインストール
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libicu-dev \
    libzip-dev \
    libsodium-dev \
    zip \
    unzip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# PHP拡張をインストール
RUN docker-php-ext-install \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    intl \
    opcache \
    zip \
    sodium

# Redisエクステンションをインストール
RUN pecl install redis && docker-php-ext-enable redis

# Composerをインストール
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 作業ディレクトリを設定
WORKDIR /var/www

# ユーザーを作成
RUN groupadd -g 1000 www
RUN useradd -u 1000 -ms /bin/bash -g www www

# 権限を設定
COPY --chown=www:www . /var/www

USER www

EXPOSE 9000
CMD ["php-fpm"]