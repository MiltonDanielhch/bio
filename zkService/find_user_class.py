import zk
from zk import ZK, const
print("zk dir:", dir(zk))
try:
    print("zk.User:", zk.User)
except:
    print("zk.User not found")

try:
    from zk.user import User
    print("zk.user.User found")
except:
    print("zk.user.User not found")
