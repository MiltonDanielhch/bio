# app/services/zk_service.py
from zk import ZK, const
from zk.exception import ZKError
import asyncio
from fastapi import HTTPException
from models.schemas import AttendanceRecord, User, DeviceInfo, AttendanceResponse
from config import settings # Usar la configuración centralizada
from datetime import datetime
import logging
from typing import List, Callable, Dict, Any, Optional
import socket

logger = logging.getLogger(__name__)

# Manejo de conexiones activas para limpieza
active_connections = []

class DeviceConnection:
    def __init__(self, ip: str, port: int, password: Optional[int] = 0):
        """Inicializa la conexión con el dispositivo"""
        self.ip = ip
        self.port = port
        self.password = password
        self.zk = ZK(
            self.ip,
            port=self.port,
            timeout=settings.DEVICE_TIMEOUT,
            password=self.password or 0,
            ommit_ping=False,
            verbose=settings.DEBUG
        )
        self.conn = None
        logger.debug(f"DeviceConnection creado para {self.ip}:{self.port}")

    async def __aenter__(self):
        """Establece conexión al entrar en el contexto `async with`."""
        logger.info(f"Conectando a dispositivo {self.ip}...")
        try:
            # Usamos to_thread para no bloquear el event loop con la E/S de red síncrona
            self.conn = await asyncio.to_thread(self.zk.connect)
            if not self.conn:
                 raise ConnectionError("La conexión devolvió un objeto nulo.")
            logger.info(f"Conexión exitosa a {self.ip}. Deshabilitando dispositivo.")
            await asyncio.to_thread(self.conn.disable_device)
            active_connections.append(self)
            return self
        except (ZKError, socket.error, socket.timeout) as e:
            logger.error(f"Error de conexión en {self.ip}: {e}")
            raise HTTPException(status_code=404, detail=f"Dispositivo no encontrado o no se pudo conectar: {e}")
        except Exception as e:
            logger.exception(f"Error inesperado al conectar con {self.ip}: {e}")
            raise HTTPException(status_code=500, detail=f"Error inesperado en dispositivo: {e}")

    async def __aexit__(self, exc_type, exc_val, exc_tb):
        """Cierra la conexión al salir del contexto `async with`."""
        if self.conn:
            try:
                logger.info(f"Habilitando y desconectando de {self.ip}")
                await asyncio.to_thread(self.conn.enable_device)
                await asyncio.to_thread(self.conn.disconnect)
            except Exception as e:
                logger.warning(f"Error al desconectar de {self.ip}: {e}")
            finally:
                self.conn = None
                if self in active_connections:
                    active_connections.remove(self)

    async def get_users(self) -> List[User]:
        """Obtiene todos los usuarios del dispositivo."""
        if not self.conn:
            raise ConnectionError("No hay una conexión activa.")
        
        logger.info(f"Obteniendo usuarios de {self.ip}...")
        users_pyzk = await asyncio.to_thread(self.conn.get_users)
        return [
            User(
                uid=user.uid,
                user_id=user.user_id,
                name=user.name,
                # Mapear manualmente el privilegio, ya que USER_PRIVILEGE_MAP no existe
                privilege='Admin' if user.privilege == const.USER_ADMIN else 'User',
                password=user.password,
                group_id=user.group_id,
                card=user.card
            )
            for user in users_pyzk
        ]

    async def get_attendance(self) -> List[AttendanceRecord]:
        """Obtiene registros de asistencia."""
        if not self.conn:
            raise ConnectionError("No hay una conexión activa.")

        logger.info(f"Obteniendo registros de asistencia de {self.ip}...")
        attendance_pyzk = await asyncio.to_thread(self.conn.get_attendance)
        
        if not attendance_pyzk:
            return []

        # Convertir manualmente cada objeto de la librería a un diccionario
        # que nuestro modelo Pydantic pueda validar.
        return [
            AttendanceRecord(
                uid=att.uid,
                user_id=att.user_id,
                timestamp=att.timestamp,
                status=att.status,
                punch=att.punch
            ) for att in attendance_pyzk
        ]

    async def get_device_info(self) -> DeviceInfo:
        """Obtiene información detallada del dispositivo."""
        if not self.conn:
            raise ConnectionError("No hay una conexión activa.")
    
        logger.info(f"Obteniendo información de {self.ip}...")
        
        # Ejecutar secuencialmente para mayor estabilidad y usar la sintaxis moderna
        firmware_version = await asyncio.to_thread(self.conn.get_firmware_version)
        device_name = await asyncio.to_thread(self.conn.get_device_name)
        serial_number = await asyncio.to_thread(self.conn.get_serialnumber)
        mac = await asyncio.to_thread(self.conn.get_mac)
        platform = await asyncio.to_thread(self.conn.get_platform)
        device_time = await asyncio.to_thread(self.conn.get_time)

        return DeviceInfo(
            firmware_version=firmware_version,
            device_name=device_name,
            serial_number=serial_number,
            mac_address=mac,
            platform=platform,
            device_time=str(device_time)
        )

    async def test_voice(self, index: int = 0) -> None:
        """Reproduce un mensaje de voz (0: 'Thank You')."""
        if not self.conn:
            raise ConnectionError("No hay una conexión activa.")
        
        logger.info(f"Probando voz en {self.ip}...")
        await asyncio.to_thread(self.conn.test_voice, index)

    async def clear_attendance(self) -> None:
        """Borra todos los registros de asistencia del dispositivo."""
        if not self.conn:
            raise ConnectionError("No hay una conexión activa.")
        
        logger.info(f"Borrando registros de asistencia de {self.ip}...")
        await asyncio.to_thread(self.conn.clear_attendance)
        logger.info(f"Registros de asistencia borrados en {self.ip}.")


async def get_attendance_from_device(ip: str, port: int, password: Optional[int] = 0) -> AttendanceResponse:
    """
    Función de servicio para obtener registros de asistencia de un dispositivo.
    Utiliza el gestor de contexto `DeviceConnection`.
    """
    try:
        async with DeviceConnection(ip, port, password) as device:
            records = await device.get_attendance()
            return AttendanceResponse(
                device_ip=ip,
                records_count=len(records),
                records=records
            )
    except HTTPException as http_exc:
        # Re-lanzar excepciones HTTP para que FastAPI las maneje
        raise http_exc
    except Exception as e:
        # Capturar cualquier otro error y devolver una respuesta de error 500
        logger.exception(f"Fallo crítico en el servicio de asistencia para {ip}: {e}")
        raise HTTPException(status_code=500, detail=f"Error interno del servicio: {e}")


async def get_users_from_device(ip: str, port: int, password: Optional[int] = 0) -> List[User]:
    """
    Función de servicio para obtener los usuarios de un dispositivo.
    """
    try:
        async with DeviceConnection(ip, port, password) as device:
            return await device.get_users()
    except HTTPException as http_exc:
        raise http_exc
    except Exception as e:
        logger.exception(f"Fallo crítico en el servicio de usuarios para {ip}: {e}")
        raise HTTPException(status_code=500, detail=f"Error interno del servicio: {e}")


async def get_info_from_device(ip: str, port: int, password: Optional[int] = 0) -> DeviceInfo:
    """
    Función de servicio para obtener la información de un dispositivo.
    """
    try:
        async with DeviceConnection(ip, port, password) as device:
            return await device.get_device_info()
    except HTTPException as http_exc:
        raise http_exc
    except Exception as e:
        logger.exception(f"Fallo crítico en el servicio de información para {ip}: {e}")
        raise HTTPException(status_code=500, detail=f"Error interno del servicio: {e}")


async def test_voice_on_device(ip: str, port: int, password: Optional[int] = 0):
    """
    Función de servicio para probar la voz en un dispositivo.
    """
    try:
        async with DeviceConnection(ip, port, password) as device:
            await device.test_voice()
            return {"status": "ok", "message": "Comando de voz enviado."}
    except HTTPException as http_exc:
        raise http_exc
    except Exception as e:
        logger.exception(f"Fallo crítico en el servicio de prueba de voz para {ip}: {e}")
        raise HTTPException(status_code=500, detail=f"Error interno del servicio: {e}")


async def clear_attendance_from_device(ip: str, port: Optional[int] = None, password: Optional[int] = None):
    """
    Función de servicio para borrar los registros de asistencia de un dispositivo.
    """
    try:
        async with DeviceConnection(ip, port, password) as device:
            await device.clear_attendance()
            return {"status": "ok", "message": f"Registros de asistencia borrados en el dispositivo {ip}."}
    except HTTPException as http_exc:
        raise http_exc
    except Exception as e:
        logger.exception(f"Fallo crítico al borrar asistencia para {ip}: {e}")
        raise HTTPException(status_code=500, detail=f"Error interno del servicio: {e}")

async def cleanup_devices():
    """Cierra todas las conexiones activas que puedan haber quedado abiertas."""
    logger.info(f"Iniciando limpieza de {len(active_connections)} conexiones activas...")
    # Copiamos la lista para poder modificarla mientras iteramos
    for device in active_connections[:]:
        try:
            if device.conn:
                await asyncio.to_thread(device.conn.disconnect)
                logger.info(f"Conexión forzada cerrada para: {device.ip}")
        except Exception as e:
            logger.error(f"Error cerrando conexión {device.ip} durante la limpieza: {e}")
        finally:
            if device in active_connections:
                active_connections.remove(device)
    logger.info("Limpieza de conexiones de dispositivos completada.")