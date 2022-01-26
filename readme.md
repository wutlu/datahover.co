$ php artisan queue:work --queue=elasticsearch
$ php artisan queue:work --queue=notifications
$ php artisan queue:work --queue=twitter
$ php artisan queue:work --queue=newsDetector
$ php artisan queue:work --queue=newsTaker
$ php artisan queue:work --queue=paymentCheck
$ php artisan queue:work --queue=feeds

sudo apt-get update
sudo apt-get upgrade
sudo apt install software-properties-common
sudo add-apt-repository ppa:ondrej/php
sudo apt-get update
sudo apt-get install apache2 git php8.0 curl postgresql redis-server supervisor unzip
sudo apt-get install php8.0-dom php8.0-mbstring php8.0-bcmath php8.0-curl php8.0-cli php8.0-gd php8.0-zip php8.0-redis php8.0-pgsql

sudo a2enmod rewrite php8.0
sudo service apache2 restart

curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer

curl -fsSL https://deb.nodesource.com/setup_current.x | sudo -E bash -
sudo apt-get install -y nodejs

// Redis
sudo nano /etc/redis/redis.conf
maxmemory 2048mb
maxmemory-policy allkeys-lru

sudo systemctl restart redis-server.service
sudo systemctl enable redis-server.service

// PostgreSQL
sudo -u postgres psql

postgres=# \password
postgres=# (New Password)
postgres=# (New Password Repeat)
postgres=# CREATE SCHEMA datahover.co AUTHORIZATION postgres;
postgres=# \q

sudo nano /etc/postgresql/10/main/postgresql.conf
max_connections = 5000

nano /etc/apache2/sites-available/datahover.co.conf
<VirtualHost *:80>
    ServerName datahover.co
    ServerAlias www.datahover.co

    ServerAdmin webmaster@localhost
    DocumentRoot /var/www/datahover.co/public

    ErrorLog ${APACHE_LOG_DIR}/error.log
    CustomLog ${APACHE_LOG_DIR}/access.log combined

    <Directory /var/www/datahover.co/>
        Options Indexes FollowSymLinks
        AllowOverride all
        Require all granted
    </Directory>
</VirtualHost>

sudo a2ensite datahover.co
service apache2 reload
