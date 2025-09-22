# Mairu

| Category | Difficulty | Point |
| :-: | :-: | :-: |
| Binary Exploitation | Normal | 200 |

ขณะที่เด็กชายสมพงกำลังทำความสะอาดห้องนั้นเขาได้พบกับเกมปริศนาที่เก็บไว้ในกล่อง ซึ่งมันก็นานมาแล้ว โดยเขาเองก็ไม่รู้เหมือนกันว่าจริงๆแล้วเกมนี้ชื่ออะไรกันแน่ และต้องการให้ทำอะไรถึงจะชนะมันได้ คุณในฐานะพ่อคนที่อดเห็นลูกกลุ้มใจทุกครั้งที่เห็นเกมนี้ไม่ได้ จึงตัดสินใจแน่วแน่ที่จะหาคำตอบเกมนี้ให้ลูกของคุณ ไม่ว่าจะด้วยวิธีใดก็ตามโดยเฉพาะวิธีที่ผู้พัฒนาพยายามป้องกันมันก็ตาม

Password: `infected`

Format: `CSUBRU{...}`

---

## Configuration

| Name | Value |
| :- | :- |
| Title | Mairu |
| Type | Static Attachments |
| Category | Pwn |
| Max Attempts Allowed | unlimited |
| Blood Bonus | Enable |

### Attachments

- `Mairu.7z`

### Flags

- `CSUBRU{r3v3rs3_f1l3_0r_m0d1fy_m3m0ry?}`

---

## Analysis

จากที่กล่าว มี hint ในคำอธิบายคือ `โดยเฉพาะวิธีที่ผู้พัฒนาพยายามป้องกันมันก็ตาม` ก็คือการโกงเกมนั้นเอง

การโกงมีหลายวิธีมาก แต่ท่าพื้นฐานคือการแก้ไข memory

เราจะใช้ Cheat Engine ในการแก้ไข memory โดยการ search หาค่า `Health` ปัจจุบัน แล้วพยายาม track มันให้ได้ address เสร็จแล้วทำการเปลี่ยนค่าเป็น 0 แล้วทำการ hit อีกครั้งก็จะพบ flag

ตัว flag นั้นถูกมองเป็น asset ในการ render จึงไม่สามารถใช้ `strings grep` หรือ C# reverse tool ในการหาได้
