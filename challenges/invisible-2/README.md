# Invisible 2

| Category | Difficulty | Point |
| :-: | :-: | :-: |
| Reverse | Normal | 200 |

รู้ได้ไง?

Format: `CSUBRU{...}`

---

## Configuration

| Name | Value |
| :- | :- |
| Title | Invisible 2 |
| Type | Static Attachments |
| Category | Reverse |
| Max Attempts Allowed | unlimited |
| Blood Bonus | Enable |

[Configuration]

### Attachments

- `invisible-2.zip`

### Flags

- `CSUBRU{50_y0u_57111_m4n463d_70_f1nd_m3}`

### Build

```sh
docker build -t cb-invisible-2 .
docker run --rm cb-invisible-2 cat /build/invisible-2.zip > ../attachments/invisible-2.zip
```

---

## Analysis

strings

```text
43535542H
52557b35H
305f7930H
755f3537H
3131315
f6d346e3H
43633645H
f37305f6H
6316e645H
f6d337d
```

```text
4353554252557b35305f7930755f35373131315f6d346e343633645f37305f66316e645f6d337d
```

FROM HEX
