# 🎯 Módulo de Procesamiento y Cierre de Planillas - Quick Start

## ✅ ¿Qué hace este módulo?

Automatiza el proceso de cierre mensual de asistencia para cálculo de nómina, generando un resumen consolidado por empleado que incluye:

- ✔️ Tardanzas y minutos de retraso
- ✔️ Faltas injustificadas
- ✔️ Horas trabajadas vs programadas
- ✔️ Horas extra clasificadas (25%, 50%, 100%)
- ✔️ Porcentaje de asistencia
- ✔️ Exportación a Excel lista para planilla

---

## 🚀 Acceso Rápido

**URL:** `/admin/periodos-planilla`

**Rutas principales:**
- Listado: `GET /admin/periodos-planilla`
- Crear: `GET /admin/periodos-planilla/create`
- Ver detalle: `GET /admin/periodos-planilla/{id}`
- Cerrar: `POST /admin/periodos-planilla/{id}/cerrar`
- Exportar: `GET /admin/periodos-planilla/{id}/exportar`

---

## 📋 Flujo de Uso en 4 Pasos

### 1️⃣ Crear Período
1. Click en "Períodos de Planilla"
2. "Nuevo Período"
3. Selecciona empresa y fechas (ej: 01/12/2025 - 31/12/2025)
4. Guardar (estado: **Abierto**)

### 2️⃣ Cerrar Período
1. Click en "Cerrar Período"
2. El sistema procesa automáticamente en segundo plano
3. Estado cambia a **Procesando** → **Cerrado**

### 3️⃣ Revisar Resultados
1. Ver estadísticas generales
2. Tabla con todos los empleados
3. Click en empleado para ver detalle individual

### 4️⃣ Exportar
1. Click en "Exportar a Excel"
2. Descarga archivo listo para importar en sistema de nómina

---

## 📊 ¿Qué calcula automáticamente?

### Por cada empleado:
- **Días laborables:** Según horario asignado
- **Días trabajados:** Días con marcaciones
- **Faltas:** Días sin marcar ni incidencia
- **Tardanzas:** Llegadas tarde (con minutos exactos)
- **Horas trabajadas:** Total de horas en el período
- **Horas extra:** Clasificadas según normativa

### Ejemplo de salida:
```
Juan Pérez - Ventas
├─ 22 días laborables
├─ 21 días trabajados
├─ 1 falta
├─ 3 tardanzas (45 minutos total)
├─ 176.5 hrs trabajadas (176 programadas)
└─ 0.5 hrs extra al 25%
```

---

## 🎨 Funcionalidades Destacadas

### Auto-refresco
Mientras un período está procesando, la página se actualiza automáticamente cada 5-10 segundos.

### Indicadores Visuales
- 🟢 **Verde:** Empleados perfectos (100% asistencia, sin tardanzas)
- 🟡 **Amarillo:** Empleados con observaciones (tardanzas frecuentes o faltas)
- 🔴 **Rojo:** Empleados con problemas graves

### Detalle por Empleado
Click en cualquier empleado para ver:
- Desglose completo de tardanzas (fecha y hora)
- Lista de faltas
- Cálculo de horas extra detallado

---

## ⚙️ Estados del Período

| Estado | Descripción | Acciones Disponibles |
|--------|-------------|---------------------|
| 🔓 **Abierto** | Recién creado, sin procesar | Cerrar, Editar, Eliminar |
| ⏳ **Procesando** | Job ejecutándose | Solo ver (auto-refresco) |
| ✅ **Cerrado** | Procesado exitosamente | Ver, Exportar, Reabrir |
| ❌ **Error** | Falló el procesamiento | Ver observaciones, Reabrir |

---

## 🔧 Mantenimiento

### ¿El job no se ejecuta?
```bash
# Verificar que el queue worker esté corriendo
php artisan queue:work

# Ver logs
tail -f storage/logs/laravel.log
```

### ¿Quedó en "Procesando"?
```bash
# Re-intentar jobs fallidos
php artisan queue:retry all

# O reabrir manualmente desde la interfaz
```

### ¿Necesitas ajustar la lógica de horas extra?
Edita: `app/Services/PlanillaService.php` método `calcularHorasTrabajadas()`

---

## 📝 Notas Importantes

- ⚠️ **Reabrir un período elimina todos los resúmenes generados**
- 📌 Un empleado solo puede tener un resumen por período
- 🔒 Solo se pueden exportar períodos cerrados
- 💾 Los resúmenes son inmutables una vez cerrados (a menos que se reabra)

---

## 🎯 Próximos Pasos

Después de implementar este módulo, considera:

1. **Agregar al menú de Voyager** para acceso rápido
2. **Configurar permisos** (solo RRHH puede cerrar períodos)
3. **Personalizar cálculo de horas extra** según tu país
4. **Probar con datos reales** de un mes completo
5. **Capacitar al equipo de RRHH** en el uso del módulo

---

## 📚 Documentación Completa

Para información técnica detallada, consulta:
`documentacion/Desarrollador/Módulos/03_modulo_procesamiento_planillas.md`

---

**Versión:** 1.0  
**Última actualización:** Diciembre 2025
