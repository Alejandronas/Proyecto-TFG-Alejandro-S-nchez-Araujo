#!/bin/bash
# ============================================================
#  Clínica General — Aprovisionamiento Servidor NFS
#  Archivo: provision/nfs.sh
# ============================================================

echo "=== [1/4] Actualizando sistema ==="
apt-get update -y

echo "=== [2/4] Instalando servidor NFS ==="
apt-get install -y nfs-kernel-server

echo "=== [3/4] Creando carpeta compartida y copiando app ==="
mkdir -p /srv/app
cp -r /vagrant/app/. /srv/app/
chown -R nobody:nogroup /srv/app/
chmod -R 755 /srv/app/

echo "=== [4/4] Configurando exportaciones NFS ==="
# Exporta /srv/app a toda la red 10.0.30.0/23 (backends)
echo "/srv/app  10.0.30.0/23(rw,sync,no_subtree_check,no_root_squash)" >> /etc/exports

exportfs -a
systemctl enable nfs-kernel-server
systemctl restart nfs-kernel-server

echo ""
echo "============================================"
echo "  NFS listo en 10.0.30.40"
echo "  Exportando: /srv/app"
echo "  Red autorizada: 10.0.30.0/23"
echo "============================================"
