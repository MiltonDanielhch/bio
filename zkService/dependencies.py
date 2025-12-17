from fastapi import Header, HTTPException, Depends
from config import settings

async def validate_api_key(x_api_key: str = Header(..., description="Clave de API para autenticar la petición.")):
    if x_api_key != settings.API_KEY:
        import logging
        msg = f"AUTH FAIL: Expected '{settings.API_KEY}' (len={len(settings.API_KEY)}), Got '{x_api_key}' (len={len(x_api_key)})"
        logging.getLogger("uvicorn.error").error(msg)
        raise HTTPException(status_code=401, detail=msg)
    return x_api_key