# Hosting the real-time notification server (server.js)

The app uses a small **Node.js server** (`server.js`) for real-time notifications and store communication (Socket.IO). You can host it in two ways.

---

## Option 1: Same server as Laravel (recommended for most cases)

Run `server.js` on the **same machine** as your Laravel app.

1. **On your server**, install Node.js (v18+), then run the notification server:
   ```bash
   cd /path/to/stockmgnt
   node server.js
   ```
   It will listen on `NOTIFICATION_PORT` (default 3050).

2. **Keep it running** with a process manager (e.g. PM2):
   ```bash
   npm install -g pm2
   pm2 start server.js --name "stockmgnt-notify"
   pm2 save && pm2 startup
   ```

3. **.env on the server** (same machine, so keep URL as localhost or use your domain with port):
   ```env
   NOTIFICATION_PORT=3050
   NOTIFICATION_SERVER_URL=
   ```
   Leave `NOTIFICATION_SERVER_URL` empty to use `http://localhost:3050`.

4. **Firewall**: Open port 3050 if the frontend (browser) will connect from outside. If you put the app behind **Nginx** (see below), you don’t need to expose 3050 publicly.

### Optional: Nginx reverse proxy (same server)

So users connect to `https://yourdomain.com` (no port), you can proxy Socket.IO to the Node app:

```nginx
# In your server block for yourdomain.com
location /socket.io/ {
    proxy_pass http://127.0.0.1:3050;
    proxy_http_version 1.1;
    proxy_set_header Upgrade $http_upgrade;
    proxy_set_header Connection "upgrade";
    proxy_set_header Host $host;
}
```

Then set in **.env** and in **server.js** (allowed origins):

- `NOTIFICATION_SERVER_URL=https://yourdomain.com`
- In `server.js` add `https://yourdomain.com` to `allowedOrigins` and run Node on 3050 (no HTTPS on Node; Nginx handles SSL).

---

## Option 2: Different server (separate host for Node)

You can run `server.js` on **another server** (e.g. a small VPS).

1. **On the second server**: Clone/copy the project (or at least `server.js`, `package.json`, `.env`) and run:
   ```bash
   node server.js
   ```
   (Again, use PM2 or similar so it stays running.)

2. **.env on the Laravel server** (main app):
   ```env
   NOTIFICATION_PORT=3050
   NOTIFICATION_SERVER_URL=https://notify.yourdomain.com
   ```
   Use the **public URL** of the Node server (with HTTPS if the site is HTTPS).

3. **.env on the Node server** (optional): Set `NOTIFICATION_PORT` if you use something other than 3050.

4. **server.js CORS**: In `allowedOrigins` (and Express `cors`), add your Laravel site origin, e.g.:
   - `https://yourdomain.com`
   - `https://www.yourdomain.com`

5. **SSL**: If the main site is HTTPS, the notification URL should be HTTPS as well (use a reverse proxy or SSL on the Node server).

---

## Summary

| Setup              | NOTIFICATION_SERVER_URL (Laravel .env) | Where server.js runs      |
|--------------------|----------------------------------------|---------------------------|
| Same server        | Empty (uses localhost:3050)            | Same machine as Laravel   |
| Same server + Nginx| `https://yourdomain.com`               | Same machine, Nginx proxy |
| Different server   | `https://notify.yourdomain.com`        | Second server/VPS        |

You do **not** have to use a separate server; running `server.js` on the same host as Laravel is enough for most deployments.
