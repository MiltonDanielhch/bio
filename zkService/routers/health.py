from fastapi import APIRouter
from models.schemas import HealthCheck

router = APIRouter()

@router.get("/health", response_model=HealthCheck, tags=["Health"])
async def health_check():
    return {"status": "ok"}