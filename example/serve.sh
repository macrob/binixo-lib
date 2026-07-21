#!/usr/bin/env bash
set -euo pipefail

ROOT="$(cd "$(dirname "$0")" && pwd)"
PORT="${PORT:-8080}"
HOST="${HOST:-127.0.0.1}"

mkdir -p "$ROOT/tmp"

echo "Offerwall example: http://${HOST}:${PORT}/exmpl2.php"
echo "Also:             http://${HOST}:${PORT}/exmpl3.php"
echo "Ctrl+C to stop"
echo

cd "$ROOT"
exec php -S "${HOST}:${PORT}"
