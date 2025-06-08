# Usar la imagen base de PHP con Apache
FROM php:8.3-apache

# Instalar dependencias del sistema y extensiones PHP necesarias para LDAP
RUN apt-get update && apt-get install -y \
    libldap2-dev \
    libzip-dev \
    unzip \
    libsasl2-dev \
    libssl-dev \
    && docker-php-ext-configure ldap \
    && docker-php-ext-install ldap zip \
    && a2enmod rewrite \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Establecer permisos correctos
RUN chown -R www-data:www-data /var/www/html/
RUN chmod -R 755 /var/www/html/

# Copiar configuración del vhost si la tienes
COPY app_gestion.conf /etc/apache2/sites-available/000-default.conf

# Exponer puerto 80
EXPOSE 80

# Actualización de Dependencias (De nuevo)
RUN apt-get update

RUN apt-get upgrade -y

# Permisos para script copia de seguridad
RUN apt-get install docker.io -y

RUN apt-get install docker-compose -y


# Reiniciamos apache
RUN apache2ctl restart

# Comando por defecto
CMD ["apache2-foreground"]
