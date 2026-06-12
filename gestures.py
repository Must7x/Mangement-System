"""Hand gesture detection for camera mouse control."""

from __future__ import annotations

import math
from dataclasses import dataclass
from enum import Enum
from typing import Sequence


class GestureMode(Enum):
    NONE = "none"
    POINT = "point"
    PINCH = "pinch"
    SCROLL = "scroll"


@dataclass(frozen=True)
class GestureConfig:
    pinch_on: float = 0.045
    pinch_off: float = 0.065


def _dist(a, b) -> float:
    return math.hypot(a.x - b.x, a.y - b.y)


def finger_up(landmarks: Sequence, tip_id: int, pip_id: int) -> bool:
    return landmarks[tip_id].y < landmarks[pip_id].y


def classify_simple(landmarks: Sequence, config: GestureConfig | None = None) -> GestureMode:
    """Easy mode: move with index tip; pinch to click. No finger pose required."""
    cfg = config or GestureConfig()
    if _dist(landmarks[4], landmarks[8]) < cfg.pinch_on:
        return GestureMode.PINCH
    return GestureMode.POINT


def classify(landmarks: Sequence, config: GestureConfig | None = None) -> GestureMode:
    """Classify the current hand pose into a control mode."""
    cfg = config or GestureConfig()

    index_up = finger_up(landmarks, 8, 6)
    middle_up = finger_up(landmarks, 12, 10)
    ring_up = finger_up(landmarks, 16, 14)
    pinky_up = finger_up(landmarks, 20, 18)
    pinch_dist = _dist(landmarks[4], landmarks[8])

    if index_up and middle_up and not ring_up and not pinky_up:
        return GestureMode.SCROLL

    if pinch_dist < cfg.pinch_on:
        return GestureMode.PINCH

    if index_up and not middle_up:
        return GestureMode.POINT

    return GestureMode.NONE


def pinch_distance(landmarks: Sequence) -> float:
    return _dist(landmarks[4], landmarks[8])
