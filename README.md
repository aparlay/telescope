## Setup project

Please follow the steps:

```bash
cd {WEB_ROOT_DIR}
mkdir "aparlay"
git clone git@github.com:aparlay/core.git
git clone git@github.com:aparlay/alua.git
git clone git@github.com:aparlay/waptap.git
cd core
composer install
cd ../alua
composer install
mkdir -p packages/Aparlay
ln -s {WEB_ROOT_DIR}/core {WEB_ROOT_DIR}/alua/packages/Aparlay/Core

php artisan octane:start --watch
```
## Technology stack requirements

- Nginx
- PHP 8+
- MonogoDB 4.4+
- Redis 6+


## Generate Self Signed SSL Certification

```bash
mkdir -p /path/to/ca
cd /path/to/ca
openssl dhparam -out /etc/nginx/ssl/dhparam.pem 2048
openssl req -x509 -nodes -new -sha512 -days 365 -newkey rsa:4096 -keyout ca.key -out ca.pem -subj "/C=IR/CN=Aparlay CA/O=Aparlay, Ltd."
openssl x509 -in ca.pem -text -noout
openssl x509 -outform pem -in ca.pem -out ca.crt
cat > v3.ext <<-EOF
authorityKeyIdentifier=keyid,issuer
basicConstraints=CA:FALSE
keyUsage = digitalSignature, nonRepudiation, keyEncipherment, dataEncipherment
subjectAltName = @alt_names
[alt_names]
# Local hosts
DNS.1 = localhost
DNS.2 = 127.0.0.1
DNS.3 = ::1
# List your domain names here
DNS.4 = waptap.test
DNS.5 = app.waptap.test
DNS.6 = web.waptap.test
DNS.7 = www.waptap.test
DNS.8 = admin.waptap.test
DNS.9 = api.waptap.test
DNS.10 = upload.waptap.test
DNS.11 = alua.test
DNS.12 = app.alua.test
DNS.13 = web.alua.test
DNS.14 = www.alua.test
DNS.15 = admin.alua.test
DNS.16 = api.alua.test
DNS.17 = upload.alua.test
EOF
openssl req -new -nodes -newkey rsa:4096 -keyout aparlay.key -out aparlay.csr -subj "/C=US/ST=Washington/L=Seattle/O=Aparlay, Ltd./CN=aparlay"
openssl x509 -req -sha512 -days 365 -extfile v3.ext -CA ca.crt -CAkey ca.key -CAcreateserial -in aparlay.csr -out aparlay.crt
openssl dhparam -out /etc/nginx/ssl-dhparams.pem 4096
```

## Setup Nginx

```bash
sudo nano /etc/nginx/sites-available/api.alua.test
```

```nginx
server {
    ssl_certificate /path/to/ca/aparlay.crt;
    ssl_certificate_key /path/to/ca/aparlay.key;
    ssl_session_cache shared:le_nginx_SSL:10m;
    ssl_session_timeout 1d;
    ssl_session_tickets off;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_prefer_server_ciphers off;
    ssl_ciphers "ECDHE-ECDSA-AES128-GCM-SHA256:ECDHE-RSA-AES128-GCM-SHA256:ECDHE-ECDSA-AES256-GCM-SHA384:ECDHE-RSA-AES256-GCM-SHA384:ECDHE-ECDSA-CHACHA20-POLY1305:ECDHE-RSA-CHACHA20-POLY1305:DHE-RSA-AES128-GCM-SHA256:DHE-RSA-AES256-GCM-SHA384";
    ssl_dhparam /etc/nginx/ssl-dhparams.pem;
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains; preload" always;

    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name api.alua.test;
    server_tokens off;
    root /path/to/alua/public;

    index index.php;

    charset utf-8;

    location /index.php {
        try_files /not_exists @octane;
    }

    location / {
        try_files $uri $uri/ @octane;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    access_log off;
    error_log  /path/to/alua/storage/logs/nginx-error.log error;

    error_page 404 /index.php;

    location @octane {
        set $suffix "";

        if ($uri = /index.php) {
            set $suffix ?$query_string;
        }

        proxy_http_version 1.1;
        proxy_set_header Host $http_host;
        proxy_set_header Scheme $scheme;
        proxy_set_header SERVER_PORT $server_port;
        proxy_set_header REMOTE_ADDR $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection $connection_upgrade;

        proxy_pass http://127.0.0.1:8000$suffix;
    }
}
server {
    listen 80;
    listen [::]:80;
    server_name api.alua.test;
    return 301 https://$host$request_uri;
}
```
