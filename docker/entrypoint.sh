#!/usr/bin/env bash
set -e

php-fpm -D -R
nginx -g 'daemon off;'