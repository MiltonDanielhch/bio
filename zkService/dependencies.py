from fastapi import Header, HTTPException, Depends
from config import settings

async def validate_api_key(x_api_key: str = Header(..., description="Clave de API para autenticar la petición.")):
    if x_api_key != settings.API_KEY:
        raise HTTPException(status_code=401, detail="Clave de API inválida o ausente")
    return x_api_key