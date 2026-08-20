#!/usr/bin/env python3
"""Generate the Pondok Huda mark for web/PWA and optional Android resources."""
from pathlib import Path
import sys

from PIL import Image, ImageDraw


ROOT = Path(__file__).resolve().parents[1]
PUBLIC = ROOT / "public"
OUT = PUBLIC / "icons"
OUT.mkdir(parents=True, exist_ok=True)

BRAND = (5, 111, 108, 255)
BRAND_DARK = (4, 88, 86, 255)
WHITE = (255, 255, 255, 255)


def mark(size: int, color=WHITE) -> Image.Image:
    """A restrained roof + H monogram. It reads as both home and Huda."""
    image = Image.new("RGBA", (size, size), (0, 0, 0, 0))
    draw = ImageDraw.Draw(image)
    width = max(2, round(size * 0.075))
    roof = [(round(size * 0.16), round(size * 0.45)),
            (round(size * 0.50), round(size * 0.16)),
            (round(size * 0.84), round(size * 0.45))]
    draw.line(roof, fill=color, width=width, joint="curve")
    left = round(size * 0.27)
    right = round(size * 0.73)
    top = round(size * 0.38)
    bottom = round(size * 0.82)
    middle = round(size * 0.59)
    draw.line((left, top, left, bottom), fill=color, width=width)
    draw.line((right, top, right, bottom), fill=color, width=width)
    draw.line((left, middle, right, middle), fill=color, width=width)
    return image


def launcher(size: int, maskable: bool = False, round_icon: bool = False) -> Image.Image:
    image = Image.new("RGBA", (size, size), BRAND)
    draw = ImageDraw.Draw(image)
    if round_icon:
        image = Image.new("RGBA", (size, size), (0, 0, 0, 0))
        draw = ImageDraw.Draw(image)
        draw.ellipse((0, 0, size - 1, size - 1), fill=BRAND)
    elif not maskable:
        draw.rounded_rectangle((0, 0, size - 1, size - 1), radius=round(size * 0.22), fill=BRAND)

    ratio = 0.48 if maskable else 0.57
    side = round(size * ratio)
    symbol = mark(side)
    image.alpha_composite(symbol, ((size - side) // 2, (size - side) // 2))
    return image


def generate_web() -> None:
    mark(256).save(PUBLIC / "brand-logo-white.png")
    mark(256, BRAND_DARK).save(PUBLIC / "brand-logo-black.png")
    launcher(192).save(OUT / "icon-192.png")
    launcher(512).save(OUT / "icon-512.png")
    launcher(512, maskable=True).save(OUT / "icon-maskable-512.png")
    launcher(64).save(PUBLIC / "favicon.png")


def generate_android(res: Path) -> None:
    densities = {"mdpi": 48, "hdpi": 72, "xhdpi": 96, "xxhdpi": 144, "xxxhdpi": 192}
    for density, size in densities.items():
        directory = res / f"mipmap-{density}"
        directory.mkdir(parents=True, exist_ok=True)
        launcher(size).save(directory / "ic_launcher.png")
        launcher(size, round_icon=True).save(directory / "ic_launcher_round.png")

    drawable = res / "drawable"
    drawable.mkdir(parents=True, exist_ok=True)
    (drawable / "ic_launcher_background.xml").write_text("""<?xml version="1.0" encoding="utf-8"?>
<shape xmlns:android="http://schemas.android.com/apk/res/android" android:shape="rectangle">
    <solid android:color="#056F6C" />
</shape>
""")
    foreground = res / "drawable-v24"
    foreground.mkdir(parents=True, exist_ok=True)
    (foreground / "ic_launcher_foreground.xml").write_text("""<?xml version="1.0" encoding="utf-8"?>
<vector xmlns:android="http://schemas.android.com/apk/res/android"
    android:width="108dp" android:height="108dp"
    android:viewportWidth="108" android:viewportHeight="108">
    <path android:pathData="M38,52 L54,38 L70,52 M43,49 L43,70 M65,49 L65,70 M43,59 L65,59"
        android:fillColor="@android:color/transparent" android:strokeColor="#FFFFFF"
        android:strokeWidth="4.5" android:strokeLineCap="round" android:strokeLineJoin="round" />
</vector>
""")
    adaptive = res / "mipmap-anydpi-v26"
    adaptive.mkdir(parents=True, exist_ok=True)
    for name in ("ic_launcher.xml", "ic_launcher_round.xml"):
        (adaptive / name).write_text("""<?xml version="1.0" encoding="utf-8"?>
<adaptive-icon xmlns:android="http://schemas.android.com/apk/res/android">
    <background android:drawable="@drawable/ic_launcher_background" />
    <foreground android:drawable="@drawable/ic_launcher_foreground" />
</adaptive-icon>
""")


generate_web()
if len(sys.argv) > 1:
    generate_android(Path(sys.argv[1]).resolve())
print("generated Pondok Huda web and launcher icons")
