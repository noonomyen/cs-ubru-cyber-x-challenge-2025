# Inside

| Category | Difficulty | Point |
| :-: | :-: | :-: |
| Steganography | Normal | 200 |

Format: `CSUBRU{...}`

---

## Configuration

| Name | Value |
| :- | :- |
| Title | Inside |
| Type | Static Attachments |
| Category | Forensics |
| Max Attempts Allowed | unlimited |
| Blood Bonus | Enable |

[Configuration]

### Attachments

- `inside.zip`

### Flags

- `CSUBRU{15_7h15_c4113d_h1d1ng}`

### Build

```sh
convert -size 10000x10000 xc:black inside.jpg
exiftool -Comment="1337" inside.jpg
steghide embed -cf ./inside.jpg -ef ./secret.txt
```

---

## Analysis

```sh
steghide extract -sf inside.jpg
```

pass `1337` get from exiftool (comment)
