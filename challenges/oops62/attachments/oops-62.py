from string import digits, ascii_uppercase, ascii_lowercase

BASE62_ALPHABET = digits + ascii_uppercase + ascii_lowercase

def base62_encode(num):
    if num == 0:
        return BASE62_ALPHABET[0]
    result = []
    while num > 0:
        num, rem = divmod(num, 62)
        result.append(BASE62_ALPHABET[rem])
    return "".join(reversed(result))

if __name__ == "__main__":
    n = int.from_bytes(b"CSUBRU{RESTRICTED}", "big")
    encoded = base62_encode(n)
    code = encoded.lower()
    print(code) # 3p8igvgqkh0udsbywn48dtviftqgdbgciuhw4b
