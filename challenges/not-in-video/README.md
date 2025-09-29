# Not In Video

| Category | Difficulty | Point |
| :-: | :-: | :-: |
| Steganography | Easy | 100 |

Format: `CSUBRU{...}`

---

## Configuration

| Name | Value |
| :- | :- |
| Title | Not In Video |
| Type | Static Attachments |
| Category | Forensics |
| Max Attempts Allowed | unlimited |
| Blood Bonus | Enable |

[Configuration]

### Attachments

- `not-in-video.zips`

### Flags

- `CSUBRU{fl4g_15_1n_4_57r4ng3_pl4c3}`

### Build

```sh
ffmpeg -f lavfi -i color=c=black:s=1280x720:d=10 -c:v libx264 -t 10 not-in-video.mp4
echo "CSUBRU{fl4g_15_1n_4_57r4ng3_pl4c3}" >> not-in-video.mp4
```

---

## Analysis

strings grep
