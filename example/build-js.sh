#!/usr/bin/env bash
set -euo pipefail

# Билдит offerwall в binixo-libjs и копирует бандл в example/js/
# Также подставляет версию в exmpl2.php
#
# Запуск из корня binixo-lib:
#   ./example/build-js.sh
#
# Или из binixo-libjs/offerwall:
#   npm run build:lib

EXAMPLE_ROOT="$(cd "$(dirname "$0")" && pwd)"
LIB_ROOT="$(cd "$EXAMPLE_ROOT/.." && pwd)"
JS_ROOT="$(cd "$LIB_ROOT/../binixo-libjs/offerwall" && pwd)"
OUT_DIR="$EXAMPLE_ROOT/js"

if [[ ! -f "$JS_ROOT/package.json" ]]; then
  echo "Не найден binixo-libjs/offerwall: $JS_ROOT" >&2
  exit 1
fi

VERSION="$(node -p "require('$JS_ROOT/package.json').version")"
BUNDLE="offerwall-v${VERSION}.js"

echo "→ build offerwall@${VERSION}"
cd "$JS_ROOT"
npm run typecheck
npm run build

SRC_JS="$JS_ROOT/dist/js/$BUNDLE"
SRC_MAP="$JS_ROOT/dist/js/${BUNDLE}.map"

if [[ ! -f "$SRC_JS" ]]; then
  echo "Бандл не собран: $SRC_JS" >&2
  exit 1
fi

mkdir -p "$OUT_DIR"
cp -f "$SRC_JS" "$OUT_DIR/"
[[ -f "$SRC_MAP" ]] && cp -f "$SRC_MAP" "$OUT_DIR/"

if [[ -f "$EXAMPLE_ROOT/exmpl2.php" ]]; then
  perl -i -pe "s#js/offerwall-v[0-9.]+\\.js#js/${BUNDLE}#g" "$EXAMPLE_ROOT/exmpl2.php"
fi

echo "✓ скопировано: $OUT_DIR/$BUNDLE"
echo "✓ exmpl2.php → js/${BUNDLE}"
