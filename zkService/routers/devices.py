from fastapi import APIRouter, Path, Query, Depends
from typing import List, Optional, Dict, Any
from models.schemas import AttendanceResponse, User, DeviceInfo, UserSyncRequest
from services import zk_service
from background.tasks import device_status
from dependencies import validate_api_key

router = APIRouter(
    prefix="/devices",
    tags=["Devices"],
    dependencies=[Depends(validate_api_key)]
)

@router.get("/status",
            response_model=Dict[str, Any],
            summary="Obtener el estado en tiempo real de los dispositivos monitoreados")
async def get_devices_status():
    return device_status

@router.get("/{ip}/attendance",
            response_model=AttendanceResponse,
            summary="Obtener registros de asistencia de un dispositivo",
            description="Conecta con un dispositivo ZKTeco por su IP, descarga todos los registros de asistencia y los devuelve.")
async def get_attendance(
    ip: str = Path(..., title="Dirección IP del dispositivo", regex=r"^\d{1,3}(\.\d{1,3}){3}$"),
    port: int = Query(4370, title="Puerto del dispositivo"),
    password: Optional[int] = Query(0, title="Clave de comunicación del dispositivo")
):
    return await zk_service.get_attendance_from_device(ip, port, password)

@router.get("/{ip}/users",
            response_model=List[User],
            summary="Obtener la lista de usuarios de un dispositivo")
async def get_users(
    ip: str = Path(..., title="Dirección IP del dispositivo", regex=r"^\d{1,3}(\.\d{1,3}){3}$"),
    port: int = Query(4370, title="Puerto del dispositivo"),
    password: Optional[int] = Query(0, title="Clave de comunicación del dispositivo")
):
    return await zk_service.get_users_from_device(ip, port, password)

@router.get("/{ip}/info",
            response_model=DeviceInfo,
            summary="Obtener información de un dispositivo")
async def get_device_info(
    ip: str = Path(..., title="Dirección IP del dispositivo", regex=r"^\d{1,3}(\.\d{1,3}){3}$"),
    port: int = Query(4370, title="Puerto del dispositivo"),
    password: Optional[int] = Query(0, title="Clave de comunicación del dispositivo")
):
    return await zk_service.get_info_from_device(ip, port, password)

@router.post("/{ip}/test-voice", summary="Probar mensaje de voz en el dispositivo")
async def test_voice(
    ip: str = Path(..., title="Dirección IP del dispositivo", regex=r"^\d{1,3}(\.\d{1,3}){3}$"),
    port: int = Query(4370, title="Puerto del dispositivo"),
    password: Optional[int] = Query(0, title="Clave de comunicación del dispositivo")
):
    return await zk_service.test_voice_on_device(ip, port, password)

@router.delete("/{ip}/attendance",
             status_code=204,
             summary="Borrar todos los registros de asistencia de un dispositivo")
async def clear_attendance(
    ip: str = Path(..., title="Dirección IP del dispositivo", regex=r"^\d{1,3}(\.\d{1,3}){3}$"),
    port: int = Query(4370, title="Puerto del dispositivo"),
    password: Optional[int] = Query(0, title="Clave de comunicación del dispositivo")
):
    return await zk_service.clear_attendance_from_device(ip, port, password)

@router.post("/{ip}/sync-users",
             summary="Sincroniza (borra y sube) una lista de usuarios a un dispositivo",
             description="Este endpoint borra TODOS los usuarios existentes en el dispositivo y sube la nueva lista proporcionada.")
async def sync_users(
    request: UserSyncRequest,
    ip: str = Path(..., title="Dirección IP del dispositivo", regex=r"^\d{1,3}(\.\d{1,3}){3}$"),
    port: int = Query(4370, title="Puerto del dispositivo"),
    password: Optional[int] = Query(0, title="Clave de comunicación del dispositivo")
):
    return await zk_service.sync_users_to_device(ip, port, password, request.users)