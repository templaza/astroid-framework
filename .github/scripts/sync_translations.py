#!/usr/bin/env python3
"""
Synchronise Joomla language files from English (en-GB) to multiple target locales.

Usage:
    python .github/scripts/sync_translations.py [options]

Options:
    --locales DE-DE,fr-FR,...   Comma-separated target locales (default: all seven)
    --source   PATH             Source INI file (default: language/en-GB/en-GB.astroid.ini)
    --force                     Re-translate already-translated keys
    --dry-run                   Parse and report without writing files or calling the API
    --no-update-xml             Skip updating astroid.xml

Environment variables:
    DEEPL_API_KEY   DeepL authentication key (required unless --dry-run)

The script:
  1. Parses the source en-GB INI file, preserving comments and structure.
  2. For each target locale, reads the existing translated file (if any).
  3. Identifies new / missing keys that need translation.
  4. Calls the DeepL API in batches with placeholder protection so HTML tags,
     URLs, printf tokens (%s/%d), and Joomla tokens ({...} / _QQ_) survive
     translation unchanged.
  5. Merges translated keys with existing ones (never overwrites existing
     translations unless --force is used).
  6. Writes the updated file to disk following the source structure.
  7. Validates placeholder parity between source and translation.
  8. Updates astroid.xml to declare any newly created locale.
  9. Prints a per-locale summary and exits non-zero on hard errors.
"""

from __future__ import annotations

import argparse
import json
import os
import re
import sys
import time
import urllib.parse
import urllib.request
from pathlib import Path
from typing import Dict, List, Optional, Tuple

# ---------------------------------------------------------------------------
# INI parsing helpers
# ---------------------------------------------------------------------------

# Joomla INI keys: alphanumeric, underscores, hyphens
_RE_KV = re.compile(r"^(?P<key>[A-Za-z0-9_\-]+)\s*=\s*(?P<raw>.+)$")


def _decode_value(raw: str) -> str:
    """Strip surrounding double-quotes from a raw INI value (if present)."""
    raw = raw.strip()
    if len(raw) >= 2 and raw[0] == '"' and raw[-1] == '"':
        return raw[1:-1]
    return raw


def parse_ini(path: Path) -> Tuple[List[str], Dict[str, str]]:
    """Return (original_lines, {key: decoded_value}).

    * Uses utf-8-sig so a leading BOM is silently stripped.
    * For duplicate keys the last definition wins (matching PHP ini behaviour).
    * Lines are returned verbatim to allow faithful reconstruction.
    """
    if not path.exists():
        return [], {}

    text = path.read_text(encoding="utf-8-sig")
    lines: List[str] = text.splitlines()
    translations: Dict[str, str] = {}

    for line in lines:
        m = _RE_KV.match(line)
        if m:
            translations[m.group("key")] = _decode_value(m.group("raw"))

    return lines, translations


def write_ini(path: Path, lines: List[str]) -> None:
    """Write lines to *path* as UTF-8 (no BOM), one line per element."""
    path.parent.mkdir(parents=True, exist_ok=True)
    path.write_text("\n".join(lines) + "\n", encoding="utf-8")


def format_kv(key: str, value: str) -> str:
    """Return ``KEY="value"`` ready to write to an INI file."""
    return f'{key}="{value}"'


# ---------------------------------------------------------------------------
# Placeholder protection
# ---------------------------------------------------------------------------

_RE_PLACEHOLDERS = re.compile(
    r"(__[A-Z_]+__)"         # already-sentinel tokens (idempotent)
    r"|(%\d*\$?[sdufeoxXb])" # printf-style: %s %d %1$s %2$d …
    r"|(\{[^}]+\})"          # Joomla-style {token}
    r"|(_QQ_)"               # Joomla escaped double-quote
    r"|(<[^>]+>)"            # HTML / XML tags
    r"|(https?://\S+)"       # URLs
    r"|(&[a-zA-Z#0-9]+;)"   # HTML entities  &amp; &#39; …
    r"|(\\\")"               # backslash-escaped quote inside value
)


def protect(value: str) -> Tuple[str, Dict[str, str]]:
    """Replace non-translatable tokens with ``__Pn__`` sentinels.

    Returns the protected text and a mapping ``{sentinel: original}``
    that can be used to restore them after translation.
    """
    mapping: Dict[str, str] = {}
    counter = [0]

    def _replace(m: re.Match) -> str:
        token = f"__P{counter[0]}__"
        mapping[token] = m.group(0)
        counter[0] += 1
        return token

    return _RE_PLACEHOLDERS.sub(_replace, value), mapping


def restore(text: str, mapping: Dict[str, str]) -> str:
    for sentinel, original in mapping.items():
        text = text.replace(sentinel, original)
    return text


# ---------------------------------------------------------------------------
# Validation
# ---------------------------------------------------------------------------

_RE_PRINTF = re.compile(r"%\d*\$?[sdufeoxXb]")
_RE_JTOKEN = re.compile(r"\{[^}]+\}")


def validate(source_val: str, translated_val: str, key: str) -> List[str]:
    """Return a list of warning strings for a single translation."""
    warnings: List[str] = []

    src_printf = sorted(_RE_PRINTF.findall(source_val))
    tgt_printf = sorted(_RE_PRINTF.findall(translated_val))
    if src_printf != tgt_printf:
        warnings.append(
            f"  WARN [{key}]: printf mismatch "
            f"source={src_printf} translated={tgt_printf}"
        )

    src_jtok = sorted(_RE_JTOKEN.findall(source_val))
    tgt_jtok = sorted(_RE_JTOKEN.findall(translated_val))
    if src_jtok != tgt_jtok:
        warnings.append(
            f"  WARN [{key}]: token mismatch "
            f"source={src_jtok} translated={tgt_jtok}"
        )

    return warnings


# ---------------------------------------------------------------------------
# DeepL translation backend
# ---------------------------------------------------------------------------

#: Maps Joomla locale tags to DeepL target language codes.
DEEPL_LANG = {
    "de-DE": "DE",
    "fr-FR": "FR",
    "es-ES": "ES",
    "pt-BR": "PT-BR",
    "zh-CN": "ZH",
    "ru-RU": "RU",
    "vi-VN": "VI",
}


def _deepl_endpoint(api_key: str) -> str:
    return (
        "https://api-free.deepl.com/v2/translate"
        if api_key.endswith(":fx")
        else "https://api.deepl.com/v2/translate"
    )


def translate_deepl(
    texts: List[str],
    target_lang: str,
    api_key: str,
    source_lang: str = "EN",
) -> List[str]:
    """Translate *texts* to *target_lang* using the DeepL REST API.

    Batches up to 50 items per request to stay within API limits.
    Falls back to the original text on any per-batch error.
    """
    endpoint = _deepl_endpoint(api_key)
    results: List[str] = []
    batch_size = 50

    for batch_start in range(0, len(texts), batch_size):
        batch = texts[batch_start : batch_start + batch_size]
        params: List[Tuple[str, str]] = [
            ("auth_key", api_key),
            ("source_lang", source_lang),
            ("target_lang", target_lang),
        ]
        for text in batch:
            params.append(("text", text))

        body = urllib.parse.urlencode(params, encoding="utf-8").encode("utf-8")
        req = urllib.request.Request(
            endpoint,
            data=body,
            method="POST",
            headers={"Content-Type": "application/x-www-form-urlencoded"},
        )
        try:
            with urllib.request.urlopen(req, timeout=60) as resp:
                data: dict = json.loads(resp.read().decode("utf-8"))
            results.extend(t["text"] for t in data["translations"])
        except Exception as exc:  # noqa: BLE001
            print(f"    [ERROR] DeepL batch failed: {exc}", file=sys.stderr)
            results.extend(batch)  # fall back to source text

        if batch_start + batch_size < len(texts):
            time.sleep(0.3)  # gentle rate limiting

    return results


# ---------------------------------------------------------------------------
# Sync one locale
# ---------------------------------------------------------------------------

def sync_locale(
    source_keys: Dict[str, str],
    source_lines: List[str],
    locale: str,
    target_ini: Path,
    api_key: Optional[str],
    *,
    force: bool,
    dry_run: bool,
) -> dict:
    """Sync translations for *locale*.  Returns a stats dict."""
    deepl_lang = DEEPL_LANG.get(locale)
    if deepl_lang is None:
        print(f"  [{locale}] No DeepL mapping – skipped.", file=sys.stderr)
        return {"skipped": True}

    _existing_lines, existing = parse_ini(target_ini)

    # Keys that need translation
    if force:
        to_translate = list(source_keys.keys())
    else:
        to_translate = [k for k in source_keys if k not in existing]

    stats = {
        "total_source": len(source_keys),
        "pre_existing": len(existing),
        "to_translate": len(to_translate),
        "translated": 0,
        "warnings": [],
    }

    if not to_translate:
        coverage = len(existing) / len(source_keys) * 100 if source_keys else 0
        print(
            f"  [{locale}] Nothing to translate "
            f"({len(existing)}/{len(source_keys)} keys, {coverage:.1f}% coverage)."
        )
        return stats

    print(
        f"  [{locale}] {len(to_translate)} keys need translation "
        f"(DeepL target: {deepl_lang})"
    )

    # Protect placeholders
    protected_texts: List[str] = []
    placeholder_maps: List[Dict[str, str]] = []
    for key in to_translate:
        ptext, pmap = protect(source_keys[key])
        protected_texts.append(ptext)
        placeholder_maps.append(pmap)

    # Translate (or no-op in dry-run)
    if dry_run or api_key is None:
        translated_texts = list(protected_texts)  # no-op
    else:
        translated_texts = translate_deepl(protected_texts, deepl_lang, api_key)

    # Restore + validate
    new_translations: Dict[str, str] = {}
    for key, ttext, pmap in zip(to_translate, translated_texts, placeholder_maps):
        restored = restore(ttext, pmap)
        warns = validate(source_keys[key], restored, key)
        stats["warnings"].extend(warns)
        new_translations[key] = restored
        stats["translated"] += 1

    # Merge: existing takes priority unless --force
    updated: Dict[str, str] = dict(existing)
    updated.update(new_translations)  # new keys only (or all if force)
    if force:
        updated.update(new_translations)  # explicit overwrite

    # Reconstruct file following source structure
    out_lines: List[str] = []
    written_keys: set = set()

    for line in source_lines:
        m = _RE_KV.match(line)
        if m:
            key = m.group("key")
            if key in written_keys:
                continue  # skip duplicate keys from source
            if key in updated:
                out_lines.append(format_kv(key, updated[key]))
                written_keys.add(key)
            else:
                # Key not translatable (shouldn't happen) – keep as comment
                out_lines.append(f"; UNTRANSLATED: {line}")
        else:
            out_lines.append(line)

    # Orphaned keys: in target but no longer in source
    orphans = [k for k in updated if k not in written_keys]
    if orphans:
        out_lines.append("")
        out_lines.append("; Orphaned keys (removed from source – kept for reference)")
        for k in orphans:
            out_lines.append(f"; {format_kv(k, updated[k])}")

    if not dry_run:
        write_ini(target_ini, out_lines)
        print(f"  [{locale}] Written → {target_ini}")
    else:
        print(f"  [{locale}] DRY RUN – would write {target_ini}")

    return stats


# ---------------------------------------------------------------------------
# astroid.xml updater
# ---------------------------------------------------------------------------

def update_astroid_xml(locales: List[str], xml_path: Path) -> bool:
    """Insert ``<language>`` entries for new locales into *xml_path*.

    Uses simple string matching to avoid corrupting the file's existing
    formatting and comments.  Returns True if the file was modified.
    """
    if not xml_path.exists():
        print(f"  [WARN] {xml_path} not found – skipping XML update.")
        return False

    content = xml_path.read_text(encoding="utf-8")
    insert_lines: List[str] = []

    for locale in locales:
        if locale == "en-GB":
            continue
        if f'tag="{locale}"' in content:
            continue
        # Match the indent used by existing <language> entries (6 spaces)
        insert_lines.append(
            f'      <language tag="{locale}">'
            f'{locale}/{locale}.astroid.ini</language>'
        )
        insert_lines.append(
            f'      <language tag="{locale}">'
            f'{locale}/{locale}.astroid.sys.ini</language>'
        )
        print(f"  [astroid.xml] Added {locale} entries.")

    if not insert_lines:
        return False

    new_block = "\n".join(insert_lines) + "\n"
    # Insert before the closing </languages> tag (preserve indentation)
    updated = re.sub(
        r"(\s*</languages>)",
        "\n" + new_block + r"\1",
        content,
        count=1,
    )
    if updated == content:
        print("  [WARN] Could not locate </languages> in astroid.xml", file=sys.stderr)
        return False

    xml_path.write_text(updated, encoding="utf-8")
    return True


# ---------------------------------------------------------------------------
# CLI
# ---------------------------------------------------------------------------

_DEFAULT_LOCALES = ["de-DE", "fr-FR", "es-ES", "pt-BR", "zh-CN", "ru-RU", "vi-VN"]
_DEFAULT_SOURCE = Path("language/en-GB/en-GB.astroid.ini")
_ASTROID_XML = Path("astroid.xml")


def build_parser() -> argparse.ArgumentParser:
    p = argparse.ArgumentParser(
        description=__doc__,
        formatter_class=argparse.RawDescriptionHelpFormatter,
    )
    p.add_argument(
        "--locales",
        default=",".join(_DEFAULT_LOCALES),
        help="Comma-separated target locales (default: %(default)s)",
    )
    p.add_argument(
        "--source",
        type=Path,
        default=_DEFAULT_SOURCE,
        help="Source en-GB INI file (default: %(default)s)",
    )
    p.add_argument(
        "--force",
        action="store_true",
        help="Re-translate all keys, overwriting existing translations",
    )
    p.add_argument(
        "--dry-run",
        action="store_true",
        help="Parse and report without calling the API or writing files",
    )
    p.add_argument(
        "--no-update-xml",
        action="store_true",
        help="Skip updating astroid.xml",
    )
    return p


def main(argv: Optional[List[str]] = None) -> int:  # noqa: C901
    args = build_parser().parse_args(argv)

    api_key: Optional[str] = os.environ.get("DEEPL_API_KEY") or None

    if not api_key and not args.dry_run:
        print(
            "[ERROR] DEEPL_API_KEY is not set.\n"
            "        Set the environment variable or use --dry-run to preview.",
            file=sys.stderr,
        )
        return 1

    locales = [loc.strip() for loc in args.locales.split(",") if loc.strip()]

    # Parse source
    if not args.source.exists():
        print(f"[ERROR] Source file not found: {args.source}", file=sys.stderr)
        return 1

    source_lines, source_keys = parse_ini(args.source)
    print(f"Source: {args.source}  ({len(source_keys)} keys)\n")

    all_stats: Dict[str, dict] = {}
    exit_code = 0

    for locale in locales:
        target_ini = Path(f"language/{locale}/{locale}.astroid.ini")
        target_sys = Path(f"language/{locale}/{locale}.astroid.sys.ini")

        print(f"── {locale} ──")
        stats = sync_locale(
            source_keys,
            source_lines,
            locale,
            target_ini,
            api_key,
            force=args.force,
            dry_run=args.dry_run,
        )
        all_stats[locale] = stats

        # Ensure sys.ini file exists (even if empty)
        if not args.dry_run and not target_sys.exists():
            target_sys.parent.mkdir(parents=True, exist_ok=True)
            target_sys.write_text("", encoding="utf-8")
            print(f"  [{locale}] Created {target_sys}")

        if stats.get("warnings"):
            for w in stats["warnings"]:
                print(w)

        print()

    # Update astroid.xml
    if not args.no_update_xml and not args.dry_run:
        print("── astroid.xml ──")
        update_astroid_xml(locales, _ASTROID_XML)
        print()

    # Summary table
    print("═" * 62)
    print(f"{'Locale':<10} {'Coverage':>10}  {'New':>6}  {'Existing':>9}  {'Warns':>6}")
    print("─" * 62)
    for locale, stats in all_stats.items():
        if stats.get("skipped"):
            print(f"{locale:<10}  {'SKIPPED':>10}")
            continue
        total = stats["total_source"]
        existing = stats["pre_existing"]
        translated = stats["translated"]
        warns = len(stats.get("warnings", []))
        coverage = (existing + translated) / total * 100 if total else 0.0
        print(
            f"{locale:<10} {coverage:>9.1f}%  "
            f"{translated:>6}  {existing:>9}  {warns:>6}"
        )
    print("═" * 62)

    return exit_code


if __name__ == "__main__":
    sys.exit(main())
