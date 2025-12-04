from zk.user import User as ZKUser
from zk import const
import inspect

print("=== ZKUser signature ===")
print(inspect.signature(ZKUser.__init__))
print("\n=== ZKUser attributes ===")
u = ZKUser(uid=1, name="Test", privilege=0, password="", user_id="1")
print(f"uid: {u.uid} (type: {type(u.uid)})")
print(f"name: {u.name} (type: {type(u.name)})")
print(f"privilege: {u.privilege} (type: {type(u.privilege)})")
print(f"password: {u.password} (type: {type(u.password)})")
print(f"user_id: {u.user_id} (type: {type(u.user_id)})")
print(f"group_id: {u.group_id} (type: {type(u.group_id)})")
print(f"card: {u.card} (type: {type(u.card)})")

print("\n=== Privilege constants ===")
print(f"USER_DEFAULT: {const.USER_DEFAULT}")
print(f"USER_ADMIN: {const.USER_ADMIN}")
