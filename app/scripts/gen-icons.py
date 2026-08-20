#!/usr/bin/env python3
"""Generate PWA icons from the original PondokHuda house mark."""
from pathlib import Path
from PIL import Image, ImageDraw

ROOT = Path(__file__).resolve().parents[1]
PUBLIC = ROOT / "public"
OUT = PUBLIC / "icons"
OUT.mkdir(parents=True, exist_ok=True)

LOGO = Image.open(PUBLIC / "brand-logo-white.png").convert("RGBA")
BRAND = (0, 105, 109, 255)
BRAND_DARK = (0, 92, 96, 255)


def make(size: int, maskable: bool = False) -> Image.Image:
    image = Image.new("RGBA", (size, size), BRAND)
    draw = ImageDraw.Draw(image)
    if not maskable:
        draw.rounded_rectangle(
            (0, 0, size - 1, size - 1),
            radius=int(size * 0.23),
            fill=BRAND,
        )
    else:
        draw.ellipse(
            (int(size * 0.06), int(size * 0.06), int(size * 0.94), int(size * 0.94)),
            fill=BRAND_DARK,
        )

    ratio = 0.52 if maskable else 0.61
    side = int(size * ratio)
    mark = LOGO.resize((side, side), Image.Resampling.LANCZOS)
    image.alpha_composite(mark, ((size - side) // 2, (size - side) // 2))
    return image


make(192).save(OUT / "icon-192.png")
make(512).save(OUT / "icon-512.png")
make(512, maskable=True).save(OUT / "icon-maskable-512.png")
make(64).save(PUBLIC / "favicon.png")
print("generated PondokHuda icons from original house mark")
