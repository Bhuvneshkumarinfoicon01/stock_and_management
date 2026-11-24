1️⃣ Start Nginx on Linux (Ubuntu / Debian / most servers)

If Nginx was installed using a package manager:

sudo systemctl start nginx
Other useful commands
sudo systemctl stop nginx        # Stop server
sudo systemctl restart nginx     # Restart server
sudo systemctl reload nginx      # Reload config without downtime
sudo systemctl status nginx      # Check status
2️⃣ Start Nginx manually (binary command)

If you installed it manually or compiled from source:

sudo nginx

Reload configuration:

sudo nginx -s reload

Stop it:

sudo nginx -s stop
3️⃣ Start Nginx on macOS (installed via Homebrew)
brew services start nginx

Or run directly:

nginx
4️⃣ Start Nginx on Windows

Go to the folder where Nginx is installed and run:

start nginx

Or double-click nginx.exe.

Stop:

nginx -s stop

✅ Check if Nginx is running

Open a browser and go to:

http://localhost

You should see the "Welcome to nginx!" page.

💡 If you want, tell me:

your OS (Ubuntu / Windows / Mac / Docker / WSL)

how you installed Nginx

…and I can give the exact steps for your setup.

//================================================
1️⃣ Default location (Ubuntu / Debian)

Usually it is here:

/etc/nginx/sites-available

Check it with:

ls /etc/nginx/sites-available

You will normally see something like:

default
2️⃣ Enabled sites folder

Nginx actually serves configs from sites-enabled which are symbolic links to sites-available.

/etc/nginx/sites-enabled

Check it:

ls /etc/nginx/sites-enabled

You might see:

default -> /etc/nginx/sites-available/default
3️⃣ View the default site configuration
sudo nano /etc/nginx/sites-available/default

Inside you will find something like:

server {
    listen 80;
    server_name _;
    root /var/www/html;
    index index.html index.htm;
}

So the website files are served from:

/var/www/html
4️⃣ Test the running server

Open in browser:

http://YOUR_SERVER_IP

or locally:

http://localhost

You should see “Welcome to nginx!”.

💡 If you want, I can also show you:

How to create a new site in Nginx (step-by-step)

How to host multiple domains

How to connect Nginx with Node.js / React / PHP