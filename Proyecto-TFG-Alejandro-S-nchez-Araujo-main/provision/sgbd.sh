#!/bin/bash
# ============================================================
#  Clínica General — Aprovisionamiento SGBD
#  Archivo: provision/sgbd.sh
# ============================================================

echo "=== [1/5] Actualizando sistema ==="
apt-get update -y
apt-get upgrade -y

echo "=== [2/5] Instalando MySQL ==="
apt-get install -y mysql-server

echo "=== [3/5] Configurando MySQL ==="
systemctl enable mysql
systemctl start mysql

# Permitir conexiones desde la red interna
sed -i "s/^bind-address.*/bind-address = 0.0.0.0/" /etc/mysql/mysql.conf.d/mysqld.cnf
sed -i "s/^mysqlx-bind-address.*/mysqlx-bind-address = 0.0.0.0/" /etc/mysql/mysql.conf.d/mysqld.cnf
systemctl restart mysql

echo "=== [4/5] Creando base de datos ==="
mysql -u root <<SQL

  DROP DATABASE IF EXISTS clinica;

  CREATE DATABASE clinica
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

  CREATE USER IF NOT EXISTS 'clinica_user'@'10.0.30.%'
    IDENTIFIED BY '1234';
  GRANT ALL PRIVILEGES ON clinica.* TO 'clinica_user'@'10.0.30.%';

  CREATE USER IF NOT EXISTS 'clinica_user'@'10.0.20.%'
    IDENTIFIED BY '1234';
  GRANT ALL PRIVILEGES ON clinica.* TO 'clinica_user'@'10.0.20.%';

  CREATE USER IF NOT EXISTS 'clinica_admin'@'localhost'
    IDENTIFIED BY 'Admin_Cl1nica!';
  GRANT ALL PRIVILEGES ON clinica.* TO 'clinica_admin'@'localhost';

  FLUSH PRIVILEGES;

  USE clinica;

  CREATE TABLE DEPARTAMENTO (
    id_departamento INT AUTO_INCREMENT PRIMARY KEY,
    nombre          VARCHAR(100) NOT NULL UNIQUE,
    descripcion     TEXT
  );

  CREATE TABLE GRUPO (
    id_grupo     INT AUTO_INCREMENT PRIMARY KEY,
    nombre_grupo VARCHAR(100) NOT NULL UNIQUE,
    descripcion  TEXT
  );

  CREATE TABLE PACIENTE (
    id_paciente          INT AUTO_INCREMENT PRIMARY KEY,
    nombre               VARCHAR(100) NOT NULL,
    apellido             VARCHAR(100) NOT NULL,
    fecha_nacimiento     DATE,
    genero               VARCHAR(20),
    direccion            VARCHAR(255),
    telefono             VARCHAR(20),
    email                VARCHAR(150) UNIQUE,
    dni                  VARCHAR(10)  UNIQUE,
    num_seguridad_social VARCHAR(20)  UNIQUE
  );

  CREATE TABLE TIPO_PRUEBA (
    id_prueba     INT AUTO_INCREMENT PRIMARY KEY,
    nombre_prueba VARCHAR(100) NOT NULL UNIQUE,
    unidad_medida VARCHAR(50)
  );

  CREATE TABLE ESPECIALIDAD (
    id_especialidad INT AUTO_INCREMENT PRIMARY KEY,
    nombre          VARCHAR(100) NOT NULL UNIQUE,
    descripcion     TEXT
  );

  CREATE TABLE EMPLEADO (
    id_empleado        INT AUTO_INCREMENT PRIMARY KEY,
    nombre             VARCHAR(100) NOT NULL,
    apellido           VARCHAR(100) NOT NULL,
    fecha_contratacion DATE,
    salario            DECIMAL(10,2),
    rol                VARCHAR(50),
    id_departamento    INT,
    id_especialidad    INT NULL,
    FOREIGN KEY (id_departamento) REFERENCES DEPARTAMENTO(id_departamento),
    FOREIGN KEY (id_especialidad) REFERENCES ESPECIALIDAD(id_especialidad)
  );

  CREATE TABLE EMPLEADOS_GRUPOS (
    id_empleado INT,
    id_grupo    INT,
    PRIMARY KEY (id_empleado, id_grupo),
    FOREIGN KEY (id_empleado) REFERENCES EMPLEADO(id_empleado),
    FOREIGN KEY (id_grupo)    REFERENCES GRUPO(id_grupo)
  );

  CREATE TABLE HISTORIAL_MEDICO (
    id_historial            INT AUTO_INCREMENT PRIMARY KEY,
    id_paciente             INT NOT NULL UNIQUE,
    fecha_creacion          DATETIME DEFAULT NOW(),
    antecedentes_familiares TEXT,
    alergias                TEXT,
    FOREIGN KEY (id_paciente) REFERENCES PACIENTE(id_paciente)
  );

  CREATE TABLE CITA (
    id_cita               INT AUTO_INCREMENT PRIMARY KEY,
    id_paciente           INT NOT NULL,
    id_empleado_sanitario INT NULL,
    fecha_cita            DATE NOT NULL,
    hora_cita             TIME NOT NULL,
    estado                VARCHAR(30) DEFAULT 'programada',
    FOREIGN KEY (id_paciente)           REFERENCES PACIENTE(id_paciente),
    FOREIGN KEY (id_empleado_sanitario) REFERENCES EMPLEADO(id_empleado)
  );

  CREATE TABLE CONSULTA (
    id_consulta           INT AUTO_INCREMENT PRIMARY KEY,
    id_paciente           INT NOT NULL,
    id_empleado_sanitario INT NOT NULL,
    fecha_consulta        DATETIME DEFAULT NOW(),
    motivo_consulta       TEXT,
    diagnostico           TEXT,
    tratamiento           TEXT,
    FOREIGN KEY (id_paciente)           REFERENCES PACIENTE(id_paciente),
    FOREIGN KEY (id_empleado_sanitario) REFERENCES EMPLEADO(id_empleado)
  );

  CREATE TABLE RECETA (
    id_receta     INT AUTO_INCREMENT PRIMARY KEY,
    id_consulta   INT NOT NULL,
    fecha_emision DATETIME DEFAULT NOW(),
    FOREIGN KEY (id_consulta) REFERENCES CONSULTA(id_consulta)
  );

  CREATE TABLE DETALLE_RECETA (
    id_detalle  INT AUTO_INCREMENT PRIMARY KEY,
    id_receta   INT NOT NULL,
    medicamento VARCHAR(150) NOT NULL,
    dosis       VARCHAR(100),
    frecuencia  VARCHAR(100),
    duracion    VARCHAR(100),
    FOREIGN KEY (id_receta) REFERENCES RECETA(id_receta)
  );

  CREATE TABLE SOLICITUD_ANALISIS (
    id_solicitud    INT AUTO_INCREMENT PRIMARY KEY,
    id_consulta     INT NOT NULL,
    fecha_solicitud DATETIME DEFAULT NOW(),
    prioridad       VARCHAR(20) DEFAULT 'normal',
    FOREIGN KEY (id_consulta) REFERENCES CONSULTA(id_consulta)
  );

  CREATE TABLE RESULTADO_LABORATORIO (
    id_resultado        INT AUTO_INCREMENT PRIMARY KEY,
    id_solicitud        INT NOT NULL,
    id_prueba           INT NOT NULL,
    valor_obtenido      DECIMAL(12,4),
    fecha_procesamiento DATETIME DEFAULT NOW(),
    observaciones       TEXT,
    FOREIGN KEY (id_solicitud) REFERENCES SOLICITUD_ANALISIS(id_solicitud),
    FOREIGN KEY (id_prueba)    REFERENCES TIPO_PRUEBA(id_prueba)
  );

  CREATE TABLE FACTURA (
    id_factura  INT AUTO_INCREMENT PRIMARY KEY,
    id_paciente INT NOT NULL,
    id_consulta INT,
    fecha       DATETIME DEFAULT NOW(),
    total       DECIMAL(10,2),
    estado      VARCHAR(20) DEFAULT 'pendiente',
    FOREIGN KEY (id_paciente) REFERENCES PACIENTE(id_paciente),
    FOREIGN KEY (id_consulta) REFERENCES CONSULTA(id_consulta)
  );

  CREATE TABLE USUARIO (
    id_usuario  INT AUTO_INCREMENT PRIMARY KEY,
    username    VARCHAR(50) NOT NULL UNIQUE,
    password    VARCHAR(255) NOT NULL,
    rol         VARCHAR(30),
    id_empleado INT NULL,
    id_paciente INT NULL,
    FOREIGN KEY (id_empleado) REFERENCES EMPLEADO(id_empleado),
    FOREIGN KEY (id_paciente) REFERENCES PACIENTE(id_paciente)
  );

  CREATE TABLE MEDICO_PACIENTE (
    id_empleado   INT NOT NULL,
    id_paciente   INT NOT NULL,
    fecha_alta    DATE DEFAULT (CURDATE()),
    PRIMARY KEY (id_empleado, id_paciente),
    FOREIGN KEY (id_empleado) REFERENCES EMPLEADO(id_empleado),
    FOREIGN KEY (id_paciente) REFERENCES PACIENTE(id_paciente)
  );

  CREATE TABLE HISTORIAL_CLINICO (
    id_historial    INT AUTO_INCREMENT PRIMARY KEY,
    id_paciente     INT          NOT NULL,
    id_empleado     INT          NOT NULL,
    fecha           DATE         NOT NULL,
    motivo_consulta VARCHAR(255),
    tipo_consulta   ENUM('presencial','urgencia','teleconsulta') DEFAULT 'presencial',
    diagnostico     VARCHAR(255) NOT NULL,
    tratamiento     TEXT,
    notas           TEXT,
    fecha_registro  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_paciente) REFERENCES PACIENTE(id_paciente),
    FOREIGN KEY (id_empleado) REFERENCES EMPLEADO(id_empleado)
  );

  INSERT INTO DEPARTAMENTO (nombre, descripcion) VALUES
    ('Dirección',      'Administración general y decisiones estratégicas'),
    ('Administración', 'Facturación, contabilidad y gestión económica'),
    ('RRHH',           'Gestión de personal y nóminas'),
    ('Informática',    'Gestión del dominio, servidores y sistemas'),
    ('Especialistas',  'Médicos especialistas que atienden pacientes'),
    ('Enfermería',     'Personal sanitario de apoyo'),
    ('Recepción',      'Gestión de citas y atención al paciente'),
    ('Laboratorio',    'Procesamiento de análisis y resultados médicos');

  INSERT INTO GRUPO (nombre_grupo, descripcion) VALUES
    ('Equipo de Emergencias', 'Personal disponible para urgencias'),
    ('Comité de Ética',       'Revisión de casos clínicos complejos'),
    ('Equipo de Guardia',     'Personal en turno de guardia');

  INSERT INTO TIPO_PRUEBA (nombre_prueba, unidad_medida) VALUES
    ('Hemograma',    'células/μL'),
    ('Glucosa',      'mg/dL'),
    ('Colesterol',   'mg/dL'),
    ('Triglicéridos','mg/dL'),
    ('Creatinina',   'mg/dL');

  INSERT INTO PACIENTE (nombre, apellido, fecha_nacimiento, genero, direccion, telefono, email, dni, num_seguridad_social) VALUES
    ('María',   'García López',   '1985-03-12', 'F', 'Calle Mayor 5, Madrid',      '611000001', 'maria.garcia@email.com',    '12345678A', '28 00000001 11'),
    ('Carlos',  'Martínez Ruiz',  '1972-07-28', 'M', 'Avenida del Sol 22, Madrid', '611000002', 'carlos.martinez@email.com', '23456789B', '28 00000002 22'),
    ('Laura',   'Sánchez Pérez',  '1990-11-05', 'F', 'Calle Luna 8, Madrid',       '611000003', 'laura.sanchez@email.com',   '34567890C', '28 00000003 33'),
    ('Antonio', 'Fernández Gil',  '1965-05-19', 'M', 'Paseo de la Castellana 10',  '611000004', 'antonio.fernandez@email.com','45678901D', '28 00000004 44'),
    ('Sofía',   'López Moreno',   '2000-08-30', 'F', 'Calle Alcalá 100, Madrid',   '611000005', 'sofia.lopez@email.com',     '56789012E', '28 00000005 55');

  INSERT INTO ESPECIALIDAD (nombre, descripcion) VALUES
    ('Medicina de Familia / General', 'Primer punto de contacto. Atienden de todo un poco.'),
    ('Pediatría',                     'Especializados en niños y adolescentes.'),
    ('Dermatología',                  'Problemas de la piel.'),
    ('Traumatología',                 'Huesos y lesiones físicas.'),
    ('Oftalmología',                  'Salud visual.'),
    ('Enfermería',                    'Realizan curas, ponen vacunas y toman constantes.'),
    ('Análisis Clínicos',             'Personal del laboratorio que procesa la sangre y orina.'),
    ('Radiología',                    'Radiografías, ecografías o resonancias.'),
    ('Fisioterapia',                  'Rehabilitación tras lesiones.');

  -- id_especialidad: 1=Medicina Familiar, 6=Enfermería, 7=Análisis Clínicos, NULL=sin especialidad clínica
  INSERT INTO EMPLEADO (nombre, apellido, fecha_contratacion, salario, rol, id_departamento, id_especialidad) VALUES
    ('Pedro',   'Alonso Vega',    '2018-01-15', 55000.00, 'medico',          5, 1),
    ('Carmen',  'Torres Díaz',    '2020-06-01', 32000.00, 'enfermera',       6, 6),
    ('Antonio', 'Ruiz Morales',   '2019-03-10', 28000.00, 'recepcionista',   7, NULL),
    ('Ana',     'Jiménez Castro', '2017-09-01', 42000.00, 'administrador',   4, NULL),
    ('Luis',    'Romero Blanco',  '2021-02-15', 52000.00, 'medico',          5, 4),
    ('Elena',   'Navarro Vidal',  '2022-07-01', 30000.00, 'enfermera',       6, 6),
    ('Miguel',  'Serrano Ortiz',  '2016-11-20', 35000.00, 'laboratorio',     8, 7);

  INSERT INTO EMPLEADOS_GRUPOS (id_empleado, id_grupo) VALUES
    (1, 1), (2, 1), (5, 1),
    (1, 2), (5, 2),
    (2, 3), (6, 3);

  INSERT INTO HISTORIAL_MEDICO (id_paciente, antecedentes_familiares, alergias) VALUES
    (1, 'Diabetes tipo 2 en madre', 'Penicilina'),
    (2, 'Hipertensión en padre',    'Ninguna conocida'),
    (3, 'Sin antecedentes',         'Polen, ácaros'),
    (4, 'Cardiopatía en abuelo',    'Ibuprofeno'),
    (5, 'Sin antecedentes',         'Ninguna conocida');

  INSERT INTO CITA (id_paciente, id_empleado_sanitario, fecha_cita, hora_cita, estado) VALUES
    (1, 1, '2025-06-10', '09:00:00', 'completada'),
    (2, 5, '2025-06-11', '10:30:00', 'completada'),
    (3, 1, '2025-06-12', '11:00:00', 'programada'),
    (4, 5, '2025-06-13', '09:30:00', 'programada'),
    (5, 1, '2025-06-14', '12:00:00', 'cancelada');

  INSERT INTO CONSULTA (id_paciente, id_empleado_sanitario, motivo_consulta, diagnostico, tratamiento) VALUES
    (1, 1, 'Dolor de cabeza frecuente', 'Migraña tensional', 'Ibuprofeno 400mg cada 8h'),
    (2, 5, 'Revisión anual',            'Paciente sano',     'Sin tratamiento'),
    (4, 5, 'Dolor en el pecho',         'Angina estable',    'Nitroglicerina sublingual');

  INSERT INTO RECETA (id_consulta) VALUES (1), (3);

  INSERT INTO DETALLE_RECETA (id_receta, medicamento, dosis, frecuencia, duracion) VALUES
    (1, 'Ibuprofeno',     '400mg', 'Cada 8 horas',   '5 días'),
    (1, 'Omeprazol',      '20mg',  'En ayunas',      '5 días'),
    (2, 'Nitroglicerina', '0.4mg', 'Según necesidad','Indefinido');

  INSERT INTO SOLICITUD_ANALISIS (id_consulta, prioridad) VALUES
    (1, 'normal'),
    (3, 'urgente');

  INSERT INTO RESULTADO_LABORATORIO (id_solicitud, id_prueba, valor_obtenido, observaciones) VALUES
    (1, 1, 5200000, 'Dentro de valores normales'),
    (1, 2, 95.00,   'Glucosa normal'),
    (2, 3, 210.00,  'Colesterol levemente elevado'),
    (2, 4, 180.00,  'Triglicéridos en límite alto');

  INSERT INTO MEDICO_PACIENTE (id_empleado, id_paciente) VALUES
    (1, 1), (1, 3), (1, 5),
    (5, 2), (5, 4);

  INSERT INTO HISTORIAL_CLINICO (id_paciente, id_empleado, fecha, motivo_consulta, tipo_consulta, diagnostico, tratamiento, notas) VALUES
    (1, 1, '2025-06-10', 'Dolor de cabeza frecuente', 'presencial',    'Migraña tensional', 'Ibuprofeno 400mg cada 8h',  'Paciente refiere estrés laboral'),
    (3, 1, '2025-06-12', 'Dolor de garganta',         'presencial',    'Faringitis aguda',  'Amoxicilina 500mg 7 días',  'Sin alergias conocidas'),
    (2, 5, '2025-06-11', 'Revisión anual rutinaria',  'presencial',    'Revisión anual',    'Sin tratamiento',           'Todos los valores en rango normal'),
    (4, 5, '2025-06-13', 'Dolor en el pecho',         'urgencia',      'Angina estable',    'Nitroglicerina sublingual', 'Se recomienda reposo y dieta baja en grasas');

  INSERT INTO FACTURA (id_paciente, id_consulta, total, estado) VALUES
    (1, 1, 45.00,  'pagada'),
    (2, 2, 35.00,  'pagada'),
    (4, 3, 120.00, 'pendiente');

  INSERT INTO USUARIO (username, password, rol, id_empleado, id_paciente) VALUES
    ('admin',           SHA2('admin1234',  256), 'administrador', 4,    NULL),
    ('pedro.alonso',    SHA2('medico1234', 256), 'medico',        1,    NULL),
    ('carmen.torres',   SHA2('enf1234',    256), 'enfermera',     2,    NULL),
    ('antonio.ruiz',    SHA2('rec1234',    256), 'recepcionista', 3,    NULL),
    ('luis.romero',     SHA2('medico1234', 256), 'medico',        5,    NULL),
    ('elena.navarro',   SHA2('enf1234',    256), 'enfermera',     6,    NULL),
    ('miguel.serrano',  SHA2('lab1234',    256), 'laboratorio',   7,    NULL),
    ('maria.garcia',    SHA2('pac1234',    256), 'paciente',      NULL, 1),
    ('carlos.martinez', SHA2('pac1234',    256), 'paciente',      NULL, 2),
    ('laura.sanchez',   SHA2('pac1234',    256), 'paciente',      NULL, 3),
    ('antonio.fernandez',SHA2('pac1234',   256), 'paciente',      NULL, 4),
    ('sofia.lopez',     SHA2('pac1234',    256), 'paciente',      NULL, 5);

SQL

echo "=== [5/5] Configurando iptables ==="
iptables -A INPUT -p tcp --dport 3306 -s 10.0.30.10 -j ACCEPT
iptables -A INPUT -p tcp --dport 3306 -s 10.0.30.20 -j ACCEPT
iptables -A INPUT -p tcp --dport 3306 -s 10.0.20.20 -j ACCEPT
iptables -A INPUT -p tcp --dport 3306 -s 10.0.20.30 -j ACCEPT
iptables -A INPUT -p tcp --dport 3306 -j DROP

echo ""
echo "============================================"
echo "  SGBD listo en 10.0.20.10:3306"
echo "  Base de datos: clinica (18 tablas)"
echo "  Usuario app:   clinica_user / 1234"
echo "  Usuario admin: clinica_admin / Admin_Cl1nica!"
echo "============================================"
