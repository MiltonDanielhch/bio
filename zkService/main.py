from fastapi import FastAPI
import logging
from contextlib import asynccontextmanager
from routers import devices, health, ws
from background.tasks import start_background_tasks
from services.zk_service import cleanup_devices
import asyncio

logger = logging.getLogger(__name__)

@asynccontextmanager
async def lifespan(app: FastAPI):
    """
    Gestiona el ciclo de vida de la aplicación. Inicia tareas en segundo plano
    al arrancar y las limpia al apagar.
    """
    logger.info("Iniciando tareas en segundo plano...")
    background_task = asyncio.create_task(start_background_tasks())
    
    yield # La aplicación se ejecuta aquí
    
    logger.info("Deteniendo servicio y liberando recursos...")
    background_task.cancel() # Cancelar la tarea de monitoreo
    await cleanup_devices()
    logger.info("Recursos liberados.")

app = FastAPI(
    title="ZK Biometric Service",
    description="Microservicio para la comunicación con relojes biométricos ZKTeco.",
    version="1.0.0",
    docs_url="/docs",
    redoc_url="/redoc",
    lifespan=lifespan
)

# Registrar routers
app.include_router(devices.router)
app.include_router(health.router)
app.include_router(ws.router)
