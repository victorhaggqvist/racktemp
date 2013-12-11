RackTemp
========
A temprature monitoring application for Raspberry Pi, buildt with the DS18B20 sensor

##Requirements
- A Raspberry Pi
- A DS18B20 sensor to read from, head over to Adafruits [tutorial](http://learn.adafruit.com/adafruits-raspberry-pi-lesson-11-ds18b20-temperature-sensing) for how to hook it up

##Install
For a quick and easy install just download the installer script, [install.sh](https://raw.github.com/victorhaggqvist/racktemp/master/install.sh). It will essentially download the application and install MySQL and Apache.

##FAQ
Q: How to add new users?

A: Since RackTemp make use of the linux users, add a user by this shell command

```sh
$ sudo adduser [username] --no-create-home
```
