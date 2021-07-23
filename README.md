# Alua Backend

## Docker based env
## Setup Project
Please follow the steps:
```bash
cd {WEB_ROOT_DIR}
mkdir "aparlay"
cd aparlay
git clone git@github.com:aparlay/core.git
git clone git@github.com:aparlay/alua.git
git clone git@github.com:aparlay/waptap.git
cd core
composer install
cd ../alua
composer install
mkdir -p packages/Aparlay
cd packages/Aparlay
ln -s ../../../core Core

cd {WEB_ROOT_DIR}/alua
./vendor/bin/sail build --no-cache
./vendor/bin/sail up -d
```

## Technology stack requirements

### Nginx

### PHP 8+

```bash
sudo apt update
sudo apt -y upgrade
sudo apt install lsb-release ca-certificates apt-transport-https software-properties-common -y
sudo add-apt-repository ppa:ondrej/php
sudo apt update
sudo apt install php8.0 php8.0-amqp php8.0-common php8.0-gd php8.0-sqlite3 \
php8.0-xsl php8.0-apcu php8.0-curl php8.0-gmp php8.0-opcache php8.0-redis \
php8.0-igbinary php8.0-mbstring php8.0-bcmath php8.0-dev php8.0-imagick \
php8.0-memcached php8.0-uuid php8.0-zip php8.0-bz2 php8.0-imap php8.0-mysql \
php8.0-psr php8.0-cli php8.0-fpm php8.0-intl php8.0-oauth php8.0-xml
php --version
sudo pecl install swoole
```

### MonogoDB 4.4+
```bash
sudo apt install gnupg
wget -qO - https://www.mongodb.org/static/pgp/server-4.4.asc | sudo apt-key add -
echo "deb [ arch=amd64,arm64 ] https://repo.mongodb.org/apt/ubuntu focal/mongodb-org/4.4 multiverse" | sudo tee /etc/apt/sources.list.d/mongodb-org-4.4.list
sudo apt update
sudo apt install -y mongodb-org
```
### Redis 6+
```bash
sudo apt update
sudo apt install redis-server
```

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
## Setup Project

Please follow the steps:

```bash
cd {WEB_ROOT_DIR}
mkdir "aparlay"
git clone git@github.com:aparlay/core.git
git clone git@github.com:aparlay/alua.git
git clone git@github.com:aparlay/waptap.git
cd core
composer install
php artisan db:seed
cd ../alua
composer install
mkdir -p packages/Aparlay
cd packages/Aparlay
ln -s ../../../core Core

php artisan octane:start --watch
```
