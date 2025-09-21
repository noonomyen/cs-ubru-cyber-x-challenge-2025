#!/bin/bash
set -euo pipefail

unset GZCTF_FLAG

exec apache2-foreground
