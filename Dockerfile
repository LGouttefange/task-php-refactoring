FROM php:8.2-alpine3.20 AS base

COPY --from=composer:2.6.5 /usr/bin/composer /usr/bin/composer

WORKDIR /srv/app
CMD ["sh"]
