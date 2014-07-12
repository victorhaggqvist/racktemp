RackTemp
========
A temprature monitoring application for Raspberry Pi, built with the DS18B20 sensor. This project is still in early development and I will hopefully have time to add lots of more fun stuff.

##Requirements
- A Raspberry Pi
- A DS18B20 sensor to read from, head over to Adafruits [tutorial](http://learn.adafruit.com/adafruits-raspberry-pi-lesson-11-ds18b20-temperature-sensing) for how to hook it up

##Install

###0 Install required packages (and make sure you are up-to-date)
You will be asked to enter the root password for MySQL, make sure to remember it.
```sh
sudo apt-get update; sudo apt-get -y upgrade
sudo apt-get -y install git nginx php5 php5-fpm php5-mysql php5-curl php5-cli mysql-server whois
```

###1 Get latest RackTemp
```sh
cd; git clone https://github.com/victorhaggqvist/racktemp.git racktemp
```

###2 Database config
Remember the MySQL root password you just entered? Hopefully. Now create a database.

Replace brackets with appropriate info.
```sh
#enter mysql promp, notice no space after '-p'
mysql -u root -p[root password]
```
```sql
CREATE DATABASE racktemp;
```
Create a new MySQL user for RackTemp.
```sql
CREATE USER 'racktemp'@'localhost' IDENTIFIED BY '[racktemp password]';
GRANT SELECT,INSERT,UPDATE,DELETE,CREATE,DROP ON racktemp.* TO 'racktemp'@'localhost';
```
Initiate Database
```sql
source /home/pi/racktemp/bootstrap.sql;
```

Type `\q` in the mysql prompt to exit it.

Edit the file `/home/pi/racktemp/app/lib/config.inc` to correspond with the user you just created.

You might also want to run `mysql_secure_installation` to make the your install a bit more secure.

###3 Nginx config
```sh
sudo rm /etc/nginx/sites-enabled/default
sudo ln -s /home/pi/racktemp/racktemp.conf /etc/nginx/sites-enabled/racktemp.conf
sudo update-rc.d nginx defaults
sudo usermod -a -G shadow www-data
```
This will give you RackTemps web UI over regular HTTP. But if you are like me you will likely want to have HTTPS too. If you don't care about HTTPS you could jump right to step 4.

Here is how to enable https.

First of you will need a Key and a Certificate (and well a CSR too). The following will give you a selfsigned cert.
```
mkdir -p /home/pi/racktemp/ssl; cd /home/pi/racktemp/ssl
openssl genrsa -out racktemp.key 4096
openssl req -new -key racktemp.key -out racktemp.csr -subj "/C=SE/ST=Some State/O=Foo/CN=example.com/"
openssl x509 -req -in racktemp.csr -out racktemp.crt -signkey racktemp.key -days 1000
```

Now you will need to uncomment the https server part of the config file.

Open `/home/pi/racktemp/racktemp.conf` and uncomment the server section in the bottom of the file with `listen 443` in it. You might also uncomment the redirect on line 17.

###4 Timezone
Make sure you have your timezone set correctly. Otherwise you will get strange stats.

If you want to auto set it, run follworing in the terminal.
```sh
cd; git clone https://github.com/victorhaggqvist/tzupdate.git
export TZ=$(./tzupdate/tzupdate -p)
sudo echo "date.timezone = '${TZ}'" | sudo tee -a /etc/php5/fpm/php.ini
sudo echo ${TZ} | sudo tee -a /etc/timezone
sudo cp /usr/share/zoneinfo/${TZ} /etc/localtime
echo "Timezone is set to ${TZ}"
```

###5 Enable Sensors
```sh
sudo sh -c 'echo "w1-gpio" >> /etc/modules'
sudo sh -c 'echo "w1-therm" >> /etc/modules'
```

###6 Add cron job
```sh
crontab -l > crons
echo "*/5 * * * * php /home/pi/racktemp/cron.php >> /dev/null" >> crons
crontab crons
rm crons
```

###7 Install RackTemp dependencies
```sh
cd; cd racktemp; curl -sS https://getcomposer.org/installer | php
php composer.phar install
```

###8 Reboot
Now reboot your Raspberry Pi to make all changes go through.
```sh
sudo reboot
```

##FAQ
**How to add new users?**
Since RackTemp make use of the linux users, add a user by this shell command

```sh
$ sudo adduser [username] --no-create-home
```


#License
GPLv3
