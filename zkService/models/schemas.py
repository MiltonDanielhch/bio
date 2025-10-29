from pydantic import BaseModel, Field
from typing import List, Optional
from datetime import datetime

class HealthCheck(BaseModel):
    """
    Modelo de respuesta para el endpoint de verificación de estado.
    """
    status: str = "ok"

class User(BaseModel):
    """
    Representa un usuario en el dispositivo biométrico.
    """
    uid: int
    user_id: str
    name: str
    privilege: str
    password: str = ""
    group_id: str = ""
    card: int = 0

class AttendanceRecord(BaseModel):
    """
    Representa un único registro de asistencia (marcación).
    """
    uid: int
    user_id: str
    timestamp: datetime
    status: int
    punch: int

class AttendanceResponse(BaseModel):
    """
    Modelo de respuesta para el endpoint de registros de asistencia.
    """
    device_ip: str
    records_count: int
    records: List[AttendanceRecord]

class DeviceInfo(BaseModel):
    """
    Representa la información de hardware y firmware de un dispositivo.
    """
    firmware_version: str
    device_name: str
    serial_number: str
    mac_address: str
    platform: str
    device_time: str

class StatusReport(BaseModel):
    status: str
    info: Optional[DeviceInfo] = None
    error: Optional[str] = None
    last_checked: str