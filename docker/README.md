### nginx 설정
> /etc/nginx/conf.d/**.conf

### php 설정
> /usr/local/etc/php/php.ini

### fpm 설정
> /usr/local/etc/php-fpm.d/**.conf


### Dockerfile 빌드 및 컨테이터 생성 명령어
```
docker build --no-cache -t 이미지명 ./
docker run -tid --name 생성할컨테이너명 -p 접속포트:80 이미지명
```
