import sys
import os
from dotenv import load_dotenv

def main():
    # Intentar importar requests, que es una nueva dependencia para este script
    try:
        import requests
    except ImportError:
        print("¡ERROR! Falta la librería 'requests'. Por favor, ejecuta 'pip install -r requirements.txt'")
        sys.exit(1)

    """
    Script de diagnóstico para verificar el entorno de Python, la instalación de pyzk
    y la conectividad básica con los dispositivos ZKTeco configurados.
    """
    print("=" * 50)
    print("Prueba de Entorno y Conectividad de zkService")
    print("=" * 50)

    # --- 1. Verificación del Entorno de Python ---
    print("\n[1] Verificando el entorno de Python...")
    print(f"    - Ejecutable de Python: {sys.executable}")
    print(f"    - Versión de Python: {sys.version.split()[0]}")

    # --- 2. Verificación de la Librería pyzk ---
    print("\n[2] Verificando la librería 'pyzk'...")
    try:
        from zk import ZK, const
        from zk.exception import ZKErrorResponse
        print("    - ¡Éxito! El módulo 'zk' se importó correctamente.")
    except ImportError:
        print("    - ¡ERROR! No se pudo importar el módulo 'zk'.")
        print("      Asegúrate de haber ejecutado 'pip install -r requirements.txt' en este entorno.")
        sys.exit(1) # Salir si la librería principal no está

    # --- 3. Carga de la Configuración ---
    print("\n[3] Cargando configuración desde el archivo .env...")
    load_dotenv()
    device_ips_str = os.getenv("KNOWN_DEVICES", "")
    device_password = int(os.getenv("DEVICE_PASSWORD", 0))
    api_key = os.getenv("API_KEY", "")
    
    if not device_ips_str:
        print("    - ¡ADVERTENCIA! La variable KNOWN_DEVICES no está definida en el archivo .env.")
        sys.exit(1)

    device_ips = [ip.strip() for ip in device_ips_str.split(',') if ip.strip()]
    print(f"    - Dispositivos a probar: {device_ips}")
    print(f"    - Contraseña a usar: {'*' * len(str(device_password)) if device_password else '(ninguna)'}")

    # --- 4. Prueba de Conexión a Dispositivos ---
    print("\n[4] Probando conexión con los dispositivos...")
    for ip in device_ips:
        print(f"    - Conectando a {ip}...")
        try:
            zk = ZK(ip, port=4370, timeout=5, password=device_password)
            conn = zk.connect()
            print(f"      - ¡Éxito! Conectado a '{conn.get_device_name()}' (SN: {conn.get_serialnumber()}).")
            conn.disconnect()
        except ZKErrorResponse as e:
            print(f"      - ¡ERROR DE CONEXIÓN! El dispositivo respondió: '{e}'. Verifica la contraseña.")
        except Exception as e:
            print(f"      - ¡ERROR DE CONEXIÓN! No se pudo conectar: {e}. Verifica la IP y la red.")

    # --- 5. Prueba de la API de zkService ---
    print("\n[5] Probando API de zkService (debe estar ejecutándose en localhost:8001)...")
    SERVICE_URL = "http://127.0.0.1:8001"
    
    # Prueba del endpoint /health
    try:
        response = requests.get(f"{SERVICE_URL}/health", timeout=5)
        if response.status_code == 200 and response.json().get("status") == "ok":
            print("    - ¡Éxito! El endpoint /health respondió correctamente.")
        else:
            print(f"    - ¡ERROR! El endpoint /health respondió con estado {response.status_code}.")
    except requests.ConnectionError:
        print("    - ¡ERROR! No se pudo conectar al servicio. Asegúrate de que esté corriendo en otra terminal.")
        sys.exit(1)

    # Prueba del endpoint /devices/status
    try:
        headers = {"x-api-key": api_key}
        response = requests.get(f"{SERVICE_URL}/devices/status", headers=headers, timeout=10)
        if response.status_code == 200:
            print("    - ¡Éxito! El endpoint /devices/status respondió correctamente (API Key válida).")
        elif response.status_code == 401:
            print("    - ¡ERROR! El endpoint /devices/status devolvió 401 Unauthorized. La API_KEY es incorrecta.")
        else:
            print(f"    - ¡ERROR! El endpoint /devices/status respondió con estado {response.status_code}.")
    except requests.RequestException as e:
        print(f"    - ¡ERROR! Ocurrió un error al llamar a /devices/status: {e}")

if __name__ == "__main__":
    main()
    input("\nPresiona Enter para salir...")