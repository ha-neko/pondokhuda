#!/usr/bin/env python3
"""Generate PondokHuda PWA icons: 192, 512, maskable-512, favicon."""
from PIL import Image, ImageDraw, ImageFont
import os

OUT = os.path.join(os.path.dirname(__file__), "public", "icons")
os.makedirs(OUT, exist_ok=True)

TEAL = (0, 105, 109)
WHITE = (255, 255, 255)
RIN = (156, 241, 244)

def house(draw, size, cx, cy, s, color):
    # roof
    draw.polygon(
        [(cx - s, cy + s * 0.15), (cx, cy - s * 0.75), (cx + s, cy + s * 0.15)],
        fill=color,
    )
    # body
    draw.rounded_rectangle(
        [cx - s * 0.62, cy + s * 0.05, cx + s * 0.62, cy + s * 0.85],
        radius=int(s * 0.15),
        fill=color,
    )
    # door
    draw.rounded_rectangle(
        [cx - s * 0.18, cy + s * 0.35, cx + s * 0.18, cy + s * 0.85],
        radius=int(s * 0.08),
        fill=TEAL,
    )

for size, maskable in [(192, False), (512, False), (512, True)]:
    img = Image.new("RGBA", (size, size), (0, 0, 0, 0))
    d = ImageDraw.Draw(img)
    r = int(size * 0.225)
    d.rounded_rectangle([0, 0, size - 1, size - 1], radius=r, fill=TEAL)
    # subtle circle accent
    d.ellipse(
        [size * 0.12, size * 0.12, size * 0.88, size * 0.88],
        outline=RIN,
        width=max(2, size // 96),
    )
    s = size * (0.30 if maskable else 0.36)
    house(d, size, size / 2, size / 2, s, WHITE)
    name = "icon-maskable-512.png" if maskable else f"icon-{size}.png"
    img.save(os.path.join(OUT, name))
    print("wrote", name)

# favicon 32
img = Image.new("RGBA", (32, 32), (0, 0, 0, 0))
d = ImageDraw.Draw(img)
d.rounded_rectangle([0, 0, 31, 31], radius=7, fill=TEAL)
house(d, 32, 16, 17, 9, WHITE)
img.save(os.path.join(os.path.dirname(OUT), "favicon.png"))
print("wrote favicon.png")