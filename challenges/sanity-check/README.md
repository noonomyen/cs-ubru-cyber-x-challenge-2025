# Sanity Check

| Category | Difficulty | Point |
| :-: | :-: | :-: |
| Network | Test | 10 |

Are you awake yet?

Format: `CSUBRU{...}`

---

## Configuration

| Name | Value |
| :- | :- |
| Title | Sanity Check |
| Type | Dynamic Container |
| Category | Misc |
| Max Attempts Allowed | unlimited |
| Blood Bonus | Disable |
| Container Image | ic-sanity-check |
| Service Port | 1337 |
| CPU Limit (0.1 CPUs) | 1 |
| Memory Limit (MB) | 32 |
| Storage Limit (MB) | 128 |

### Flags

- `CSUBRU{[GUID]}`

---

## Analysis

แค่ netcat connect ไปที่ instance แล้วตอบ 2
