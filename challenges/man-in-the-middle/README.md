# Man In The Middle

| Category | Difficulty | Point |
| :-: | :-: | :-: |
| Network | Normal | 200 |

Format: `CSUBRU{username:password}`

หา username password ที่มี role admin

---

## Hints

- hint: ตัวอย่าง flag CSUBRU{admin:admin1234}

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

- `CSUBRU{adminlawza007:nihahaha3389}`

---

## Analysis

```bash
strings man-in-the-middle.pcapng | grep "adminlawza007"
{"username":"adminlawza007","password":"nihahaha3389"}
{"username":"adminlawza007","password":"nihahaha3389"}
```
