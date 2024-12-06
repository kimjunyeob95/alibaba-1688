# 기본이미지
FROM php:8.0-fpm

ARG PROFILE

#dev/prod
ENV ENVIRONMENT=${PROFILE}

ENV LANG=ko_KR.UTF-8
ENV LANGUAGE=ko_KR:ko
ENV LC_ALL=ko_KR.UTF-8

# Install dependencies
RUN apt-get update && apt-get install -y \
    wget \
    build-essential \
    git \
    libssl-dev \
    zlib1g-dev \
    libncurses5-dev \
    libncursesw5-dev \
    libreadline-dev \
    libsqlite3-dev \
    libgdbm-dev \
    libdb5.3-dev \
    libbz2-dev \
    libexpat1-dev \
    liblzma-dev \
    tk-dev \
    libffi-dev \
    curl \
    unzip \
    locales \
    libzip-dev \
    vim \
    awscli \
    nginx \
    net-tools \
    procps \
    supervisor \
    exiftool \
    libmagickwand-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    fonts-nanum

# 로케일 생성 및 업데이트
RUN echo "ko_KR.UTF-8 UTF-8" > /etc/locale.gen && \
    locale-gen ko_KR.UTF-8 && \
    update-locale LANG=ko_KR.UTF-8 LC_ALL=ko_KR.UTF-8

# Python 3.7 설치
RUN wget https://www.python.org/ftp/python/3.7.12/Python-3.7.12.tgz && \
    tar xzf Python-3.7.12.tgz && \
    cd Python-3.7.12 && \
    ./configure --enable-optimizations && \
    make altinstall && \
    ln -sf /usr/local/bin/python3.7 /usr/bin/python3.7 && \
    ln -sf /usr/local/bin/pip3.7 /usr/bin/pip3.7 && \
    rm -rf /Python-3.7.12* && \
    apt-get clean

# Python 3.7을 기본 python 명령어로 설정
RUN ln -sf /usr/local/bin/python3.7 /usr/bin/python && \
    ln -sf /usr/local/bin/pip3.7 /usr/bin/pip

# 필요한 Python 패키지 설치
RUN pip install --upgrade pip && \
    pip install PyMySQL requests urllib3 certifi charset-normalizer flask_socketio flask_cors eventlet gunicorn stegano

# confluent-kafka 패키지 별도 설치
RUN apt-get update && apt-get install -y librdkafka-dev && \
    pip install confluent-kafka && \
    pecl install rdkafka && \
    docker-php-ext-enable rdkafka

# bashrc에 alias 추가
RUN echo 'alias python=python3.7' >> ~/.bashrc && \
    echo 'alias pip=pip3.7' >> ~/.bashrc

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install extensions
RUN docker-php-ext-install pdo pdo_mysql mysqli zip exif pcntl opcache sockets

# Install imagick extension via pecl
RUN pecl install imagick && docker-php-ext-enable imagick

# Enable imagick extension
RUN mkdir -p /usr/local/etc/php/conf.d && \
    echo "extension=imagick.so" > /usr/local/etc/php/conf.d/docker-php-ext-imagick.ini

# Install gd extension
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd

# Enable gd extension
RUN echo "extension=gd.so" > /usr/local/etc/php/conf.d/docker-php-ext-gd.ini

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Add user for laravel application
RUN groupadd -g 1000 www && useradd -u 1000 -ms /bin/bash -g www www
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV TZ=Asia/Seoul

# Node
RUN curl -sL https://deb.nodesource.com/setup_lts.x | bash - && \
    apt-get install -y nodejs

RUN composer global require laravel/envoy && composer require guzzlehttp/guzzle

# Copy everything into the work directory
COPY . /var/www/html/

# Set work directory
WORKDIR /var/www/html

RUN chown -R www.www /var/www/html
RUN chmod -R 777 /var/www/html/storage

RUN composer install

# 설정 파일 덮어쓰기
# nginx
COPY ./docker/configs/nginx/app.conf /etc/nginx/sites-enabled/default
# php
COPY ./docker/configs/php/php.ini /usr/local/etc/php/php.ini
# php-fpm
COPY ./docker/configs/php-fpm/app.conf /usr/local/etc/php-fpm.d/app.conf

# Env
COPY .env.example /var/www/html/.env

#APP_URL 운영으로 변경하기
RUN if [ "$ENVIRONMENT" = "prod" ]; then \
        sed -i "s|APP_ENV=.*|APP_ENV=production|" /var/www/html/.env; \
        sed -i "s|APP_DEBUG=.*|APP_DEBUG=false|" /var/www/html/.env; \
        sed -i "s|APP_URL=.*|APP_URL=https://production.url|" /var/www/html/.env; \
    fi

RUN php artisan key:generate

# COPY ./docker/configs/supervisor/laravel-scheduler.conf /etc/supervisor/conf.d/laravel-scheduler.conf
# COPY ./docker/configs/supervisor/laravel-worker.conf /etc/supervisor/conf.d/laravel-worker.conf
COPY ./docker/configs/supervisor/supervisord.conf /etc/supervisor/supervisord.conf

#  entry point sh 권한 부여
RUN ["chmod", "+x", "/var/www/html/docker/entrypoint.sh"]

# nginx가 사용하는 포트를 노출시킨다.
EXPOSE 80

# ENTRYPOINT ["/var/www/html/docker/entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisor/supervisord.conf"]
