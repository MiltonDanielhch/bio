# 📊 Diagrama de Flujo - Módulo de Planillas

## Flujo de Procesamiento Completo

```mermaid
graph TB
    Start([Usuario RRHH]) --> Create[Crear Período]
    Create --> |Define fechas| PeriodoAbierto[Estado: ABIERTO]
    
    PeriodoAbierto --> |Fin de mes| Cerrar[Click: Cerrar Período]
    Cerrar --> Dispatch[Despachar Job]
    Dispatch --> Procesando[Estado: PROCESANDO]
    
    Procesando --> Job[ProcesarPlanillaJob]
    Job --> Service[PlanillaService]
    
    Service --> GetEmpleados[Obtener Empleados Activos]
    GetEmpleados --> Loop{Por cada empleado}
    
    Loop --> ProcesarEmpleado[Procesar Empleado]
    
    ProcesarEmpleado --> IterarDias[Iterar por cada día del período]
    
    IterarDias --> CheckLaboral{¿Día laboral?}
    CheckLaboral -->|No| NextDay[Siguiente día]
    CheckLaboral -->|Sí| CheckIncidencia{¿Tiene incidencia?}
    
    CheckIncidencia -->|Sí| Incidencia[Contar como incidencia]
    CheckIncidencia -->|No| CheckMarcacion{¿Tiene marcaciones?}
    
    CheckMarcacion -->|No| Falta[Registrar FALTA]
    CheckMarcacion -->|Sí| CalcTardanza[Calcular Tardanza]
    
    CalcTardanza --> CheckTarde{¿Llegó tarde?}
    CheckTarde -->|Sí| RegTardanza[Registrar tardanza + minutos]
    CheckTarde -->|No| Puntual[Registrar puntual]
    
    RegTardanza --> CalcHoras[Calcular Horas Trabajadas]
    Puntual --> CalcHoras
    
    CalcHoras --> CheckExtra{¿Horas extra?}
    CheckExtra -->|Sí| ClasificarExtra[Clasificar<br/>25%, 50%, 100%]
    CheckExtra -->|No| NoExtra[Sin horas extra]
    
    ClasificarExtra --> NextDay
    NoExtra --> NextDay
    Falta --> NextDay
    Incidencia --> NextDay
    
    NextDay --> CheckMasDias{¿Más días?}
    CheckMasDias -->|Sí| IterarDias
    CheckMasDias -->|No| GuardarResumen[Guardar Resumen]
    
    GuardarResumen --> CheckMasEmpleados{¿Más empleados?}
    CheckMasEmpleados -->|Sí| Loop
    CheckMasEmpleados -->|No| Cerrado[Estado: CERRADO]
    
    Cerrado --> Ver[Ver Resultados]
    Ver --> Exportar[Exportar a Excel]
    
    Exportar --> Nomina[Importar en Sistema de Nómina]
    
    Nomina --> End([FIN])
    
    style Start fill:#90EE90
    style End fill:#FFB6C1
    style Cerrado fill:#87CEEB
    style Procesando fill:#FFD700
    style Falta fill:#FF6347
    style RegTardanza fill:#FFA500
    style ClasificarExtra fill:#9370DB
```

## Estados del Período

```mermaid
stateDiagram-v2
    [*] --> Abierto: Crear período
    
    Abierto --> Procesando: Cerrar
    Abierto --> [*]: Eliminar
    
    Procesando --> Cerrado: Éxito
    Procesando --> Error: Fallo
    
    Cerrado --> Abierto: Reabrir
    
    Error --> Procesando: Reintentar
    Error --> Abierto: Reabrir
    
    note right of Abierto
        - Puede editarse
        - Puede eliminarse
    end note
    
    note right of Procesando
        - Auto-refresco UI
        - No puede modificarse
    end note
    
    note right of Cerrado
        - Inmutable
        - Puede exportarse
        - Puede reabrirse
    end note
    
    note right of Error
        - Ver observaciones
        - Reintentar o reabrir
    end note
```

## Arquitectura del Sistema

```mermaid
graph LR
    User[Usuario RRHH] --> Controller[PeriodoPlanillaController]
    
    Controller --> |Despacha| Job[ProcesarPlanillaJob]
    Controller --> |Lee| ModelP[PeriodoPlanilla]
    Controller --> |Lee| ModelR[ResumenMensualAsistencia]
    
    Job --> Service[PlanillaService]
    
    Service --> |Lee| Empleado[(Empleado)]
    Service --> |Lee| RegistroAsist[(RegistroAsistencia)]
    Service --> |Lee| Horario[(Horario)]
    Service --> |Lee| Incidencia[(Incidencia)]
    
    Service --> |Escribe| ResumenDB[(resumen_mensual_asistencia)]
    
    ResumenDB --> Export[Exportación Excel]
    Export --> Nomina[Sistema Nómina]
    
    style User fill:#90EE90
    style Service fill:#FFD700
    style ResumenDB fill:#87CEEB
    style Nomina fill:#DDA0DD
```

## Relaciones de Base de Datos

```mermaid
erDiagram
    EMPRESAS ||--o{ PERIODOS_PLANILLA : "tiene"
    PERIODOS_PLANILLA ||--o{ RESUMEN_MENSUAL_ASISTENCIA : "contiene"
    EMPLEADOS ||--o{ RESUMEN_MENSUAL_ASISTENCIA : "tiene resumen en"
    
    PERIODOS_PLANILLA {
        int id PK
        int empresa_id FK
        string codigo UK
        date fecha_inicio
        date fecha_fin
        enum estado
        int cerrado_por FK
        timestamp fecha_cierre
    }
    
    RESUMEN_MENSUAL_ASISTENCIA {
        int id PK
        int periodo_id FK
        int empleado_id FK
        int total_dias_laborables
        int total_dias_trabajados
        int total_dias_falta
        int total_tardanzas
        int total_minutos_tardanza
        decimal total_horas_trabajadas
        decimal total_horas_extra_25
        decimal total_horas_extra_50
        decimal total_horas_extra_100
        json detalle_tardanzas_por_dia
        json detalle_faltas
    }
```

## Cálculo de Tardanzas (Lógica)

```mermaid
flowchart TD
    Start([Entrada Empleado]) --> GetHora[Obtener hora de entrada real]
    GetHora --> GetHorario[Obtener horario programado]
    GetHorario --> CalcLimite[Calcular límite:<br/>hora_prog + tolerancia]
    
    CalcLimite --> Compare{hora_real > límite?}
    
    Compare -->|NO| Puntual[✅ Puntual<br/>tardanza = 0 min]
    Compare -->|SÍ| Tarde[⚠️ Tardanza]
    
    Tarde --> CalcMin[Calcular minutos:<br/>hora_real - hora_prog]
    CalcMin --> RegMin[Registrar tardanza + minutos]
    
    Puntual --> End([Siguiente proceso])
    RegMin --> End
    
    style Puntual fill:#90EE90
    style Tarde fill:#FFA500
```

## Clasificación de Horas Extra

```mermaid
flowchart TD
    Start([Calcular Horas Extra]) --> CalcDiff[horas_extra = trabajadas - programadas]
    
    CalcDiff --> Check{horas_extra > 0?}
    
    Check -->|NO| NoExtra[Sin horas extra]
    Check -->|SÍ| CheckDia{¿Sáb/Dom?}
    
    CheckDia -->|SÍ| HE100[100% del total]
    CheckDia -->|NO| CheckCant{horas_extra <= 2?}
    
    CheckCant -->|SÍ| HE25[25% del total]
    CheckCant -->|NO| Split[Dividir:<br/>2hrs al 25%<br/>resto al 50%]
    
    NoExtra --> End([Guardar])
    HE100 --> End
    HE25 --> End
    Split --> End
    
    style HE25 fill:#FFD700
    style Split fill:#FFA500
    style HE100 fill:#FF6347
```

## Timeline de un Período Completo

```
Día 1              Día 15             Día 30             Día 31
│                  │                  │                  │
▼                  ▼                  ▼                  ▼
┌─────────────────────────────────────────────────────────┐
│ Período: Diciembre 2025                                 │
│ Estado: ABIERTO                                         │
└─────────────────────────────────────────────────────────┘
│                                                         │
│  [Empleados marcando asistencia diariamente]           │
│                                                         │
                                                          ▼
                                              ┌───────────────────┐
                                              │ Click: Cerrar     │
                                              │ Estado: PROCESANDO│
                                              └───────────────────┘
                                                          │
                                          [Job procesando... 2-5 min]
                                                          │
                                                          ▼
                                              ┌───────────────────┐
                                              │ Estado: CERRADO   │
                                              │ Resúmenes listos  │
                                              └───────────────────┘
                                                          │
                                                          ▼
                                              ┌───────────────────┐
                                              │ Exportar Excel    │
                                              │ → Sistema Nómina  │
                                              └───────────────────┘
```

---

## Leyenda de Colores

- 🟢 **Verde**: Inicio/Fin exitoso
- 🟡 **Amarillo**: En proceso/Advertencia
- 🔵 **Azul**: Estado normal
- 🟠 **Naranja**: Atención requerida
- 🔴 **Rojo**: Error/Problema

---

**Ver más:** [Documentación completa](documentacion/Desarrollador/Módulos/03_modulo_procesamiento_planillas.md)
