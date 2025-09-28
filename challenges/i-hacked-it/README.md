# I Hacked It

| Category | Difficulty | Point |
| :-: | :-: | :-: |
| Network | Normal | 200 |

คุณคือ hacker ที่ได้ทำการ hack server ดังกล่าวได้สำเร็จ คุณถึงขึ้นตอนสุดท้ายของงานนี้แล้ว ซึ่งก็คือการขโมยข้อมูล

Format: `CSUBRU{...}`

---

## Hints

- ความลับของการ deploy

---

## Configuration

| Name | Value |
| :- | :- |
| Title | I Hacked It |
| Type | Dynamic Container |
| Category | Misc |
| Max Attempts Allowed | unlimited |
| Blood Bonus | Disable |
| Container Image | ic-i-hacked-it |
| Service Port | 1337 |
| CPU Limit (0.1 CPUs) | 1 |
| Memory Limit (MB) | 64 |
| Storage Limit (MB) | 128 |

### Flags

- `CSUBRU{[GUID]}`

---

## Analysis

อยู่ใน env ของ process sh server.sh
