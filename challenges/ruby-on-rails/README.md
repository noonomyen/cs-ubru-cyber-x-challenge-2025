# Ruby On Rails

| Category | Difficulty | Point |
| :-: | :-: | :-: |
| Forensics | Medium | 300 |

สมปองเขาได้ลองเขียนฝึกเขียน Ruby on Rails แต่ดันไปลง version ที่มีช่องโหว่และใช่ เขาไม่ได้ setup firewall จนทำให้คนที่อยู่ใน network สามารถเข้าเว็บของเขาได้และได้ขโมยบางอย่างออกไป คุณพอจะรู้ไหมว่าที่ถูกขโมยไปคืออะไร? ไม่ต้องเป็นห่วงเพราะสมปองเขามี nginx log ให้คุณ

Format: `CSUBRU{...}`

---

## Hints

- hint: CVE-2018-3760

---

## Configuration

| Name | Value |
| :- | :- |
| Title | Ruby On Rails |
| Type | Dynamic Container |
| Category | Forensics |
| Max Attempts Allowed | Unlimited |
| Blood Bonus | Enable |
| Container Image | ic-ruby-on-rails |
| Service Port | 80 |
| CPU Limit (0.1 CPUs) | 1 |
| Memory Limit (MB) | 128 |
| Storage Limit (MB) | 256 |

### Attachments

- `nginxLog.zip`

### Flags

- `CSUBRU{cv3_2018_3760_ruby_0n_r4ils_s0_wh3r3_is_7h3_7r4in_[TEAM_HASH]}`

---

## Analysis

```text
เข้าเว็บ และตอบทีละคำถาม เพื่อให้ได้ Flag
1. 172.18.0.1
2. 25/Sep/2025
3. Path Traversal
4. /etc/passwd
5. nuclearLaunchCode
```
