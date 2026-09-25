FROM php:8.3-cli
RUN docker-php-ext-install pdo_sqlite
WORKDIR /app
COPY . /app
RUN mkdir -p /app/data /app/uploads && chmod -R 777 /app/data /app/uploads
EXPOSE 10000
CMD ["sh","-c","php -S 0.0.0.0:${PORT:-10000} router.php"]
