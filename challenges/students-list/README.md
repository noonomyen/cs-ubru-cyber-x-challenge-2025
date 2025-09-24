# Students List

| Category | Difficulty | Point |
| :-: | :-: | :-: |
| Web | Normal | 200 |

สมพงษ์อยากไปงาน Event Blue Archive Big Fan แต่พอไปทางเข้า ดันเจอด่านตรวจ โดยที่ให้ตอบชื่อจากรูปที่โจทย์มาให้ สมพงษ์เก่งพอจะรู้ว่ารูปนี้คือใคร แต่ปัญหาคือเขาดันสะกดเป๊ะ ๆ ไม่ถูก ด้วยความที่เขาเป็น vibe coder เขาจึง speed run ทำเว็บเพื่อแสดงผลชื่อตามที่ search ชื่อก็ได้รายชื่อตัวละครมาแล้ว และเพื่อความปลอดภัย เขาจึงป้องกันการโดน SQL Injection โดยการ Block คำอันตราย และ space เขามั่นใจว่ายังไงก็ไม่โดนแล้ว และแล้วเขาก็เจอคุณในงาน จึงได้พูดคุยกันเรื่องเว็บที่เขาสร้าง และเขาก็ได้ท้าคุณผู้เป็น Hacker ผู้มีประสบการณ์ โดยที่เขาท้ามาว่าให้หาข้อมูลที่ถูกซ่อนไว้ใน database ที่อยู่ใน table secret_flag

Format: `CSUBRU{...}`

---

## Hints

Hint: ถูก block แค่ Upper Case

---

## Configuration

| Name | Value |
| :- | :- |
| Title | Students List |
| Type | Dynamic Container |
| Category | Web |
| Max Attempts Allowed | Unlimited |
| Blood Bonus | Enable |
| Container Image | ic-students-list |
| Service Port | 80 |
| CPU Limit (0.1 CPUs) | 1 |
| Memory Limit (MB) | 128 |
| Storage Limit (MB) | 256 |

### Flags

- `CSUBRU{sp4c3_is_34sy_70_by_p4ss_0r_n07_[TEAM_HASH]}`

---

## Analysis

1. Submit test queries to observe that the application strips spaces and rejects any upper-case SQL keywords such as `UNION` or `SELECT`.
2. Swap spaces for `/**/` comments and write your payload entirely in lower case to bypass the filter.
3. Use an input like:

   ```sql
   '/**/union/**/select/**/9999,flag,'x','y'/**/from/**/secret_flag--
   ```

   inside the search box to break out of the `LIKE` clause.
