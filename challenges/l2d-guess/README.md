# L2D Guess

| Category | Difficulty | Point |
| :-: | :-: | :-: |
| Web | Medium | 300 |

ทีมอีเวนต์ของสมศักดิ์ กำลังเตรียมงานแฟนมีตของ Blue Archive เขาเลยทำเว็บเกม L2D Guess ให้แฟน ๆ มาทายชื่อนักเรียนจากภาพ Live2D แบบสุ่ม 10 รอบ หากตอบถูกทุกข้อจะถูกยอมรับว่าเป็น "Blue Archive Big Fan" แต่ถ้าพลาดแม้แต่ข้อเดียวก็จบเกมทันที รีเฟรชปุ๊บเกมก็จะสุ่มชุดรูปใหม่พร้อมนับสถิติใหม่ทั้งหมด ทุกอย่างปกติหมด จนกระทั่งคุณนั้นลอง Scan ดู แล้วคุณล่ะรู้ไหมว่าอะไรที่ผิดปกติ?

Format: `CSUBRU{...}`

---

## Hints

- เกมมันแค่ตัวหลอก

---

## Configuration

| Name | Value |
| :- | :- |
| Title | L2D-Guess |
| Type | Static Container |
| Category | Web |
| Max Attempts Allowed | Unlimited |
| Blood Bonus | Enable |
| Container Image | `ic-l2d-guess` |
| Service Port | 80 |
| CPU Limit (0.1 CPUs) | 1 |
| Memory Limit (MB) | 256 |
| Storage Limit (MB) | 256 |

### Flags

- `CSUBRU{w3lc0m3_blu3_4rchiv3_bigf4n_im_w4i7ing_f0r_y0u_1337_muh4h4}`

---

## Analysis

1. ขอไฟล์ `.env` จากเซิร์ฟเวอร์ (`/.env`) เพื่อดึงคีย์ R2
2. ใช้ credential ที่ได้ไปเรียก Cloudflare R2 API ตรง ๆ
3. `ls` โฟลเดอร์ `secret/` แล้วดึงไฟล์ `secret/flag_choo3ieyoole6Yei.txt`
4. อ่านคอนเทนต์ไฟล์เพื่อรับ flag
