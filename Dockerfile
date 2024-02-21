# 기본이미지
FROM php:8.0-fpm

ARG PROFILE

#dev/prod
ENV ENVIRONMENT=${PROFILE}

# Install dependencies
# git 확인 필요
# locales 타임존 확인
RUN apt-get update && apt-get install -y \
    git \
    curl \
    unzip \
    libmcrypt4 \
    locales \
    libzip-dev \
    vim \
    awscli  # Install the AWS CLI

RUN apt-get install -y nginx net-tools && apt-get install -y procps

# Python
RUN apt-get install -y python3 python3-pip
RUN apt-get install -y python3-confluent-kafka
RUN echo 'alias python=python3' >> ~/.bashrc
RUN pip3 install PyMySQL requests urllib3 certifi charset-normalizer flask_socketio flask_cors eventlet gunicorn

RUN apt-get install -y supervisor

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install extensions
RUN docker-php-ext-install pdo pdo_mysql mysqli zip exif pcntl opcache sockets

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Add user for laravel application
RUN groupadd -g 1000 www && useradd -u 1000 -ms /bin/bash -g www www
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV TZ=Asia/Seoul

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

COPY ./docker/configs/supervisor/laravel-scheduler.conf /etc/supervisor/conf.d/laravel-scheduler.conf
COPY ./docker/configs/supervisor/laravel-worker.conf /etc/supervisor/conf.d/laravel-worker.conf
COPY ./docker/configs/supervisor/supervisord.conf /etc/supervisor/supervisord.conf

#  entry point sh 권한 부여
RUN ["chmod", "+x", "/var/www/html/docker/entrypoint.sh"]

# nginx가 사용하는 포트를 노출시킨다.
EXPOSE 80

# ENTRYPOINT ["/var/www/html/docker/entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisor/supervisord.conf"]
