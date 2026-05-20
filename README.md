# html-deployer-host-agent

**Host Agent PHP** for [HTML Deployer](https://backrun.co/html-deployer/) — a single `backrun.php` you upload to **your own PHP hosting**. The Chrome extension sends HTML here; this script saves `.html` files next to itself (no shell, no remote code load).

> **GitHub does not run PHP.** This repo is only **source / download**. You must copy `backrun.php` to a real web server (cPanel, Hostinger, VPS, etc.) over HTTPS.

---

## Quick start

1. **Download** `backrun.php` from this repo (Raw file or Releases).
2. **Edit** `SECRET_KEY` in the file — use a long random string (32+ characters). Do **not** use the example placeholder on a public server.
3. **Upload** the file to your site, e.g. `https://example.com/backrun.php`.
4. In the extension: **Extension options → Host Agent →** paste **Agent URL** and the same **Secret key** → **Test Connection** → Save.

You can also generate a fresh script (with a random key already inserted) from the extension: **Options → Host Agent → Download agent script** (served from the product server).

---

## What the script does

| Action   | Purpose |
|----------|---------|
| `ping`   | Health check; extension expects `agent: html-deployer-host-agent`. |
| `deploy` | Saves `filename.html` in the same directory as `backrun.php`. |
| `list`   | Lists `*.html` in that directory. |
| `delete` | Removes one `.html` file (safe path checks). |

Max payload size is defined in the file (default 2 MB).

---

## Security

- Treat `SECRET_KEY` like a password. Anyone with the key can deploy HTML to your folder.
- Prefer **HTTPS** for the Agent URL.
- After changing `SECRET_KEY` on the server, update the same value in the extension.

---

## License

Use and modify for your own hosting in connection with HTML Deployer. If you redistribute, keep notices that make the origin and security expectations clear to users.

---

## Links

- **HTML Deployer** (product & docs): [backrun.co/html-deployer/](https://backrun.co/html-deployer/)
- **Chrome extension**: [Install](https://chromewebstore.google.com/detail/html-deployer-1-click-ai/gihmknkabkkghpiocgnoiejagngdegea).
