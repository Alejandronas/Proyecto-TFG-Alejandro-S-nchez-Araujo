#!/bin/bash
# ============================================================
#  Clínica General — Aprovisionamiento Backend
#  Archivo: provision/backend.sh
#  Válido para Backend 1 y Backend 2
# ============================================================

echo "=== [1/4] Actualizando sistema ==="
apt-get update -y
apt-get upgrade -y

echo "=== [2/4] Instalando Apache, PHP y cliente MySQL ==="
apt-get install -y apache2 php libapache2-mod-php php-mysql mysql-client

echo "=== [3/4] Configurando Apache ==="
systemctl enable apache2
systemctl start apache2
a2enmod rewrite

cat > /etc/apache2/sites-available/clinica.conf <<EOF
<VirtualHost *:80>
    ServerName ${HOSTNAME_VM}
    DocumentRoot /var/www/html

    <Directory /var/www/html>
        AllowOverride All
        Require all granted
        DirectoryIndex index.php index.html
    </Directory>

    ErrorLog \${APACHE_LOG_DIR}/clinica_error.log
    CustomLog \${APACHE_LOG_DIR}/clinica_access.log combined
</VirtualHost>
EOF

a2ensite clinica.conf
a2dissite 000-default.conf

# Eliminar index.html por defecto de Apache
rm -f /var/www/html/index.html

# Copiar la aplicación web al directorio de Apache
if [ -d /vagrant/app ]; then
  cp -r /vagrant/app/. /var/www/html/
  chown -R www-data:www-data /var/www/html/
  echo "Aplicación copiada desde /vagrant/app"
else
  echo "AVISO: carpeta /vagrant/app no encontrada — sube los archivos manualmente"
fi

systemctl restart apache2

echo "=== [4/4] Verificando conexión con SGBD ==="
mysql -h 10.0.20.10 -u clinica_user -p1234 clinica -e "SHOW TABLES;" && \
  echo "Conexión con SGBD: OK" || \
  echo "AVISO: SGBD no disponible aún — levanta el SGBD primero"

echo ""
echo "============================================"
echo "  Backend listo: ${HOSTNAME_VM}"
echo "  Apache + PHP instalados"
echo "  Rewrite habilitado"
echo "  index.php tiene prioridad sobre index.html"
echo "============================================"
