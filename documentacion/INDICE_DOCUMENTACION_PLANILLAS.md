# 📚 Índice de Documentación - Módulo de Planillas

Bienvenido al **Módulo de Procesamiento y Cierre de Planillas** de GobeBio. Esta es tu guía para navegar toda la documentación disponible.

---

## 🚀 Para Empezar

### ¿Eres nuevo? Empieza aquí:

1. **[Quick Start](MODULO_PLANILLAS_QUICKSTART.md)** ⭐  
   Guía rápida de 5 minutos para entender qué hace el módulo y cómo usarlo

2. **[Resumen de Implementación](RESUMEN_IMPLEMENTACION_PLANILLAS.md)**  
   Visión general de todo lo que se implementó

---

## 👨‍💼 Para Usuarios (RRHH / Administradores)

### Guías de Uso

- **[Quick Start](MODULO_PLANILLAS_QUICKSTART.md)**  
  Flujo completo: Crear → Cerrar → Revisar → Exportar

### Tutoriales en Video (Próximamente)
- Cómo crear un período de planilla
- Cómo interpretar los resultados
- Cómo exportar para nómina

---

## 👨‍💻 Para Desarrolladores

### Documentación Técnica

1. **[Módulo Completo](documentacion/Desarrollador/Módulos/03_modulo_procesamiento_planillas.md)** 📖  
   Documentación técnica exhaustiva:
   - Arquitectura del sistema
   - Descripción de tablas y campos
   - Modelos, servicios y jobs
   - Flujos de procesamiento
   - API y endpoints
   - Troubleshooting

2. **[Base de Datos Actualizada](documentacion/Desarrollador/Documentación%20General/02_base_de_datos.md)**  
   Diagrama ER actualizado con las nuevas tablas

### Ejemplos de Código

3. **[Ejemplos Prácticos de Uso](app/Examples/EjemploPlanillaUsage.php)** 💡  
   12 ejemplos listos para ejecutar:
   - Crear período
   - Procesar asíncrona y sincronamente
   - Consultar resúmenes
   - Filtrar empleados
   - Exportar datos
   - Ver detalles de tardanzas y faltas
   - Comparar períodos
   - Estadísticas generales

### Personalización

4. **[Guía de Personalización](documentacion/GUIA_PERSONALIZACION_PLANILLAS.md)** 🎨  
   Cómo adaptar el módulo a tus necesidades:
   - Personalizar cálculo de horas extra (por país)
   - Modificar detección de tardanzas
   - Agregar feriados
   - Calcular descuentos y bonos
   - Notificaciones por email
   - Agregar gráficos
   - Aprobación multi-nivel
   - API para integración

---

## 📂 Estructura de Archivos

### Backend (Laravel)

```
backend/
├── app/
│   ├── Models/
│   │   ├── PeriodoPlanilla.php           # Modelo principal
│   │   └── ResumenMensualAsistencia.php  # Modelo de resúmenes
│   │
│   ├── Services/
│   │   └── PlanillaService.php           # Lógica de negocio
│   │
│   ├── Jobs/
│   │   └── ProcesarPlanillaJob.php       # Job asíncrono
│   │
│   ├── Http/Controllers/Admin/
│   │   └── PeriodoPlanillaController.php # Controlador CRUD
│   │
│   └── Examples/
│       └── EjemploPlanillaUsage.php      # Ejemplos de código
│
├── database/migrations/
│   ├── 2025_12_15_225208_create_periodos_planilla_table.php
│   └── 2025_12_15_225208_create_resumen_mensual_asistencia_table.php
│
├── resources/views/admin/periodos/
│   ├── index.blade.php    # Listado
│   ├── create.blade.php   # Formulario
│   ├── show.blade.php     # Detalle
│   └── empleado.blade.php # Detalle empleado
│
└── routes/
    └── web.php            # Rutas agregadas
```

### Documentación

```
documentacion/
├── MODULO_PLANILLAS_QUICKSTART.md
├── RESUMEN_IMPLEMENTACION_PLANILLAS.md
├── GUIA_PERSONALIZACION_PLANILLAS.md
├── INDICE_DOCUMENTACION_PLANILLAS.md (este archivo)
│
└── Desarrollador/
    ├── Módulos/
    │   └── 03_modulo_procesamiento_planillas.md
    └── Documentación General/
        └── 02_base_de_datos.md (actualizado)
```

---

## 🎯 Flujos de Trabajo

### Flujo de Usuario (RRHH)

```
1. Crear Período
   ↓
2. [Esperar fin de mes]
   ↓
3. Cerrar Período
   ↓
4. [Sistema procesa automáticamente]
   ↓
5. Revisar Resultados
   ↓
6. Exportar a Excel
   ↓
7. Importar en Sistema de Nómina
```

### Flujo Técnico

```
1. Usuario click "Cerrar Período"
   ↓
2. Controller despacha ProcesarPlanillaJob
   ↓
3. Job llama a PlanillaService
   ↓
4. Service itera por cada empleado:
   - Obtiene marcaciones del período
   - Obtiene horarios asignados
   - Calcula día por día:
     • Tardanzas
     • Faltas
     • Horas trabajadas
     • Horas extra
   - Guarda resumen consolidado
   ↓
5. Job actualiza estado a "cerrado"
   ↓
6. Usuario visualiza resultados
```

---

## 🔍 Búsqueda Rápida

### Necesito saber cómo...

- **...usar el módulo**: [Quick Start](MODULO_PLANILLAS_QUICKSTART.md)
- **...cambiar el cálculo de horas extra**: [Guía de Personalización](documentacion/GUIA_PERSONALIZACION_PLANILLAS.md#-personalizar-cálculo-de-horas-extra)
- **...agregar notificaciones**: [Guía de Personalización](documentacion/GUIA_PERSONALIZACION_PLANILLAS.md#-agregar-notificaciones)
- **...entender la arquitectura**: [Módulo Completo](documentacion/Desarrollador/Módulos/03_modulo_procesamiento_planillas.md#%EF%B8%8F-arquitectura-del-módulo)
- **...ver ejemplos de código**: [Ejemplos](app/Examples/EjemploPlanillaUsage.php)
- **...troubleshoot un problema**: [Módulo Completo](documentacion/Desarrollador/Módulos/03_modulo_procesamiento_planillas.md#-troubleshooting)

---

## 📊 Recursos Adicionales

### Comandos Útiles

```bash
# Ver migraciones
php artisan migrate:status

# Ejecutar queue worker
php artisan queue:work

# Acceder a ejemplos en Tinker
php artisan tinker
$ejemplo = new App\Examples\EjemploPlanillaUsage();
$ejemplo->crearPeriodo();

# Ver rutas del módulo
php artisan route:list | grep periodos
```

### URLs del Módulo

- **Listado:** `/admin/periodos-planilla`
- **Crear:** `/admin/periodos-planilla/create`
- **Ver:** `/admin/periodos-planilla/{id}`
- **Exportar:** `/admin/periodos-planilla/{id}/exportar`

---

## ✅ Checklist de Verificación

Antes de usar en producción, verifica:

- [ ] Migraciones ejecutadas (`php artisan migrate`)
- [ ] Queue worker corriendo (`php artisan queue:work`)
- [ ] Creado período de prueba
- [ ] Procesado período de prueba
- [ ] Revisados cálculos (tardanzas, horas extra)
- [ ] Exportación funciona
- [ ] Personalizado según normativa local (si aplica)
- [ ] Agregado al menú de Voyager (opcional)
- [ ] Configurados permisos (opcional)
- [ ] Capacitado equipo de RRHH

---

## 🆘 ¿Necesitas Ayuda?

### Problemas Comunes

1. **"El job no se ejecuta"**  
   → Ver: [Troubleshooting](documentacion/Desarrollador/Módulos/03_modulo_procesamiento_planillas.md#el-job-no-se-ejecuta)

2. **"Los cálculos no son correctos"**  
   → Ver: [Personalización de Cálculos](documentacion/GUIA_PERSONALIZACION_PLANILLAS.md)

3. **"Quedó en estado 'procesando'"**  
   → Ver: [Troubleshooting](documentacion/Desarrollador/Módulos/03_modulo_procesamiento_planillas.md#estado-queda-en-procesando-indefinidamente)

### Soporte Técnico

- 📧 Email: [tu-email@empresa.com]
- 📚 Documentación: Este repositorio
- 💬 Slack/Discord: [tu-canal]

---

## 📅 Changelog

### Versión 1.0 (15 Diciembre 2025)
- ✅ Implementación inicial completa
- ✅ CRUD de períodos
- ✅ Procesamiento asíncrono
- ✅ Cálculo de tardanzas, faltas y horas extra
- ✅ Exportación a Excel
- ✅ Documentación completa

### Próximas Versiones (Roadmap)
- 🔜 Notificaciones por email
- 🔜 Gráficos y dashboards
- 🔜 Aprobación multi-nivel
- 🔜 API REST
- 🔜 Integración con sistemas de nómina

---

## 🎓 Capacitación

### Para nuevos desarrolladores:

1. Lee [Quick Start](MODULO_PLANILLAS_QUICKSTART.md)
2. Revisa [Ejemplos](app/Examples/EjemploPlanillaUsage.php)
3. Ejecuta ejemplos en Tinker
4. Lee [Módulo Completo](documentacion/Desarrollador/Módulos/03_modulo_procesamiento_planillas.md)
5. Personaliza según necesidad

### Para usuarios de RRHH:

1. Lee [Quick Start](MODULO_PLANILLAS_QUICKSTART.md)
2. Practica con período de prueba
3. Solicita capacitación presencial (si disponible)

---

## 🏆 Créditos

**Módulo desarrollado por:** Sistema GobeBio  
**Fecha:** Diciembre 2025  
**Versión:** 1.0

---

**¿Tienes sugerencias para mejorar esta documentación?**  
Crea un issue en el repositorio o contacta al equipo de desarrollo.

**¡Gracias por usar el Módulo de Planillas de GobeBio!** 🚀
