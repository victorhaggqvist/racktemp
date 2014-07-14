Sensor Doc's
============
Test of sensor (DS18B20) output, default config aka just plugged in.

```sh
pi@raspberrypi ~ $ cat /sys/bus/w1/devices/28-000005060fe3/w1_slave
7e 01 4b 46 7f ff 02 10 25 : crc=25 YES
7e 01 4b 46 7f ff 02 10 25 t=23875
```
Temp 23.875C

```sh
pi@raspberrypi ~ $ cat /sys/bus/w1/devices/28-000005060fe3/w1_slave
8d 00 4b 46 7f ff 03 10 3e : crc=3e YES
8d 00 4b 46 7f ff 03 10 3e t=8812
```
Temp 8.812C
