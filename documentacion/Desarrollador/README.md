# Documentación del Proyecto GobeBio

Bienvenido a la documentación técnica del sistema GobeBio. Este repositorio contiene toda la información necesaria para que los desarrolladores puedan entender la arquitectura, los flujos de datos y los componentes individuales del proyecto.

La documentación está organizada en dos secciones principales:

1.  **Documentación General**: Explica los conceptos y la arquitectura que aplican a todo el sistema.
2.  **Documentación por Módulo**: Describe en detalle cada uno de los módulos CRUD y funcionales de la aplicación.

---

## 1. Documentación General

Estos documentos proporcionan una visión de alto nivel del proyecto. Se recomienda leerlos en orden para tener una comprensión completa de cómo funciona GobeBio.

- **[01 - Arquitectura General](./Documentación%20General/01_arquitectura_general.md)**: Visión general de la arquitectura híbrida (Laravel + Microservicio Python).
- **[02 - Base de Datos](./Documentación%20General/02_base_de_datos.md)**: Diagrama y descripción de las tablas principales.
- **[03 - Rutas y Controladores](./Documentación%20General/03_rutas_y_controladores.md)**: Patrones de rutas y controladores clave.
- **[04 - Flujos de Sincronización](./Documentación%20General/04_flujos_de_sincronizacion.md)**: Explicación de los procesos asíncronos para sincronizar usuarios y asistencias con el hardware.
- **[05 - Automatización y Comandos](./Documentación%20General/05_automatizacion_y_comandos.md)**: Cómo configurar el Scheduler de Laravel y los comandos de Artisan más útiles.

---

## 2. Documentación por Módulo

Cada archivo describe el propósito, los componentes (Modelo, Controlador, Vistas, etc.), el flujo de datos y las buenas prácticas de un módulo específico.

### Módulos de Estructura Organizacional
- **[01 - Empresas](./Documentación%20por%20Módulo/modulos/01_empresas.md)**
- **[02 - Sucursales](./Documentación%20por%20Módulo/modulos/02_sucursales.md)**
- **[03 - Departamentos](./Documentación%20por%20Módulo/modulos/03_departamentos.md)**

### Módulos de Personal y Horarios
- **[04 - Empleados](./Documentación%20por%20Módulo/modulos/04_empleados.md)**
- **[05 - Horarios](./Documentación%20por%20Módulo/modulos/05_horarios.md)**
- **[06 - Asignación de Horarios](./Documentación%20por%20Módulo/modulos/06_asignacion_horarios.md)**

### Módulos de Gestión Biométrica
- **[07 - Dispositivos](./Documentación%20por%20Módulo/modulos/07_dispositivos.md)**
- **[08 - Mapeo Dispositivo-Empleado](./Documentación%20por%20Módulo/modulos/08_dispositivo_empleado.md)**
- **[09 - Script de Listado AJAX](./Documentación%20por%20Módulo/modulos/09_script_listado_ajax.md)**

### Módulos de Asistencia e Incidencias
- **[10 - Registros de Asistencia](./Documentación%20por%20Módulo/modulos/10_registros_asistencia.md)**
- **[11 - Tipos de Incidencia](./Documentación%20por%20Módulo/modulos/11_tipos_incidencia.md)**
- **[12 - Gestión de Incidencias](./Documentación%20por%20Módulo/modulos/12_incidencias.md)**

### Módulos de Reportes
- **[13 - Reportes de Asistencia](./Documentación%20por%20Módulo/modulos/13_reportes_asistencia.md)**

