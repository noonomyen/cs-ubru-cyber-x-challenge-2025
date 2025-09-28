# Stolen File

| Category | Difficulty | Point |
| :-: | :-: | :-: |
| Network | Medium | 300 |

ไฟล์บางอย่างถูกขโมยไป ได้ยังไง? ทั้งที่ ใน log ก็เห็นแค่ DNS Protocal หนิ

Format: `CSUBRU{...}`

---

## Configuration

| Name | Value |
| :- | :- |
| Title | Stolen File |
| Type | Static Attachments |
| Category | Forensics |
| Max Attempts Allowed | Unlimited |
| Blood Bonus | Enable |
| Container Image | N/A |
| Service Port | N/A |
| CPU Limit (0.1 CPUs) | N/A |
| Memory Limit (MB) | N/A |
| Storage Limit (MB) | N/A |

### Attachments

- `stolen-file.pcapng`

### Flags

- `CSUBRU{why_y0u_s33_m3_dns_d474_3xfil7r47i0n}`

---

## Analysis

```python
#!/usr/bin/env python3
"""Reconstruct an exfiltrated payload from DNS queries captured in a PCAP/PCAPNG."""

import argparse
import sys
from pathlib import Path

try:
    from scapy.all import DNSQR, rdpcap
except ImportError as exc:  # pragma: no cover - helpful guidance when scapy missing
    print("[!] scapy is required (pip install scapy)", file=sys.stderr)
    raise


def parse_args() -> argparse.Namespace:
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument(
        "pcap",
        type=Path,
        help="Path to the dns.pcapng (or .pcap) capture",
    )
    parser.add_argument(
        "-o",
        "--output",
        type=Path,
        default=Path("recovered.png"),
        help="Where to save the reconstructed binary payload",
    )
    parser.add_argument(
        "--hex-output",
        type=Path,
        default=None,
        help="Optional path to write the reconstructed hex string",
    )
    parser.add_argument(
        "--domain",
        default="exfil.lab",
        help="Authoritative suffix used during exfiltration (default: exfil.lab)",
    )
    return parser.parse_args()


def normalise_labels(domain: str) -> list[str]:
    domain = domain.strip(".")
    if not domain:
        raise ValueError("Domain suffix cannot be empty")
    return domain.lower().split(".")


def extract_records(pcap_path: Path) -> list:
    if not pcap_path.exists():
        raise FileNotFoundError(f"PCAP not found: {pcap_path}")
    return rdpcap(str(pcap_path))


def decode_dns_queries(packets, suffix_labels: list[str]):
    chunks: dict[int, str] = {}
    expected_chunks: int | None = None

    for pkt in packets:
        if not pkt.haslayer(DNSQR):
            continue
        question = pkt[DNSQR]
        if not getattr(question, "qname", None):
            continue
        raw_name = question.qname.decode(errors="ignore")
        qname = raw_name.rstrip(".").lower()
        labels = qname.split(".")
        if len(labels) < len(suffix_labels) + 2:
            continue
        if labels[-len(suffix_labels) :] != suffix_labels:
            continue

        head_label = labels[0]
        index_label = labels[1]
        if head_label == "final":
            try:
                expected_chunks = int(index_label, 16)
            except ValueError:
                continue
            continue

        try:
            index = int(index_label, 16)
        except ValueError:
            continue
        if any(char not in "0123456789abcdef" for char in head_label):
            continue
        if index not in chunks:
            chunks[index] = head_label
    return expected_chunks, chunks


def reconstruct(expected_chunks: int | None, chunks: dict[int, str]) -> bytes:
    if expected_chunks is None:
        expected_chunks = max(chunks.keys(), default=-1) + 1
    ordered_hex = []
    for index in range(expected_chunks):
        if index not in chunks:
            raise ValueError(f"Missing chunk {index} (expected {expected_chunks} chunks)")
        ordered_hex.append(chunks[index])
    hex_payload = "".join(ordered_hex)
    try:
        return bytes.fromhex(hex_payload), hex_payload
    except ValueError as err:
        raise ValueError(f"Failed to decode hex payload: {err}") from err


def main() -> None:
    args = parse_args()
    suffix_labels = normalise_labels(args.domain)
    packets = extract_records(args.pcap)
    expected_chunks, chunks = decode_dns_queries(packets, suffix_labels)

    if not chunks:
        print("[!] No exfiltration chunks detected. Check the domain suffix or capture.", file=sys.stderr)
        sys.exit(1)

    try:
        payload, hex_payload = reconstruct(expected_chunks, chunks)
    except ValueError as err:
        print(f"[!] {err}", file=sys.stderr)
        sys.exit(1)

    args.output.write_bytes(payload)
    print(f"[+] Wrote payload ({len(payload)} bytes) to {args.output}")

    if args.hex_output:
        args.hex_output.write_text(hex_payload, encoding="utf-8")
        print(f"[+] Wrote hex payload to {args.hex_output}")


if __name__ == "__main__":
    main()
```
