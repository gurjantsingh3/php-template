# Use the official PHP image with Apache
FROM php:8.1-apache

WORKDIR /app

COPY . .
EXPOSE 5200



CMD [ "php", "-S", "0.0.0.0:5200" ]