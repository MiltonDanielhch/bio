from fastapi import FastAPI
from pydantic import BaseModel

# Modelo de respuesta para el endpoint de salud
class HealthCheck(BaseModel):
    status: str

# Inicializar la aplicación FastAPI
app = FastAPI(
    title="ZK Biometric Service",
    description="Microservicio para la comunicación con relojes biométricos ZKTeco.",
    version="1.0.0",
)

@app.get("/health", response_model=HealthCheck, tags=["Health"])
async def health_check():
    """Verifica que el servicio esté funcionando correctamente."""
    return {"status": "ok"}