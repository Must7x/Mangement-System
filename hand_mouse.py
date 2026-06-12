#!/usr/bin/env python3
"""Control the mouse with hand gestures via webcam."""

from __future__ import annotations

import argparse
import time
from collections.abc import Callable

import cv2
import mediapipe as mp
import numpy as np
import pyautogui

from gestures import GestureConfig, GestureMode, classify, classify_simple, pinch_distance

pyautogui.FAILSAFE = True
pyautogui.PAUSE = 0

HELP_AR = """
=== وضع بسيط (افتراضي) ===
  حرّك إصبع السبابة في الهواء  -> يتحرك المؤشر
  قرّب الإبهام من السبابة (قرصة) -> نقرة
  W / S (نافذة الكاميرا مفعّلة) -> تمرير لأعلى / لأسفل
  مسافة -> إيقاف مؤقت
  Q -> خروج
"""


def parse_args() -> argparse.Namespace:
    parser = argparse.ArgumentParser(description="Hand-controlled mouse with click and scroll")
    parser.add_argument("--camera", type=int, default=0, help="Webcam device index")
    parser.add_argument(
        "--advanced",
        action="store_true",
        help="Use strict gestures (index up, two fingers for scroll)",
    )
    parser.add_argument("--smooth", type=float, default=None, help="Cursor smoothing (0-1)")
    parser.add_argument("--pad", type=float, default=0.12, help="Edge padding for mapping")
    parser.add_argument("--scroll-sens", type=float, default=12.0, help="Scroll sensitivity (advanced)")
    parser.add_argument("--scroll-cooldown", type=float, default=0.04, help="Scroll cooldown (advanced)")
    parser.add_argument("--width", type=int, default=640, help="Capture width")
    parser.add_argument("--height", type=int, default=480, help="Capture height")
    return parser.parse_args()


def map_to_screen(
    tip_x: float,
    tip_y: float,
    pad: float,
    cursor: np.ndarray,
    smooth: float,
) -> np.ndarray:
    nx = np.clip((1.0 - tip_x - pad) / (1.0 - 2.0 * pad), 0.0, 1.0)
    ny = np.clip((tip_y - pad) / (1.0 - 2.0 * pad), 0.0, 1.0)
    target = np.array([nx, ny], dtype=float)
    return cursor * (1.0 - smooth) + target * smooth


def draw_help(frame, lines: list[str], enabled: bool) -> None:
    y = 28
    for line in lines:
        color = (0, 255, 0) if enabled else (0, 120, 255)
        cv2.putText(frame, line, (10, y), cv2.FONT_HERSHEY_SIMPLEX, 0.55, color, 2)
        y += 26


def handle_pinch(lm, config: GestureConfig, pinch_active: bool) -> bool:
    d = pinch_distance(lm)
    if d < config.pinch_on:
        return True
    if pinch_active and d > config.pinch_off:
        pyautogui.click()
        return False
    return pinch_active


def main() -> None:
    args = parse_args()
    simple = not args.advanced
    smooth = args.smooth if args.smooth is not None else (0.18 if simple else 0.25)
    config = GestureConfig(pinch_on=0.05, pinch_off=0.07) if simple else GestureConfig()

    screen_w, screen_h = pyautogui.size()
    cap = cv2.VideoCapture(args.camera)
    cap.set(cv2.CAP_PROP_FRAME_WIDTH, args.width)
    cap.set(cv2.CAP_PROP_FRAME_HEIGHT, args.height)

    if not cap.isOpened():
        raise SystemExit(f"Could not open camera {args.camera}")

    mp_hands = mp.solutions.hands
    hands = mp_hands.Hands(max_num_hands=1, min_detection_confidence=0.6, min_tracking_confidence=0.5)
    mp_draw = mp.solutions.drawing_utils

    classify_fn: Callable = classify_simple if simple else classify

    cursor = np.array([0.5, 0.5], dtype=float)
    enabled = True
    pinch_active = False
    last_scroll_y: float | None = None
    last_scroll_time = 0.0

    if simple:
        print(HELP_AR)
        on_screen = [
            "SIMPLE | Move: index finger | Click: pinch",
            "Scroll: W up / S down (focus this window) | Space: pause",
        ]
    else:
        print("Advanced mode - strict finger gestures")
        on_screen = [
            "ADVANCED | Point / Pinch / 2-finger scroll",
            "Space: pause | Q: quit",
        ]

    while True:
        ok, frame = cap.read()
        if not ok:
            break

        frame = cv2.flip(frame, 1)
        rgb = cv2.cvtColor(frame, cv2.COLOR_BGR2RGB)
        result = hands.process(rgb)

        mode = GestureMode.NONE
        action = "pause" if not enabled else "ready"

        if result.multi_hand_landmarks:
            hand_lm = result.multi_hand_landmarks[0]
            lm = hand_lm.landmark
            mode = classify_fn(lm, config)
            tip = lm[8]

            if enabled:
                if mode == GestureMode.POINT:
                    cursor = map_to_screen(tip.x, tip.y, args.pad, cursor, smooth)
                    pyautogui.moveTo(
                        int(cursor[0] * screen_w),
                        int(cursor[1] * screen_h),
                        duration=0,
                    )
                    action = "move"
                    last_scroll_y = None

                elif mode == GestureMode.PINCH:
                    pinch_active = handle_pinch(lm, config, pinch_active)
                    action = "click (pinch)"
                    last_scroll_y = None

                elif mode == GestureMode.SCROLL:
                    y = tip.y
                    now = time.time()
                    if last_scroll_y is not None and now - last_scroll_time > args.scroll_cooldown:
                        dy = (last_scroll_y - y) * frame.shape[0]
                        if abs(dy) > 3:
                            clicks = int(dy / args.scroll_sens)
                            if clicks != 0:
                                pyautogui.scroll(clicks)
                                last_scroll_time = now
                    last_scroll_y = y
                    pinch_active = False
                    action = "scroll"

                elif pinch_active:
                    pinch_active = handle_pinch(lm, config, pinch_active)
                    last_scroll_y = None

            mp_draw.draw_landmarks(frame, hand_lm, mp_hands.HAND_CONNECTIONS)

        draw_help(frame, on_screen + [f"> {action}"], enabled)
        cv2.imshow("Hand Mouse", frame)

        key = cv2.waitKey(1) & 0xFF
        if key in (ord("q"), ord("Q")):
            break
        if key == ord(" "):
            enabled = not enabled
            pinch_active = False
            last_scroll_y = None
        if simple and enabled:
            if key in (ord("w"), ord("W")):
                pyautogui.scroll(3)
            if key in (ord("s"), ord("S")):
                pyautogui.scroll(-3)

    cap.release()
    cv2.destroyAllWindows()


if __name__ == "__main__":
    main()
