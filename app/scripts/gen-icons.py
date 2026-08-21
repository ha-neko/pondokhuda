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


def splash(width: int, height: int) -> Image.Image:
    """Calm branded launch screen for pre-Android 12 devices."""
    image = Image.new("RGBA", (width, height), (6, 47, 48, 255))
    glow = Image.new("RGBA", image.size, (0, 0, 0, 0))
    glow_draw = ImageDraw.Draw(glow)
    glow_size = round(min(width, height) * 0.72)
    cx, cy = width // 2, round(height * 0.45)
    glow_draw.ellipse(
        (cx - glow_size // 2, cy - glow_size // 2,
         cx + glow_size // 2, cy + glow_size // 2),
        fill=(76, 217, 222, 18),
    )
    image = Image.alpha_composite(image, glow)

    side = max(72, round(min(width, height) * 0.23))
    symbol = mark(side)
    image.alpha_composite(symbol, ((width - side) // 2, cy - side // 2))

    draw = ImageDraw.Draw(image)
    line_width = max(18, round(side * 0.42))
    line_height = max(3, round(side * 0.035))
    line_top = cy + side // 2 + round(side * 0.17)
    draw.rounded_rectangle(
        (cx - line_width // 2, line_top, cx + line_width // 2, line_top + line_height),
        radius=line_height,
        fill=(141, 226, 217, 180),
    )
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
    (drawable / "splash_mark.xml").write_text("""<?xml version="1.0" encoding="utf-8"?>
<vector xmlns:android="http://schemas.android.com/apk/res/android"
    android:width="108dp" android:height="108dp"
    android:viewportWidth="108" android:viewportHeight="108">
    <path android:pathData="M34,51 L54,34 L74,51 M40,47 L40,74 M68,47 L68,74 M40,61 L68,61"
        android:fillColor="@android:color/transparent" android:strokeColor="#FFFFFF"
        android:strokeWidth="5" android:strokeLineCap="round" android:strokeLineJoin="round" />
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
        <item name="windowSplashScreenBackground">#062F30</item>
        <item name="windowSplashScreenAnimatedIcon">@drawable/splash_mark</item>
        <item name="postSplashScreenTheme">@style/AppTheme.NoActionBar</item>
        <item name="android:statusBarColor">#062F30</item>
        <item name="android:navigationBarColor">#062F30</item>
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
