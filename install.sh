#!/bin/bash
echo "RackTemp installler"
echo "This installer will take you through the entire process of installing RackTemp"
echo "Press [Enter] to begin..."
read FOO

echo "MySQL setup.."
echo -n "Enter a MySQL root password: "
read MYSQL_ROOT_PW
while [[ -z "$MYSQL_ROOT_PW" ]]; do
  echo -n "Enter a MySQL root password: "
  read MYSQL_ROOT_PW
done

echo "Installing package dependencies..."
echo "mysql-server-5.5 mysql-server/root_password password ${MYSQL_ROOT_PW}" | sudo debconf-set-selections
echo "mysql-server-5.5 mysql-server/root_password_again password ${MYSQL_ROOT_PW}" | sudo debconf-set-selections

echo "Getting system up-to-date.."
sudo apt-get -qq update
sudo apt-get -qq upgrade
echo "Installing packages.."
sudo apt-get -qq install git nginx php5 php5-fpm php5-mysql php5-curl php5-cli mysql-server-5.5 whois expect vim

echo "Getting latest RackTemp.."
cd; git clone https://github.com/victorhaggqvist/racktemp.git racktemp
cd racktemp
git checkout dev

echo "Configuring RackTemp.."
RACKTEMP_PW=$(tr -dc A-Za-z0-9_ < /dev/urandom | head -c 20)
SQL=$(cat << EOF
CREATE DATABASE  IF NOT EXISTS racktemp;
CREATE USER 'racktemp'@'localhost' IDENTIFIED BY '${RACKTEMP_PW}';
GRANT SELECT,INSERT,UPDATE,DELETE,CREATE,DROP ON racktemp.* TO 'racktemp'@'localhost';
source /home/pi/racktemp/configs/bootstrap.sql;
EOF)
echo "$SQL" | mysql -u root -p${MYSQL_ROOT_PW}

SECURE_MYSQL=$(expect -c "

set timeout 10
spawn mysql_secure_installation

expect \"Enter current password for root (enter for none):\"
send \"$MYSQL_ROOT_PW\r\"

expect \"Change the root password?\"
send \"n\r\"

expect \"Remove anonymous users?\"
send \"y\r\"

expect \"Disallow root login remotely?\"
send \"y\r\"

expect \"Remove test database and access to it?\"
send \"y\r\"

expect \"Reload privilege tables now?\"
send \"y\r\"

expect eof
")

echo "$SECURE_MYSQL"

cat /home/pi/racktemp/app/lib/config.inc.sample | sed "s/SillyPassword/${RACKTEMP_PW}/" > /home/pi/racktemp/app/lib/config.inc

echo "Configure Nginx..."
sudo rm /etc/nginx/sites-enabled/default
sudo ln -sf /home/pi/racktemp/configs/racktemp.conf /etc/nginx/sites-enabled/racktemp.conf
sudo update-rc.d nginx defaults
sudo usermod -a -G shadow www-data

echo -n "Do you want to enable HTTPS acess to RackTemp? [Y/n]:"
read response

if [[ $response =~ ^([yY][eE][sS]|[yY]|)$ ]]; then
  echo "HTTPS config.."
  conf=$(head -n 21 configs/racktemp.conf)
  sslstrip=$(tail -n 16 configs/racktemp.conf | cut -c2-)
  echo -e "$conf \n $sslstrip" > configs/racktemp.conf

  echo "Generating certificate.."
  mkdir -p /home/pi/racktemp/ssl; cd /home/pi/racktemp/ssl
  openssl genrsa -out racktemp.key 4096
  openssl req -new -key racktemp.key -out racktemp.csr -subj "/C=SE/ST=Some State/O=Foo/CN=example.com/"
  openssl x509 -req -in racktemp.csr -out racktemp.crt -signkey racktemp.key -days 1000
fi

echo -n "Do you want to update your Raspberry Pi's timezone according to your location? [Y/n]: "
read response

if [[ $response =~ ^([yY][eE][sS]|[yY]|)$ ]]; then
  echo "Updateing timezone"
  cd; git clone https://github.com/victorhaggqvist/tzupdate.git
  export TZ=$(./tzupdate/tzupdate -p)
  sudo echo "date.timezone = '${TZ}'" | sudo tee -a /etc/php5/fpm/php.ini
  sudo echo ${TZ} | sudo tee -a /etc/timezone
  sudo cp /usr/share/zoneinfo/${TZ} /etc/localtime
  echo "Timezone is set to ${TZ}"
fi

echo "Loading sensor drivers.."
sudo sh -c 'echo "w1-gpio" >> /etc/modules'
sudo sh -c 'echo "w1-therm" >> /etc/modules'

echo "Adding cronjob.."
crontab -l > crons
echo "*/5 * * * * php /home/pi/racktemp/cron.php >> /dev/null" >> crons
crontab crons
rm crons

echo "Installing Composer dependencies.."
cd; cd racktemp; curl -sS https://getcomposer.org/installer | php
php composer.phar install

echo "Installation done"
echo "You should reboot your Raspberry Pi now."
echo "Rebooting in 15 sec. Press Ctrl-C to abort and reboot manualy later".
echo "After the reboot, open $(ifconfig eth0 | grep 'inet addr' | cut -d: -f2 | awk '{ print $1}') in your browser to meet RackTemp"
sleep 15
sudo reboot
