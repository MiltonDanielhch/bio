from fastapi import FastAPI, Request
import logging
import sys
from contextlib import asynccontextmanager
from routers import devices, health, ws
from background.tasks import start_background_tasks
from services.zk_service import cleanup_devices
import asyncio

# Configurar logging para que salga por consola inmediatamente
logging.basicConfig(
    stream=sys.stdout,
    level=logging.INFO,
    format='%(asctime)s - %(name)s - %(levelname)s - %(message)s'
)
logger = logging.getLogger(__name__)

@asynccontextmanager
async def lifespan(app: FastAPI):
    """
    Gestiona el ciclo de vida de la aplicación. Inicia tareas en segundo plano
    al arrancar y las limpia al apagar.
    """
    logger.info("Iniciando tareas en segundo plano...")
    
    # DEBUG: Imprimir configuración cargada
    from config import settings
    logger.info(f"--- DEBUG CONFIGURATION ---")
    logger.info(f"API_KEY (len={len(settings.API_KEY)}): {settings.API_KEY}")
    logger.info(f"KNOWN_DEVICES: {settings.KNOWN_DEVICES}")
    logger.info(f"---------------------------")

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

# --- DEBUG MIDDLEWARE: Ver qué headers llegan realmente ---
@app.middleware("http")
async def debug_headers(request: Request, call_next):
    logger.info(f"--- SOLICITUD ENTRANTE: {request.method} {request.url} ---")
    # Imprimir el valor exacto de la API KEY recibida (entre comillas para ver espacios)
    received_key = request.headers.get("x-api-key")
    
    # Usamos print con flush=True para asegurar que salga en Docker pase lo que pase
    print(f"--- DEBUG FORCE: Header 'x-api-key' recibido: '{received_key}' ---", flush=True)
    logger.info(f"Header 'x-api-key' recibido: '{received_key}'")
    
    response = await call_next(request)
    logger.info(f"Respuesta enviada: {response.status_code}")
    return response

# Registrar routers
app.include_router(devices.router)
app.include_router(health.router)
app.include_router(ws.router)
