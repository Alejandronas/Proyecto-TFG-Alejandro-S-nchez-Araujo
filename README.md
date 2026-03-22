# 🏥 Clínica General
### Sistema de Gestión Integral · Proyecto de Fin de Grado

**Alumno:** Alejandro Sánchez Araujo  
**Centro:** IES Albarregas · 2º ASIR  
**Dominio:** `clinicageneral.local`  
**Tecnologías:** Vagrant · VirtualBox · Ubuntu · Apache · PHP · MySQL · Nginx · Bootstrap

---

## 1. Descripción del proyecto

El proyecto consiste en el desarrollo e implantación de una infraestructura de red completa que soporte un sistema de gestión integral para una clínica general. La solución abarca cuatro módulos principales: servicios de red, administración de sistemas, base de datos y seguridad, e implantación de aplicaciones web.

La empresa sobre la que se realiza el proyecto es una clínica general con sede en Madrid, organizada en ocho departamentos y gestionada bajo el dominio `clinicageneral.local`. El sistema permite administrar pacientes, citas médicas, consultas, historiales clínicos, análisis de laboratorio, recetas y facturación desde una plataforma web centralizada accesible desde cualquier navegador dentro de la red interna.

Toda la infraestructura se despliega sobre máquinas virtuales Ubuntu sin entorno gráfico mediante Vagrant y VirtualBox, simulando un entorno de producción real con criterios de disponibilidad, seguridad y eficiencia.

---

## 2. Módulos del proyecto

### 2.1 Servicios en red

Implementación de los servicios de red necesarios para el funcionamiento de la clínica:

- **DHCP:** asignación automática de direcciones IP a todos los dispositivos de la clínica, con reservas para servidores y equipos críticos.
- **DNS:** servidor de nombres interno con dominio `clinicageneral.local`, resolución directa e inversa e integración con el servidor web y FTP.
- **FTP:** servidor para el intercambio seguro de archivos entre departamentos (imágenes médicas, informes, resultados de laboratorio) con usuarios y permisos por departamento.
- **HTTP:** servidor web corporativo accesible desde el dominio gestionado por el DNS, con la aplicación de gestión de la clínica.

### 2.2 Administración de sistemas

Gestión y automatización de los sistemas operativos de la infraestructura:

- **Linux:** scripts bash para facilitar la administración del servidor, incluyendo gestión de usuarios, configuración de servicios y administración de permisos.
- **Windows Server:** implementación del dominio `clinicageneral.local` con estructura de unidades organizativas, grupos de seguridad, usuarios y políticas de grupo (GPO).

### 2.3 Base de datos y seguridad

Diseño e implementación de la base de datos relacional y las políticas de seguridad:

- Diseño del modelo entidad-relación con 15 tablas interrelacionadas que cubren todos los módulos del sistema clínico.
- Implementación en MySQL con integridad referencial completa mediante claves foráneas.
- Seguridad con iptables: políticas por defecto DROP, reglas específicas por IP para el acceso al puerto 3306, acceso SSH restringido y registro de eventos en la base de datos.

### 2.4 Implantación de aplicaciones web

Despliegue de una aplicación web de tres capas sobre infraestructura virtualizada:

- **Infraestructura:** dos servidores backend con Apache y PHP, un balanceador de carga Nginx y un servidor de base de datos MySQL, todos sobre Ubuntu sin entorno gráfico.
- **Aplicación web:** sistema de gestión clínica con módulos de pacientes, citas, consultas, laboratorio, recetas y facturación. La aplicación no almacena datos en local y accede siempre a la base de datos centralizada.
- **Alta disponibilidad:** balanceo de carga entre dos backends en round robin para garantizar la continuidad del servicio.

---

## 3. Arquitectura de red

La infraestructura sigue un modelo segmentado en cuatro redes privadas, cada una con una función específica. Todo el tráfico externo entra por el Router NAT y se distribuye según la red de destino.

```
Router NAT / Firewall
│
├── LAN1 (10.0.60.0/23) — Servicios de red
│   ├── DHCP + DNS     10.0.60.10
│   └── FTP            10.0.60.20
│
└── LAN2 (10.0.50.0/23) — Acceso exterior
    └── Balanceador    10.0.50.10  ← Nginx round robin
        │
        └── LAN Interna (10.0.30.0/23)
            ├── Backend 1  10.0.30.10  ← Apache + PHP
            └── Backend 2  10.0.30.20  ← Apache + PHP
                │
                └── LAN Datos (10.0.20.0/23)
                    └── SGBD       10.0.20.10  ← MySQL
```

### 3.1 Segmentos de red

| Red | Rango | Función |
|---|---|---|
| LAN Datos | 10.0.20.0/23 | Red exclusiva para el servidor de base de datos. Solo los backends tienen acceso permitido. |
| LAN Interna | 10.0.30.0/23 | Red de los servidores backend. Comunicación entre backends y balanceador. |
| LAN2 | 10.0.50.0/23 | Red de acceso exterior al balanceador desde la red de la clínica. |
| LAN1 | 10.0.60.0/23 | Red de servicios. Aloja el servidor DHCP+DNS y el servidor FTP. |

### 3.2 Servidores y direcciones IP

| Servidor | IP principal | IP secundaria | Función |
|---|---|---|---|
| SGBD | 10.0.20.10 | — | Base de datos MySQL |
| Backend 1 | 10.0.30.10 | 10.0.20.20 | Apache + PHP |
| Backend 2 | 10.0.30.20 | 10.0.20.30 | Apache + PHP |
| Balanceador | 10.0.50.10 | 10.0.30.30 | Nginx round robin |
| DHCP + DNS | 10.0.60.10 | — | isc-dhcp-server + bind9 |
| FTP | 10.0.60.20 | — | vsftpd con usuarios por departamento |

---

## 4. Base de datos

Base de datos `clinica` implementada en MySQL con juego de caracteres `utf8mb4` y diseño normalizado con integridad referencial completa.

| Tabla | Tipo | Descripción |
|---|---|---|
| `DEPARTAMENTO` | Maestra | Departamentos de la clínica |
| `GRUPO` | Maestra | Grupos de trabajo multidisciplinares |
| `PACIENTE` | Maestra | Datos personales de los pacientes |
| `TIPO_PRUEBA` | Maestra | Catálogo de análisis de laboratorio |
| `EMPLEADO` | Dependiente | Personal de la clínica con rol y departamento |
| `EMPLEADOS_GRUPOS` | Dependiente | Relación N:M entre empleados y grupos |
| `HISTORIAL_MEDICO` | Dependiente | Historial clínico por paciente (1:1) |
| `CITA` | Dependiente | Citas programadas entre pacientes y médicos |
| `CONSULTA` | Dependiente | Consultas médicas con diagnóstico y tratamiento |
| `RECETA` | Dependiente | Recetas emitidas durante las consultas |
| `DETALLE_RECETA` | Dependiente | Medicamentos, dosis y duración por receta |
| `SOLICITUD_ANALISIS` | Dependiente | Solicitudes de análisis generadas en consulta |
| `RESULTADO_LABORATORIO` | Dependiente | Resultados numéricos de los análisis |
| `FACTURA` | Dependiente | Facturas asociadas a pacientes y consultas |
| `USUARIO` | Dependiente | Cuentas de acceso con rol asignado |

---

## 5. Dominio Windows Server

El dominio `clinicageneral.local` está implementado en Windows Server con Active Directory. La estructura organizativa refleja los departamentos reales de la clínica.

### 5.1 Unidades organizativas

```
clinicageneral.local
└── CLINICA
    ├── Dirección         → director.general, subdirector
    ├── Administración    → jefe.administracion, facturacion1
    ├── RRHH              → jefe.rrhh, rrhh.tecnico1
    ├── Informática       → admin.sistema, tecnico.it1, tecnico.it2
    ├── Especialistas     → esp.cardiologia, esp.dermatologia, esp.traumatologia
    ├── Enfermería        → enf.ana, enf.carlos
    ├── Recepción         → recepcion1, recepcion2
    └── Laboratorio       → lab.tecnico1, lab.tecnico2
```

### 5.2 Grupos de seguridad

| Grupo | Ámbito | Descripción |
|---|---|---|
| DIR_Usuarios | Global | Dirección con acceso a recursos confidenciales |
| ADM_Usuarios | Global | Administración y Facturación |
| RRHH_Usuarios | Global | Recursos Humanos y gestión de personal |
| IT_Usuarios | Global | Departamento IT con acceso a herramientas internas |
| IT_Admins | Global | Administradores de dominio con permisos elevados |
| ESP_Usuarios | Global | Especialistas con acceso a historial y agenda |
| ENF_Usuarios | Global | Enfermería con acceso a historial y seguimiento |
| REC_Usuarios | Global | Recepción con acceso a gestión de citas |
| LAB_Usuarios | Global | Laboratorio con acceso a resultados y análisis |

---

## 6. Aplicación web

Aplicación desarrollada con PHP y Bootstrap sobre Apache, siguiendo arquitectura MVC. No almacena datos en local y accede siempre a la base de datos centralizada.

### 6.1 Módulos

- **Pacientes:** registro, consulta y modificación de datos personales e historial médico.
- **Citas:** programación, modificación y cancelación con asignación de médico.
- **Consultas:** registro de diagnósticos, tratamientos y generación de recetas.
- **Laboratorio:** solicitud de análisis y consulta de resultados por paciente.
- **Facturación:** generación y consulta de facturas asociadas a los servicios.
- **Control de acceso:** login con roles diferenciados (médico, enfermera, recepcionista, laboratorio, administrador).

### 6.2 Tecnologías

| Tecnología | Uso |
|---|---|
| PHP | Lógica del servidor, controladores y modelos |
| HTML + Bootstrap 5 | Estructura y estilos de las vistas |
| MySQL | Almacenamiento persistente de todos los datos |
| Apache | Servidor web con mod_rewrite habilitado |
| Nginx | Balanceador de carga en round robin |
| Python | Automatización y scripts de administración |
| Vagrant + VirtualBox | Virtualización de toda la infraestructura |

### 6.3 Estructura del proyecto

```
clinica-general/
├── Vagrantfile
├── README.md
├── provision/
│   ├── sgbd.sh
│   ├── backend.sh
│   └── balanceador.sh
└── app/
    ├── index.php
    ├── login.php
    ├── panel.php
    ├── db/
    │   └── Conexion.php
    ├── includes/
    │   ├── header.php
    │   └── footer.php
    ├── controllers/
    ├── models/
    └── views/
        ├── citas/
        ├── pacientes/
        ├── consultas/
        ├── laboratorio/
        └── facturas/
```

---

### 6.4 Levantar la infraestructura

```bash
# Levantar todo
vagrant up

# Levantar una máquina concreta
vagrant up sgbd
vagrant up backend1
vagrant up backend2
vagrant up balanceador

# Acceder a una máquina
vagrant ssh sgbd

# Reaprovisionar sin recrear
vagrant provision backend1

# Apagar todo
vagrant halt
```
