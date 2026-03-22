#!/bin/bash
# ============================================================
#  Clínica General — Aprovisionamiento Balanceador
#  Archivo: provision/balanceador.sh
# ============================================================

echo "=== [1/3] Actualizando sistema ==="
apt-get update -y
apt-get upgrade -y

echo "=== [2/3] Instalando Nginx ==="
apt-get install -y nginx

echo "=== [3/3] Configurando balanceo de carga ==="
cat > /etc/nginx/sites-available/clinica <<'EOF'
upstream backends {
    server 10.0.30.10;
    server 10.0.30.20;
}

server {
    listen 80;
    server_name clinicageneral.local;

    location / {
        proxy_pass         http://backends;
        proxy_set_header   Host $host;
        proxy_set_header   X-Real-IP $remote_addr;
        proxy_set_header   X-Forwarded-For $proxy_add_x_forwarded_for;
    }
}
EOF

ln -sf /etc/nginx/sites-available/clinica /etc/nginx/sites-enabled/clinica
rm -f /etc/nginx/sites-enabled/default
nginx -t && systemctl restart nginx
systemctl enable nginx

echo ""
echo "============================================"
echo "  Balanceador listo en 10.0.50.10"
echo "  Round robin: 10.0.30.10 y 10.0.30.20"
echo "============================================"
