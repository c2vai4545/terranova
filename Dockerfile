FROM php:8.2-apache

RUN a2enmod rewrite && docker-php-ext-install pdo pdo_mysql

# Ajustar DocumentRoot a /public
RUN sed -i 's#DocumentRoot /var/www/html#DocumentRoot /var/www/html/public#' /etc/apache2/sites-available/000-default.conf

# Permitir .htaccess en /public
RUN sed -i 's#<Directory /var/www/>#<Directory /var/www/>\n\tAllowOverride All#' /etc/apache2/apache2.conf

# Propagar PORT en tiempo de ejecución para health check de Koyeb
ENV PORT=8000

WORKDIR /var/www/html
COPY . .

# Entrypoint: genera config.local.php desde variables de entorno y arranca Apache
COPY deploy/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

EXPOSE 80
CMD ["/entrypoint.sh"]


