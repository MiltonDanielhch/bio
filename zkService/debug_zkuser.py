from zk import ZK
from zk.user import User as ZKUser
from zk import const
import inspect

# Ver signatura del método set_user
print("=== set_user signature ===")
print(inspect.signature(ZK.set_user))

# Crear usuario de prueba
u = ZKUser(uid=101, name="Test User", privilege=0, password="", user_id="101", group_id="", card=0)
print(f"\n=== ZKUser test object ===")
print(f"uid: {u.uid} (type: {type(u.uid).__name__})")
print(f"privilege: {u.privilege} (type: {type(u.privilege).__name__})")
print(f"card: {u.card} (type: {type(u.card).__name__})")
print(f"group_id: '{u.group_id}' (type: {type(u.group_id).__name__})")
print(f"user_id: '{u.user_id}' (type: {type(u.user_id).__name__})")
print(f"password: '{u.password}' (type: {type(u.password).__name__})")
print(f"name: '{u.name}' (type: {type(u.name).__name__})")

print(f"\n=== Full object ===")
print(vars(u))
