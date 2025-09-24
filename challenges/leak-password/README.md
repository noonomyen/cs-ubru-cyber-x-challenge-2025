# Leak Password

| Category | Difficulty | Point |
| :-: | :-: | :-: |
| Forensics | Normal | 200 |

สมควร เขาใช้ linux เป็น native บนเครื่อง laptop ของเขา และเขาก็ได้ลองเล่นระบบ ssh แต่เขาดันลืมปิด และรหัส login เขานั้นก็ง่ายมาก พอไป connect เน็ตที่ ม เขาโดน Attacker scan เจอว่าเปิด port 22 ไว้ และ Attacker ก็ได้ Brute Force จนเข้าระบบได้ เมื่อเขารู้ตัวเขาก็รีบเปลี่ยนรหัสทันที แต่พอไปที่ ม ก็รู้สึกแปลกๆ เหมือน remote มา ทั้งทีเปลี่ยนรหัสแล้ว?


Format: `CSUBRU{...}`

---

## Hints

- no hint

---

## Configuration

| Name | Value |
| :- | :- |
| Title | Leak |
| Type | Dynamic Container |
| Category | Forensics |
| Max Attempts Allowed |  |
| Blood Bonus | Enable |
| Container Image | ic-leak-password |
| Service Port | 80 |
| CPU Limit (0.1 CPUs) |  |
| Memory Limit (MB) |  |
| Storage Limit (MB) |  |

[Configuration]

### Attachments

- `authLog.zip`

### Flags

- `CSUBRU{plz_s37up_ur_fir3w4ll_[TEAM_HASH]}`

---

## Analysis

```
เข้าเว็บ และตอบทีละคำถาม เพื่อให้ได้ Flag
1. 192.168.35.1
2. 192.168.35.2
3. 22
4. jordan23
5. whiterabbit
```
