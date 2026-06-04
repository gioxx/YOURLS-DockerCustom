#!/usr/bin/env bash
set -euo pipefail

script_dir="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
repo_root="$(cd "$script_dir/.." && pwd)"
target_dir="$repo_root/user/languages"

mkdir -p "$target_dir"

get_source_repo() {
  case "$1" in
    de_DE) printf '%s\n' 'DerSev/YOURLS-de_DE' ;;
    es_ES) printf '%s\n' 'kralizeck/YOURLS-es_ES' ;;
    fr_FR) printf '%s\n' 'ozh/YOURLS-fr_FR' ;;
    it_IT) printf '%s\n' 'ggardin/YOURLS-it_IT' ;;
    *) return 1 ;;
  esac
}

echo "Updating YOURLS translation files in: $target_dir"

for locale in de_DE es_ES fr_FR it_IT; do
  repo="$(get_source_repo "$locale")"
  tmpdir="$(mktemp -d)"

  for ext in po mo; do
    url="https://raw.githubusercontent.com/${repo}/master/${locale}.${ext}"
    out="${target_dir}/${locale}.${ext}"

    echo "  - ${locale}.${ext} <= ${repo}"
    curl -fsSL "$url" -o "$tmpdir/${locale}.${ext}"
    mv "$tmpdir/${locale}.${ext}" "$out"
  done

  rmdir "$tmpdir"
done

echo "Done."
