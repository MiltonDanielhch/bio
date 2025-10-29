import asyncio
import time
import logging
from services.zk_service import DeviceConnection
from config import settings
from fastapi import HTTPException
from datetime import datetime

logger = logging.getLogger(__name__)

# Diccionario en memoria para almacenar el estado de los dispositivos
device_status = {}

async def check_device(ip: str):
    """
    Verifica el estado de un único dispositivo y actualiza el diccionario de estado.
    Esta corutina está diseñada para ejecutarse en paralelo para cada dispositivo.
    """
    try:
        # Usamos el gestor de contexto asíncrono que ya maneja la conexión/desconexión
        async with DeviceConnection(ip, port=settings.DEVICE_PORT, password=settings.DEVICE_PASSWORD) as device:
            info = await device.get_device_info()
            status_report = {
                "status": "online",
                "info": info.model_dump(),
                "last_checked": datetime.fromtimestamp(time.time()).isoformat()
            }
    except HTTPException as http_exc:
        # Errores de conexión o del dispositivo capturados por el servicio
        status_report = {
            "status": "offline",
            "error": http_exc.detail,
            "last_checked": datetime.fromtimestamp(time.time()).isoformat()
        }
    except Exception as e:
        # Otros errores inesperados
        logger.error(f"Error inesperado al chequear el dispositivo {ip}: {e}")
        status_report = {
            "status": "offline",
            "error": f"Error inesperado: {str(e)}",
            "last_checked": datetime.fromtimestamp(time.time()).isoformat()
        }
    
    device_status[ip] = status_report

async def monitor_devices():
    logger.info(f"Iniciando monitoreo de dispositivos: {settings.KNOWN_DEVICES}")
    while True:
        # Crea y ejecuta una tarea de chequeo para cada IP en paralelo
        await asyncio.gather(*(check_device(ip) for ip in settings.KNOWN_DEVICES if ip))
        await asyncio.sleep(settings.DEVICE_CHECK_INTERVAL)

async def start_background_tasks():
    asyncio.create_task(monitor_devices())