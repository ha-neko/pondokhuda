#!/usr/bin/env python3
"""Generate the Pondok Huda mark for web/PWA and optional Android resources."""
from pathlib import Path
import sys

from PIL import Image, ImageDraw, ImageFont


ROOT = Path(__file__).resolve().parents[1]
PUBLIC = ROOT / "public"
OUT = PUBLIC / "icons"
OUT.mkdir(parents=True, exist_ok=True)

BRAND = (46, 125, 50, 255)
BRAND_DARK = (27, 94, 32, 255)
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


def splash(width: int, height: int) -> Image.Image:
    """Refined branded launch screen for pre-Android 12 devices."""
    image = Image.new("RGBA", (width, height))
    draw = ImageDraw.Draw(image)
    top = (6, 38, 15)
    bottom = (16, 74, 33)
    for y in range(height):
        t = y / max(1, height - 1)
        color = tuple(round(top[i] + (bottom[i] - top[i]) * t) for i in range(3)) + (255,)
        draw.line((0, y, width, y), fill=color)

    unit = min(width, height)
    decor = Image.new("RGBA", image.size, (0, 0, 0, 0))
    decor_draw = ImageDraw.Draw(decor)
    decor_draw.ellipse(
        (width - unit * 0.58, -unit * 0.28, width + unit * 0.18, unit * 0.48),
        fill=(134, 217, 122, 13),
        outline=(168, 230, 158, 18),
        width=max(2, round(unit * 0.008)),
    )
    decor_draw.ellipse(
        (-unit * 0.42, height - unit * 0.36, unit * 0.34, height + unit * 0.40),
        fill=(167, 243, 208, 8),
    )
    image = Image.alpha_composite(image, decor)

    cx = width // 2
    cy = round(height * (0.39 if height >= width else 0.38))
    tile = max(76, round(unit * 0.22))
    radius = round(tile * 0.25)
    surface = Image.new("RGBA", image.size, (0, 0, 0, 0))
    surface_draw = ImageDraw.Draw(surface)
    box = (cx - tile // 2, cy - tile // 2, cx + tile // 2, cy + tile // 2)
    shadow_offset = max(3, round(tile * 0.055))
    surface_draw.rounded_rectangle(
        (box[0], box[1] + shadow_offset, box[2], box[3] + shadow_offset),
        radius=radius,
        fill=(0, 0, 0, 42),
    )
    surface_draw.rounded_rectangle(
        box,
        radius=radius,
        fill=(56, 142, 60, 255),
        outline=(255, 255, 255, 32),
        width=max(1, round(tile * 0.012)),
    )
    image = Image.alpha_composite(image, surface)

    symbol_side = round(tile * 0.62)
    symbol = mark(symbol_side)
    image.alpha_composite(symbol, (cx - symbol_side // 2, cy - symbol_side // 2))

    draw = ImageDraw.Draw(image)
    title_font = splash_font(max(16, round(unit * 0.047)), bold=True)
    sub_font = splash_font(max(11, round(unit * 0.021)), bold=False)
    title = "Pondok Huda"
    subtitle = "ruang nyaman untuk penghuni"
    title_y = box[3] + round(unit * 0.075)
    draw_centered(draw, title, title_y, title_font, (245, 251, 249, 255), width)
    draw_centered(
        draw,
        subtitle,
        title_y + round(unit * 0.062),
        sub_font,
        (190, 211, 207, 225),
        width,
    )
    return image


def splash_font(size: int, bold: bool) -> ImageFont.ImageFont:
    names = (
        "/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf",
        "/usr/share/fonts/TTF/DejaVuSans-Bold.ttf",
    ) if bold else (
        "/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf",
        "/usr/share/fonts/TTF/DejaVuSans.ttf",
    )
    for name in names:
        path = Path(name)
        if path.exists():
            return ImageFont.truetype(str(path), size=size)
    return ImageFont.load_default()


def draw_centered(
    draw: ImageDraw.ImageDraw,
    text: str,
    y: int,
    font: ImageFont.ImageFont,
    fill: tuple[int, int, int, int],
    width: int,
) -> None:
    bounds = draw.textbbox((0, 0), text, font=font)
    text_width = bounds[2] - bounds[0]
    draw.text(((width - text_width) // 2, y), text, font=font, fill=fill)


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
    <solid android:color="#2E7D32" />
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
    (drawable / "splash_mark.xml").write_text("""<?xml version="1.0" encoding="utf-8"?>
<vector xmlns:android="http://schemas.android.com/apk/res/android"
    android:width="108dp" android:height="108dp"
    android:viewportWidth="108" android:viewportHeight="108">
    <path android:pathData="M34,22 H74 C80.6,22 86,27.4 86,34 V74 C86,80.6 80.6,86 74,86 H34 C27.4,86 22,80.6 22,74 V34 C22,27.4 27.4,22 34,22 Z"
        android:fillColor="#388E3C" />
    <path android:pathData="M36,51 L54,35 L72,51 M41,48 L41,73 M67,48 L67,73 M41,60 L67,60"
        android:fillColor="@android:color/transparent" android:strokeColor="#FFFFFF"
        android:strokeWidth="4.5" android:strokeLineCap="round" android:strokeLineJoin="round" />
</vector>
""")

    splash_sizes = {
        "drawable": (480, 320),
        "drawable-land-mdpi": (480, 320),
        "drawable-land-hdpi": (800, 480),
        "drawable-land-xhdpi": (1280, 720),
        "drawable-land-xxhdpi": (1600, 960),
        "drawable-land-xxxhdpi": (1920, 1280),
        "drawable-port-mdpi": (320, 480),
        "drawable-port-hdpi": (480, 800),
        "drawable-port-xhdpi": (720, 1280),
        "drawable-port-xxhdpi": (960, 1600),
        "drawable-port-xxxhdpi": (1280, 1920),
    }
    for directory_name, dimensions in splash_sizes.items():
        directory = res / directory_name
        directory.mkdir(parents=True, exist_ok=True)
        splash(*dimensions).save(directory / "splash.png")

    values = res / "values"
    values.mkdir(parents=True, exist_ok=True)
    (values / "styles.xml").write_text("""<?xml version="1.0" encoding="utf-8"?>
<resources>
    <style name="AppTheme" parent="Theme.AppCompat.Light.DarkActionBar">
        <item name="colorPrimary">@color/colorPrimary</item>
        <item name="colorPrimaryDark">@color/colorPrimaryDark</item>
        <item name="colorAccent">@color/colorAccent</item>
    </style>
    <style name="AppTheme.NoActionBar" parent="Theme.AppCompat.DayNight.NoActionBar">
        <item name="windowActionBar">false</item>
        <item name="windowNoTitle">true</item>
        <item name="android:background">@null</item>
    </style>
    <style name="AppTheme.NoActionBarLaunch" parent="Theme.SplashScreen">
        <item name="android:background">@drawable/splash</item>
        <item name="windowSplashScreenBackground">#06290F</item>
        <item name="windowSplashScreenAnimatedIcon">@drawable/splash_mark</item>
        <item name="postSplashScreenTheme">@style/AppTheme.NoActionBar</item>
        <item name="android:statusBarColor">#06290F</item>
        <item name="android:navigationBarColor">#06290F</item>
        <item name="android:windowLightStatusBar">false</item>
    </style>
</resources>
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
print("generated Pondok Huda web, launcher, and Android splash assets")
