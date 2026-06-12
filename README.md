# Hand Mouse

Control your mouse with hand gestures using your webcam.

## Simple mode (default)

| Action | How |
|--------|-----|
| Move cursor | Move your index finger in the air |
| Click | Pinch thumb and index together |
| Scroll | Press **W** / **S** while the camera window is focused |
| Pause | **Space** |
| Quit | **Q** |

## Advanced mode (`--advanced`)

| Gesture | Action |
|---------|--------|
| Index finger up (others down) | Move cursor |
| Pinch thumb + index | Left click |
| Index + middle up (ring/pinky down) | Scroll (move hand up/down) |

## Setup

```powershell
cd C:\Users\delli7\Projects\hand-mouse
python -m venv .venv
.\.venv\Scripts\Activate.ps1
pip install -r requirements.txt
```

## Run

```powershell
python hand_mouse.py
```

### Controls

- **Space** — pause/resume mouse control
- **Q** — quit
- Move mouse to any screen corner — emergency stop (PyAutoGUI failsafe)

### Options

```powershell
python hand_mouse.py --camera 0 --smooth 0.2 --scroll-sens 15
```

| Flag | Default | Description |
|------|---------|-------------|
| `--camera` | 0 | Webcam device index |
| `--smooth` | 0.25 | Cursor smoothing (lower = smoother) |
| `--pad` | 0.15 | Edge dead zone for mapping |
| `--scroll-sens` | 12 | Scroll sensitivity |
| `--width` / `--height` | 640×480 | Capture resolution |

## Tuning

If clicks fire too easily, edit `gestures.py` and lower `pinch_on` (e.g. `0.035`). If clicks are hard to trigger, raise `pinch_off`.

Good lighting and sitting ~0.5–1 m from the camera works best.
