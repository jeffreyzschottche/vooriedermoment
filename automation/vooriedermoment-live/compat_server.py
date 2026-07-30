#!/usr/bin/env python3
"""Local, read-only compatibility bridge for the existing VIM Keysmith macro."""

from __future__ import annotations

import argparse
import json
from http.server import BaseHTTPRequestHandler, ThreadingHTTPServer
from pathlib import Path


def make_handler(root: Path):
    class Handler(BaseHTTPRequestHandler):
        server_version = "VoorIederMomentCompat/1.0"

        def log_message(self, fmt: str, *args: object) -> None:
            with (root / "compat-server.log").open("a", encoding="utf-8") as log:
                log.write((fmt % args) + "\n")

        def send_json(self, status: int, payload: dict[str, object]) -> None:
            body = json.dumps(payload, ensure_ascii=False).encode("utf-8")
            self.send_response(status)
            self.send_header("Content-Type", "application/json; charset=utf-8")
            self.send_header("Content-Length", str(len(body)))
            self.end_headers()
            self.wfile.write(body)

        def current_order(self) -> dict[str, object] | None:
            path = root / "current.json"
            if not path.exists():
                return None
            with path.open(encoding="utf-8") as source:
                return json.load(source)

        def do_GET(self) -> None:  # noqa: N802
            if self.path == "/health":
                self.send_json(200, {"ok": True})
                return
            if self.path == "/api/v1/orders/export":
                order = self.current_order()
                self.send_json(200, {"orders": [order] if order else []})
                return
            self.send_json(404, {"error": "Not found"})

        def do_POST(self) -> None:  # noqa: N802
            if self.path == "/api/v1/orders/export":
                order = self.current_order()
                self.send_json(200, {"orders": [order] if order else []})
                return
            if self.path == "/api/v1/orders/export/ack":
                # De live claim blijft leidend. De oude ack mag de order niet
                # afronden voordat vier previews succesvol zijn geüpload.
                self.send_json(200, {"ok": True, "acknowledged": False})
                return
            self.send_json(404, {"error": "Not found"})

    return Handler


def main() -> None:
    parser = argparse.ArgumentParser()
    parser.add_argument("--root", required=True, type=Path)
    parser.add_argument("--port", default=8000, type=int)
    args = parser.parse_args()
    args.root.mkdir(parents=True, exist_ok=True)
    server = ThreadingHTTPServer(("127.0.0.1", args.port), make_handler(args.root))
    server.serve_forever()


if __name__ == "__main__":
    main()
