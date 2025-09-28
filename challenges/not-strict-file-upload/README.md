# Not Strict File Upload

| Category | Difficulty | Point |
| :-: | :-: | :-: |
| Forensics | Easy | 100 |

สันติเขาได้สร้างเว็บฝากไฟล์ภาพ แต่เขาเช็คไม่ละเอียดจนทำให้ attacker อัพ shell ขึ้น server คุณพอจะบอกได้ไหมว่า attacker ทำอะไรบ้าง?

Format: `CSUBRU{...}`

---

## Hints

- hint: เข้าเว็บไซต์ตอบทุกคำตามเพื่อให้ได้ Flag

---

## Configuration

| Name | Value |
| :- | :- |
| Title | Not Strict File Upload |
| Type | Dynamic Container |
| Category | Forensics |
| Max Attempts Allowed | Unlimited |
| Blood Bonus | Enable |
| Container Image | ic-not-strict-file-upload |
| Service Port | 80 |
| CPU Limit (0.1 CPUs) | 1 |
| Memory Limit (MB) | 128 |
| Storage Limit (MB) | 256 |

### Attachments

- `nginxLog_easy.zip`

### Flags

- `CSUBRU{upll04d_sh3ll_f0r_c0mm4nd_inj3c7i0n_b4sic_m37h0d_[TEAM_HASH]}`

---

## Analysis

```text
เข้าเว็บ และตอบทีละคำถาม เพื่อให้ได้ Flag
1. 10.42.0.1
2. Arona.php
3. ls
```
