from pathlib import Path
from PIL import Image, ImageDraw, ImageFont, ImageFilter
import math

ROOT = Path(__file__).resolve().parent.parent
OUT = Path(__file__).resolve().parent / "frames"
OUT.mkdir(exist_ok=True)

W, H, FPS, N = 1920, 1080, 30, 150
cream = (251, 248, 241)
ink = (36, 48, 42)
green = (46, 101, 72)
orange = (239, 140, 75)
yellow = (246, 216, 120)

def font(size, bold=False):
    candidates = [
        "/System/Library/Fonts/Supplemental/Arial Bold.ttf" if bold else "/System/Library/Fonts/Supplemental/Arial.ttf",
        "/System/Library/Fonts/SFNS.ttf",
    ]
    for p in candidates:
        if Path(p).exists():
            return ImageFont.truetype(p, size)
    return ImageFont.load_default()

logo = Image.open(ROOT / "assets/ipet-logo.png").convert("RGBA")
hero = Image.open(ROOT / "assets/carousel-dog.png").convert("RGB")

def ease(t):
    return 1 - (1 - t) ** 3

def cover(img, size, scale=1.0, x=0.5, y=0.5):
    iw, ih = img.size
    target = max(size[0] / iw, size[1] / ih) * scale
    resized = img.resize((int(iw * target), int(ih * target)), Image.Resampling.LANCZOS)
    left = int((resized.width - size[0]) * x)
    top = int((resized.height - size[1]) * y)
    return resized.crop((left, top, left + size[0], top + size[1]))

for i in range(N):
    t = i / FPS
    img = Image.new("RGB", (W, H), cream)
    d = ImageDraw.Draw(img, "RGBA")
    d.rounded_rectangle((38, 38, W-38, H-38), radius=34, outline=(232,227,216,255), width=2)

    intro = ease(min(1, t / 0.8))
    # Decorative color fields: layered, soft, brand-led.
    d.ellipse((1350 + int(22*math.sin(t*1.8)), -140, 2100, 610), fill=(220,235,213,170))
    d.ellipse((-230, 720 + int(18*math.sin(t*1.4)), 430, 1380), fill=(246,216,120,90))
    dot_y = 168 - int(38 * intro)
    d.ellipse((1732, dot_y, 1794, dot_y+62), fill=orange)

    lw, lh = logo.size
    logo_scale = 0.11 * intro
    if logo_scale > 0:
        lg = logo.resize((max(1, int(lw*logo_scale)), max(1, int(lh*logo_scale))), Image.Resampling.LANCZOS)
        img.paste(lg, (112, 88), lg)

    reveal = ease(min(1, max(0, (t-0.55)/0.95)))
    if reveal > 0:
        hero_frame = cover(hero, (940, 650), scale=1.04 + 0.025*min(1,t/5), x=0.5 + 0.035*min(1,t/5), y=0.48)
        mask = Image.new("L", (940, 650), 0)
        md = ImageDraw.Draw(mask)
        md.rounded_rectangle((int(-940+940*reveal), 0, 940, 650), radius=30, fill=255)
        img.paste(hero_frame, (870, 240), mask)
        d = ImageDraw.Draw(img, "RGBA")
        d.rounded_rectangle((870, 240, 1810, 890), radius=30, outline=(255,255,255,220), width=3)

    text_alpha = int(255 * ease(min(1, max(0, (t-0.95)/0.7))))
    d = ImageDraw.Draw(img, "RGBA")
    d.text((115, 290), "EVERYDAY FAVORITES", font=font(24, True), fill=(*green, text_alpha))
    d.text((115, 350), "Just the right kind", font=font(78, True), fill=(*ink, text_alpha))
    d.text((115, 442), "of company, every day.", font=font(78, True), fill=(*ink, text_alpha))
    d.text((118, 580), "Find food, toys and care essentials", font=font(30), fill=(83,98,90,text_alpha))
    d.text((118, 622), "for every pet and every routine.", font=font(30), fill=(83,98,90,text_alpha))

    cta = ease(min(1, max(0, (t-2.1)/0.7)))
    if cta > 0:
        d.rounded_rectangle((115, 735, 430, 800), radius=32, fill=(*green, int(255*cta)))
        d.text((150, 750), "Start shopping  →", font=font(25, True), fill=(255,255,255,int(255*cta)))
        d.text((115, 850), "Dogs   ·   Cats   ·   Small Pets", font=font(22, True), fill=(*green, int(255*cta)))

    # Tiny continuous lift gives the still image a subtle living quality.
    if i:
        prev = OUT / f"frame-{i-1:04d}.png"
    img.save(OUT / f"frame-{i:04d}.png", quality=95)

frames = [Image.open(OUT / f"frame-{i:04d}.png").convert("RGB") for i in range(N)]
frames[0].save(Path(__file__).resolve().parent / "ipet-test.gif", save_all=True,
               append_images=frames[1:], duration=int(1000/FPS), loop=0, optimize=False)

# Portable fallback movie: Motion-JPEG AVI is playable by QuickTime, VLC and browsers
# that support AVI, and does not require an external encoder.
import io, struct
def chunk(tag, payload):
    pad = b"\0" if len(payload) % 2 else b""
    return tag + struct.pack("<I", len(payload)) + payload + pad
def list_chunk(kind, payload):
    body = kind + payload
    pad = b"\0" if len(body) % 2 else b""
    return b"LIST" + struct.pack("<I", len(body)) + body + pad

jpgs = []
for frame in frames:
    buf = io.BytesIO()
    frame.save(buf, format="JPEG", quality=90, optimize=True)
    jpgs.append(buf.getvalue())
frame_bytes = b"".join(chunk(b"00dc", data) for data in jpgs)
idx = b""; offset = 4; cursor = 4
for data in jpgs:
    idx += struct.pack("<4s4sIII", b"00dc", b"00db", 0x10, cursor, len(data))
    cursor += 8 + len(data) + (len(data) % 2)

avih = struct.pack("<IIIIIIIIIIIIII", 33333, max(map(len, jpgs)), 0, 0x10, N, 0, 1, max(map(len, jpgs)), W, H, 0, 0, 0, 0)
strh = struct.pack("<4s4sIHHIIIIIIIIhhhh", b"vids", b"MJPG", 0, 0, 0, 1, FPS, 0, N, max(map(len, jpgs)), 0xFFFFFFFF, 0, 0, 0, 0, W, H)
strf = struct.pack("<IIIHHIIIIII", 40, W, H, 1, 24, int.from_bytes(b"MJPG", "little"), 0, 0, 0, 0, 0)
hdrl = list_chunk(b"hdrl", chunk(b"avih", avih) + list_chunk(b"strl", chunk(b"strh", strh) + chunk(b"strf", strf)))
movi = list_chunk(b"movi", frame_bytes)
avi = b"RIFF" + struct.pack("<I", 4 + len(hdrl) + len(movi) + len(chunk(b"idx1", idx))) + b"AVI " + hdrl + movi + chunk(b"idx1", idx)
(Path(__file__).resolve().parent / "renders" / "ipet-test.avi").write_bytes(avi)
print(f"Generated {N} frames, GIF and AVI in {Path(__file__).resolve().parent / 'renders'}")
