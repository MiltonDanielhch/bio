import os
from pydantic_settings import BaseSettings, SettingsConfigDict
from typing import List, Optional, Union
from dotenv import load_dotenv

# Carga las variables de entorno desde un archivo .env si existe
load_dotenv()

class Settings(BaseSettings):
    """
    Configuraciones de la aplicación, leídas desde variables de entorno.
    Pydantic-settings se encarga de la carga, conversión de tipos y validación.
    """
    # --- Configuración de la API y Servicio ---
    API_KEY: str = "un-token-secreto-muy-seguro"
    LOG_LEVEL: str = "INFO"
    DEBUG: bool = False
    MAX_WORKERS: int = 10

    # --- Configuración de Dispositivos ZKTeco ---
    DEVICE_PORT: int = 4370
    DEVICE_TIMEOUT: int = 5
    DEVICE_PASSWORD: Optional[int] = 0 # La contraseña de comunicación, si existe
    RECONNECT_ATTEMPTS: int = 3

    # --- Configuración de Tareas en Segundo Plano ---
    # Lista de IPs de dispositivos a monitorear, separadas por comas.
    # Ejemplo en .env: KNOWN_DEVICES=192.168.1.201,192.168.1.202
    KNOWN_DEVICES: Union[List[str], str] = "192.168.1.201"

    # Intervalo en segundos para verificar el estado de los dispositivos.
    DEVICE_CHECK_INTERVAL: int = 60
    
    model_config = SettingsConfigDict(
        env_file=".env",
        env_file_encoding='utf-8',
        env_nested_delimiter='__',
    )

settings = Settings()

# Asegurarse de que KNOWN_DEVICES sea siempre una lista
if isinstance(settings.KNOWN_DEVICES, str):
    settings.KNOWN_DEVICES = [ip.strip() for ip in settings.KNOWN_DEVICES.split(',') if ip.strip()]