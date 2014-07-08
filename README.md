RackTemp
========
A temprature monitoring application for Raspberry Pi, built with the DS18B20 sensor. This project is still in early development and I will hopefully have time to add lots of more fun stuff.

##Requirements
- A Raspberry Pi
- A DS18B20 sensor to read from, head over to Adafruits [tutorial](http://learn.adafruit.com/adafruits-raspberry-pi-lesson-11-ds18b20-temperature-sensing) for how to hook it up

##Install

1. Install required packages
```sh
$ sudo apt-get install -y git apache2 php5 php5-gd php5-curl php5-mysql mysql-server whois
```

2. Get latest RackTemp
```sh
git clone https://github.com/victorhaggqvist/racktemp.git racktemp #download the application
cd racktemp

sudo mv racktemp/* /var/www/  #move application to web root
sudo rm -r racktemp/
```

For a quick and easy install just download and run the installer script, [install.sh](https://raw.github.com/victorhaggqvist/racktemp/master/install.sh). It will essentially download the application and install MySQL and Apache.

Type the following in your Pi's terminal to download and run the script
```sh
$ wget https://raw.github.com/victorhaggqvist/racktemp/master/install.sh
$ chmod +x install.sh && ./install.sh
```

##FAQ
Q: How to add new users?

A: Since RackTemp make use of the linux users, add a user by this shell command

```sh
$ sudo adduser [username] --no-create-home
```

##Disclaimer
RackTemp is provided as is without any warranty. I'm not responsible for any damage this software may cause.
