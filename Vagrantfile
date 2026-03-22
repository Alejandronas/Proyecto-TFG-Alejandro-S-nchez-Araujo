# -*- mode: ruby -*-
# vi: set ft=ruby :
#
# ============================================================
#  Clínica General — Infraestructura completa
#  Módulo: Implantación de Aplicaciones Web
#  Alumno: Alejandro Sánchez Araujo — 2º ASIR
#
#  Máquinas:
#    - SGBD        10.0.20.10  (MySQL)
#    - Backend 1   10.0.30.10  (Apache + PHP)
#    - Backend 2   10.0.30.20  (Apache + PHP)
#    - Balanceador 10.0.50.10  (Nginx)
# ============================================================

Vagrant.configure("2") do |config|

  # ── SGBD ────────────────────────────────────────────────────
  config.vm.define "sgbd" do |sgbd|
    sgbd.vm.box      = "ubuntu/jammy64"
    sgbd.vm.hostname = "sgbd-clinica"

    sgbd.vm.network "private_network",
      ip:      "10.0.20.10",
      netmask: "255.255.254.0"

    sgbd.vm.provider "virtualbox" do |vb|
      vb.name   = "SGBD-Clinica"
      vb.memory = 2096
      vb.cpus   = 2
    end

    sgbd.vm.provision "shell", path: "provision/sgbd.sh"
  end

  # ── BACKEND 1 ───────────────────────────────────────────────
  config.vm.define "backend1" do |b1|
    b1.vm.box      = "ubuntu/jammy64"
    b1.vm.hostname = "backend1-clinica"

    b1.vm.network "private_network",
      ip:      "10.0.30.10",
      netmask: "255.255.254.0"

    b1.vm.network "private_network",
      ip:      "10.0.20.20",
      netmask: "255.255.254.0"

    b1.vm.provider "virtualbox" do |vb|
      vb.name   = "Backend1-Clinica"
      vb.memory = 2048
      vb.cpus   = 2
    end

    b1.vm.provision "shell", path: "provision/backend.sh",
      env: { "HOSTNAME_VM" => "backend1-clinica" }
  end

  # ── BACKEND 2 ───────────────────────────────────────────────
  config.vm.define "backend2" do |b2|
    b2.vm.box      = "ubuntu/jammy64"
    b2.vm.hostname = "backend2-clinica"

    b2.vm.network "private_network",
      ip:      "10.0.30.20",
      netmask: "255.255.254.0"

    b2.vm.network "private_network",
      ip:      "10.0.20.30",
      netmask: "255.255.254.0"

    b2.vm.provider "virtualbox" do |vb|
      vb.name   = "Backend2-Clinica"
      vb.memory = 2048
      vb.cpus   = 2
    end

    b2.vm.provision "shell", path: "provision/backend.sh",
      env: { "HOSTNAME_VM" => "backend2-clinica" }
  end

  # ── BALANCEADOR ─────────────────────────────────────────────
  config.vm.define "balanceador" do |bal|
    bal.vm.box      = "ubuntu/jammy64"
    bal.vm.hostname = "balanceador-clinica"

    bal.vm.network "private_network",
      ip:      "10.0.50.10",
      netmask: "255.255.254.0"

    bal.vm.network "private_network",
      ip:      "10.0.30.30",
      netmask: "255.255.254.0"

    bal.vm.provider "virtualbox" do |vb|
      vb.name   = "Balanceador-Clinica"
      vb.memory = 2048
      vb.cpus   = 2
    end

    bal.vm.provision "shell", path: "provision/balanceador.sh"
  end

end
