# Weak Vault

| Category | Difficulty | Point |
| :-: | :-: | :-: |
| Reverse | Easy | 100 |

ห้องนิรภัยนี้ค่อนข้างอ่อนแอมาก จนใครๆก็สามารถเปิดมันได้ง่ายๆ

Format: `CSUBRU{MD5}`

---

## Configuration

| Name | Value |
| :- | :- |
| Title | Weak Vault |
| Type | Static Attachments |
| Category | Reverse |
| Max Attempts Allowed | unlimited |
| Blood Bonus | Enable |

[Configuration]

### Attachments

- `weak-vault.zip`

### Flags

- `CSUBRU{e70f38465a0cb5e2fb53cce1edeb283c}`

### Build

```sh
docker build -t cb-weak-vault .
docker run --rm cb-weak-vault cat /build/weak-vault.zip > ../attachments/weak-vault.zip
```

---

## Analysis

ใช้ ghidra reverse แล้วจะเจอ pin

ง่ายกว่านั้นคือ strings แล้วจะพบเลข 6 ตัว (999999)
