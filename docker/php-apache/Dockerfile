FROM php:8.2.10-apache AS base
# フロントエンド・ビルド・ツールViteで使うポートを開放
EXPOSE 5173

# ワーキングディレクトリの設定
WORKDIR /var/www/html

# libonig-dev: dockerでphp:7.4以降はlibonig-devというパッケージをする必要がある。
# docker-php-ext-install: PHPで必要な標準ライブラリが入っている。
RUN apt-get update && apt-get install -y \
  libonig-dev \
  && docker-php-ext-install pdo_mysql mysqli \
  && apt-get install -y zlib1g-dev \
  && apt-get install -y zip unzip \
  && apt-get -y install libzip-dev \
  # LoadModule rewrite_moduleを有効化するのに必要
  && a2enmod rewrite

# composerのインストール
RUN curl -sS https://getcomposer.org/installer | php \
  && mv composer.phar /usr/local/bin/composer \
  && chmod +x /usr/local/bin/composer

# composerコマンドをrootで実行することを許可
ENV COMPOSER_ALLOW_SUPERUSER 1
ENV COMPOSER_HOME /composer
ENV PATH $PATH:/composer/vendor/bin
# タイムゾーン設定
ENV TZ=Asia/Tokyo

# 手動じゃないと無理かも 25行目でroot権限実行許可を設定しているので、エラーとならない
#RUN composer update
# Laravelデバッグバーのインストール
#RUN composer require barryvdh/laravel-debugbar:^3.7

# ローカルで修正したphp.iniをコンテナ内に配置。コンテナ内にはデフォルトでphp.iniは無い。
COPY ./config/php/php.ini /usr/local/etc/php/
# ローカルで修正したlocalhost設定をコンテナ内に配置。
COPY ./config/vhost.conf /etc/apache2/sites-available/

# デフォルトのlocalhost設定を削除。Debian系のApache/2.4.57 (Debian)はドメインを１つしか設定できない為。
#RUN a2dissite 000-default.conf
# 新しいlocalhost設定を割り当てる
RUN a2ensite vhost.conf

# フロントエンド・ビルド・ツールViteで使う設定ここから
# nodebrewを入れる前に下記をインストールする
# RUN apt-get install -y git-all
# 余計な推奨パッケージを入れない & レイヤーを減らす
# RUN set -eux; \
#     apt-get update; \
#     apt-get install -y --no-install-recommends \
#       git \
#       build-essential \
#     ; \
#     rm -rf /var/lib/apt/lists/*
# RUN set -eux; \
#     apt-get update; \
#     apt-get install -y --no-install-recommends \
#       git \
#       build-essential \
#     ; \
#     rm -rf /var/lib/apt/lists/*
RUN set -eux; \
    apt-get update; \
    apt-get install -y --no-install-recommends \
      git \
      build-essential \
      libpng-dev libjpeg62-turbo-dev libfreetype6-dev libzip-dev; \
      docker-php-ext-configure gd --with-freetype --with-jpeg; \
      docker-php-ext-install -j$(nproc) gd zip; \
    rm -rf /var/lib/apt/lists/*

# Mailhogのためのmhsendmailインストール
RUN curl -sSL https://github.com/mailhog/mhsendmail/releases/download/v0.2.0/mhsendmail_linux_amd64 -o mhsendmail && \
  chmod +x mhsendmail && \
  mv mhsendmail /usr/local/bin/mhsendmail

# ここからは、手動じゃないと無理かも
# RUN /bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"
# RUN  (echo; echo 'eval "$(/home/linuxbrew/.linuxbrew/bin/brew shellenv)"') >> /root/.bashrc eval "$(/home/linuxbrew/.linuxbrew/bin/brew shellenv)"
# eval "$(/home/linuxbrew/.linuxbrew/bin/brew shellenv)"
# RUN brew install nodebrew
# mkdir -p ~/.nodebrew/src
# RUN nodebrew install v16.12.0
# RUN nodebrew use v16.12.0
# RUN cd /var/www/html/uCRM \
#   && export PATH=$HOME/.nodebrew/current/bin:$PATH


# phpoffice/phpspreadsheetをインストールために、gdとzipをインストールする必要がある。コマンド内手動
# gdのインストール
# root@123456789abc:/var/www/html# apt-get install -y zlib1g-dev libpng-dev libjpeg62-turbo-dev
# root@123456789abc:/var/www/html# docker-php-ext-configure gd --with-jpeg
# root@123456789abc:/var/www/html# docker-php-ext-install -j$(nproc) gd
# zipのインストール
# apt-get -y install libzip-dev
# docker-php-ext-install zip

#/phpspreadsheetをインストール
# # composer require phpoffice/phpspreadsheet
