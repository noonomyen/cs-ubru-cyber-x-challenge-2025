# Invisible

| Category | Difficulty | Point |
| :-: | :-: | :-: |
| Reverse | Easy | 100 |

มองไม่เห็นหรอก

Format: `CSUBRU{...}`

---

## Configuration

| Name | Value |
| :- | :- |
| Title | Invisible |
| Type | Static Attachments |
| Category | Reverse |
| Max Attempts Allowed | unlimited |
| Blood Bonus | Enable |

[Configuration]

### Attachments

- `invisible.zip`

### Flags

- `CSUBRU{0h_wh47_4r3_y0u_d01ng?}`

### Build

```sh
docker build -t cb-invisible .
docker run --rm cb-invisible cat /build/invisible.zip > ../attachments/invisible.zip
```

---

## Analysis

แค่ strings + grep "CSUBRU"
