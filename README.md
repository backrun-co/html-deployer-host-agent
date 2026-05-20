# html-deployer-host-agent

**Host Agent PHP** for [HTML Deployer](https://backrun.co/html-deployer/) — a single `backrun.php` you upload to **your own PHP hosting**. The Chrome extension sends HTML here; this script saves `.html` files next to itself (no shell, no remote code load).

> **GitHub does not run PHP.** This repo is only **source / download**. You must copy `backrun.php` to a real web server (cPanel, Hostinger, VPS, etc.) over HTTPS.

---

## Quick start

1. **Download** `backrun.php` from this repo (Raw file or Releases).
2. **Secret key** — the file includes a **32-character default** you can use immediately (copy from `SECRET_KEY` in `backrun.php`, or use this value):

   ```
   BACKRUNDEFAULTHOSTAGENTSECRETS32
   ```

   Paste it into the extension under **Options → Host Agent → Secret key**. Optional: replace it with your own random string (32+ characters) in **both** `backrun.php` and the extension.
3. **Upload** the file to your site, e.g. `https://example.com/backrun.php`.
4. In the extension: **Extension options → Host Agent →** set **Agent URL** (your `backrun.php` link), **Secret key** (step 2) → **Test Connection** → Save.

**Other download:** From the Chrome extension — **Options → Host Agent → Download agent script directly** — you get a fresh `backrun.php` with a **random** key already filled in (different from the GitHub default above).

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
- The **default key in this repo is public** (same for everyone who clones GitHub). Fine for quick tests; for a live site, **generate your own** key in `backrun.php` and in the extension.
- Prefer **HTTPS** for the Agent URL.
- After changing `SECRET_KEY` on the server, update the same value in the extension.

---

## License

Use and modify for your own hosting in connection with HTML Deployer. If you redistribute, keep notices that make the origin and security expectations clear to users.

---

## Links

- **HTML Deployer** (product & docs): [backrun.co/html-deployer/](https://backrun.co/html-deployer/)
- **Chrome extension**: install from the Chrome Web Store page linked on the site above.
