#!/usr/bin/env python3

import csv
import re
import sys
from decimal import Decimal, InvalidOperation
from pathlib import Path

from pypdf import PdfReader


CODE_PATTERN = re.compile(r"^[A-Z][A-Z0-9.-]{0,24}$")
ROW_PATTERN = re.compile(
    r"^\s{0,12}(?P<code>[A-Z][A-Z0-9.-]{0,24})\s{2,}(?P<body>\S.*)$"
)
PRICED_BODY_PATTERN = re.compile(
    r"^(?P<description>.+?)\s{2,}(?P<unit>\S+)\s{2,}(?P<price>[0-9][0-9,]*\.\d{2})$"
)
PRICE_PATTERN = re.compile(r"^\$?[0-9][0-9,]*(?:\.[0-9]{1,4})?$")
CHAPTER_NAMES = {
    "A": "Anteproyectos, proyectos, estudios, trabajos de campo y laboratorio",
    "B": "Desyerbe, desmonte, tala, excavaciones, demoliciones, acarreos y rellenos",
    "C": "Cimbra, estructuras de madera y carpinteria",
    "D": "Acero de refuerzo para concreto",
    "E": "Estructura metalica",
    "F": "Concreto hidraulico",
    "G": "Cimientos, muros, pisos, techados y enladrillados",
    "H": "Instalaciones sanitarias",
    "I": "Instalaciones hidraulicas",
    "J": "Instalaciones complementarias en edificios",
    "K": "Instalaciones electricas en general",
    "L": "Recubrimientos, acabados, pinturas y herrajes",
    "M": "Vidrieria",
    "N": "Alcantarillado",
    "O": "Construccion de sistemas de agua potable",
    "Q": "Obras viales",
    "R": "Pilotes y pilas",
    "S": "Banquetas, guarniciones y andaderos",
    "T": "Alumbrado publico y trabajos afines",
    "U": "Senalizacion en vialidades",
    "V": "Areas jardinadas y forestacion",
    "Z": "Realizacion de limpieza",
}
SKIP_MARKERS = (
    "TABULADOR GENERAL DE PRECIOS UNITARIOS",
    "SECRETAR",
    "DIRECCI",
    "ESTRAT",
    "NORMAS Y REGISTROS DE OBRA",
    "Plaza de la Constituci",
    "Colonia Centro",
    "de  la  Ciudad  de  M",
    "de la Ciudad de M",
    "Alcald�a Cuauht",
    "C.P. 06000",
    "P�gina ",
    "Página ",
    "LOS ESTUDIOS DE AJUSTE DE COSTOS",
    "BLICAS DE LA CIUDAD DE M",
    "ES DEL 27.47 %",
)


def normalize(value: str) -> str:
    return " ".join(value.split())


def price_from(value: str):
    value = normalize(value).replace("$", "")
    if not PRICE_PATTERN.fullmatch(value):
        return None

    try:
        return Decimal(value.replace(",", ""))
    except InvalidOperation:
        return None


def extract_records(pdf_path: Path):
    reader = PdfReader(str(pdf_path))
    if reader.is_encrypted and not reader.decrypt(""):
        raise RuntimeError("The PDF requires a password.")

    records = {}
    chapter_code = None
    chapter_name = None
    current = None

    def finish_current():
        nonlocal chapter_code, chapter_name, current
        if current is None:
            return

        description = normalize(" ".join(current["description_parts"]))
        if len(current["code"]) == 1 and description:
            chapter_code = current["code"]
            chapter_name = description
        elif current["price"] is not None and current["unit"] and description:
            record_chapter_code = current["code"][:1]
            records[current["code"]] = {
                "code": current["code"],
                "chapter_code": record_chapter_code,
                "chapter_name": CHAPTER_NAMES.get(
                    record_chapter_code,
                    chapter_name or f"Capitulo {record_chapter_code}",
                ),
                "description": description,
                "unit": normalize(current["unit"]),
                "labor_unit_price": "",
                "material_unit_price": "",
                "total_unit_price": f'{current["price"]:.2f}',
                "source_page": current["source_page"],
            }
        current = None

    for page_number, page in enumerate(reader.pages, start=1):
        page_text = page.extract_text(extraction_mode="layout") or ""

        for line in page_text.splitlines():
            if not line.strip() or any(marker in line for marker in SKIP_MARKERS):
                continue

            row_match = ROW_PATTERN.match(line)
            code_cell = row_match.group("code") if row_match else ""
            body_cell = row_match.group("body") if row_match else ""
            starts_record = bool(row_match and CODE_PATTERN.fullmatch(code_cell)) and code_cell not in {"P", "PU"}

            if starts_record:
                finish_current()
                priced_match = PRICED_BODY_PATTERN.match(body_cell)
                current = {
                    "code": code_cell,
                    "description_parts": [
                        priced_match.group("description") if priced_match else body_cell
                    ],
                    "unit": priced_match.group("unit") if priced_match else "",
                    "price": price_from(priced_match.group("price")) if priced_match else None,
                    "source_page": page_number,
                }
                continue

            if current is None:
                continue

            continuation = line.strip()
            if "Concepto de Obra" in continuation or "Clave" == continuation:
                continue

            if current["price"] is None:
                priced_match = PRICED_BODY_PATTERN.match(continuation)
                if priced_match:
                    current["description_parts"].append(priced_match.group("description"))
                    current["unit"] = priced_match.group("unit")
                    current["price"] = price_from(priced_match.group("price"))
                    continue

            leading_spaces = len(line) - len(line.lstrip())
            if leading_spaces >= 20:
                current["description_parts"].append(continuation)

    finish_current()
    return list(records.values()), len(reader.pages)


def main():
    if len(sys.argv) != 3:
        raise SystemExit("Usage: extract_cdmx_unit_prices.py INPUT.pdf OUTPUT.csv")

    pdf_path = Path(sys.argv[1])
    output_path = Path(sys.argv[2])
    rows, page_count = extract_records(pdf_path)

    output_path.parent.mkdir(parents=True, exist_ok=True)
    with output_path.open("w", encoding="utf-8", newline="") as stream:
        writer = csv.DictWriter(
            stream,
            fieldnames=[
                "code",
                "chapter_code",
                "chapter_name",
                "description",
                "unit",
                "labor_unit_price",
                "material_unit_price",
                "total_unit_price",
                "source_page",
            ],
        )
        writer.writeheader()
        writer.writerows(rows)

    print(f"Extracted {len(rows)} priced concepts from {page_count} pages.")
    if rows:
        print(f"First: {rows[0]['code']} | Last: {rows[-1]['code']}")


if __name__ == "__main__":
    main()
