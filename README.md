# RackTemp

A temperature monitoring application for Raspberry Pi, built with the DS18B20 sensor.

## Requirements
- A Raspberry Pi
- A DS18B20 sensor to read from, head over to Adafruit's [tutorial](http://learn.adafruit.com/adafruits-raspberry-pi-lesson-11-ds18b20-temperature-sensing) for how to hook it up. You can get a pack of 5 for like $4-5 on ebay.
- 4.7K resistor(s), please referrer to Adafruit's tutorial for the how and why.

### Note
As of some time Q1 2015 there is also a boot config change needed to be done to work with the DS18B20 sensor.

The change to be done on `/boot/config.txt` is to add `dtoverlay=w1-gpio` to the end.

Here is a one-liner:
```
echo "dtoverlay=w1-gpio" | sudo tee -a /boot/config.txt
```

You may read more [here](https://www.raspberrypi.org/forums/viewtopic.php?f=28&t=97314), [here](https://www.raspberrypi.org/forums/viewtopic.php?f=37&t=98407) and [here](https://raspberrypi.stackexchange.com/a/27570/4407)

## Setup

### Install required packages
You will be asked to enter the root password for MySQL, make sure to remember it.

```sh
sudo apt-get update; sudo apt-get -y upgrade
sudo apt-get -y install git nginx php5 php5-fpm php5-mysql php5-curl php5-cli mysql-server whois unzip
curl -sS https://getcomposer.org/installer | php -- --filename=composer
```

### Get latest RackTemp

    git clone https://github.com/victorhaggqvist/racktemp.git racktemp

### Composer install
Fill the stuff you are prompted for. You may just ignore the stuff about `mailer_`.

    ./composer install

### Database config

```sh
app/console doctrine:database:create
app/console doctrine:schema:create
```

Edit the file `app/config/parameters.yml` to correspond with the user you just created.

You might also want to run `mysql_secure_installation` to make the your install a bit more secure.

### Nginx config
```sh
sudo rm /etc/nginx/sites-enabled/default
sudo ln -s /home/pi/racktemp/configs/racktemp.conf /etc/nginx/sites-enabled/racktemp.conf
sudo update-rc.d nginx defaults
sudo usermod -a -G shadow www-data
```

### Symfony
Make sure Symfony can write to the log and cache dirs.
```sh
chown -R someuser:www-data app/logs/
chown -R someuser:www-data app/cache/
```

### Setting up HTTPS
You may optionally setup access to RackTemp over HTTPS, default is just regular HTTP.

First of you will need a Key and a Certificate (and well a CSR too). The following will give you a selfsigned cert.
```sh
openssl genrsa -out racktemp.key 4096
openssl req -new -key racktemp.key -out racktemp.csr -sha512 -subj "/C=SE/ST=Some State/O=Foo/CN=example.com/"
openssl x509 -req -in racktemp.csr -out racktemp.crt -signkey racktemp.key -days 1000
```

Now you will need to uncomment the https server part of the config file.

Open `/home/pi/racktemp/configs/racktemp.conf` and uncomment the server section in the bottom of the file with `listen 443` in it. You might also uncomment the redirect on line 17.

### Timezone
Make sure you have your timezone set correctly. Otherwise you will get strange stats.

If you want to auto set it, run following in the terminal.
```sh
cd; git clone https://github.com/victorhaggqvist/tzupdate.git
export TZ=$(./tzupdate/tzupdate -p)
sudo echo "date.timezone = '${TZ}'" | sudo tee -a /etc/php5/fpm/php.ini
sudo echo ${TZ} | sudo tee -a /etc/timezone
sudo cp /usr/share/zoneinfo/${TZ} /etc/localtime
echo "Timezone is set to ${TZ}"
```

### Enable Sensors
```sh
sudo sh -c 'echo "w1-gpio" >> /etc/modules'
sudo sh -c 'echo "w1-therm" >> /etc/modules'
```

### Add cron job
```sh
crontab -l > crons
echo "*/5 * * * * php /home/pi/racktemp/app/console racktemp:cron >> /dev/null" >> crons
crontab crons
rm crons
```

### Reboot
Now reboot your Raspberry Pi to make all changes go through.
```sh
sudo reboot
```

## FAQ
**How to add new users?**
Since RackTemp make use of the linux users, add a user by this shell command

```sh
sudo adduser [username] --no-create-home
```

## Credits
Image on the signin-page us from unsplash.com, https://unsplash.com/photos/bW2vHKCxbx4 by Dmitry Sytnik 

## License

    RackTemp - Temperature monitoring for Raspberry Pi
    Copyright (C) 2012 - 2015  Victor Häggqvist

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
