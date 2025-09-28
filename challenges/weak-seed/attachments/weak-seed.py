from Crypto.Cipher import AES
from hashlib import sha256
from time import time

def encrypt(msg: str, seed: int) -> bytes:
    key = sha256(str(seed).encode()).digest()
    cipher = AES.new(key, AES.MODE_CTR)
    ciphertext = cipher.encrypt(msg.encode())
    return cipher.nonce + ciphertext

msg = "???"
seed = int(time())

enc_bytes = encrypt(msg, seed)
print(enc_bytes.hex())
# 8e72cd601953bf5df5de82ce5f4db5386467a5672f9885be05a2896aec75908b0158e2
