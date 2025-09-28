# Man In The Middle

| Category | Difficulty | Point |
| :-: | :-: | :-: |
| Network | Normal | 200 |

หา username password ที่มี role admin

Format: `CSUBRU{MD5(username:password)}`

---

## Hints

- hint: ตัวอย่างคำตอบ admin:admin1234 แล้วให้ทำการ MD5 แล้วตอบใน CSUBRU{...}

---

## Configuration

| Name | Value |
| :- | :- |
| Title | Man In The Middle |
| Type | Static Attachments |
| Category | Forensics |
| Max Attempts Allowed | Unlimited |
| Blood Bonus | Enable |
| Container Image | N/A |
| Service Port | N/A |
| CPU Limit (0.1 CPUs) | N/A |
| Memory Limit (MB) | N/A |
| Storage Limit (MB) | N/A |

### Attachments

- `man-in-the-middle.pcapng`

### Flags

- `CSUBRU{b4daea07a5fd166cc6f53db159b297a2}`

---

## Analysis

```bash
strings man-in-the-middle.pcapng | grep "adminlawza007"
{"username":"adminlawza007","password":"nihahaha3389"}
{"username":"adminlawza007","password":"nihahaha3389"}
```

Answer: `adminlawza007:nihahaha3389`
MD5: `b4daea07a5fd166cc6f53db159b297a2`
