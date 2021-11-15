FROM php:8-apache

COPY ./apache2.conf /etc/apache2/apache2.conf

ENV APACHE_DOCUMENT_ROOT /var/www/html/public

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

RUN apt-get update; \
    apt-get install -y \
	libfreetype6-dev libjpeg62-turbo-dev zlib1g-dev \
	libpng-dev; \
    docker-php-ext-install \
        gd;
