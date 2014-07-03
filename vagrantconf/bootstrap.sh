#!/usr/bin/env bash
sudo debconf-set-selections <<< 'mysql-server-5.5 mysql-server/root_password password root'
sudo debconf-set-selections <<< 'mysql-server-5.5 mysql-server/root_password_again password root'

sudo apt-get update
sudo apt-get -y updgrade
sudo apt-get -y install nginx php5 php5-fpm php5-mysql php5-gd vim mysql-server-5.5 php5-curl php5-cli screen

# bind mysql to all
cat /etc/mysql/my.cnf | sed 's/bind-address/#bind-address/' > mymod.cnf
sudo cp mymod.cnf /etc/mysql/my.cnf
rm mymod.cnf

# reset mysql root user to get acces outside vb
mysql -u root -proot -e "source /vagrant/vagrantconf/bootstrap.sql"

#init racktemp db
mysql -uroot -proot -e "source /vagrant/racktemp.sql"

# nginx conf
sudo rm /etc/nginx/sites-enabled/default
sudo ln -s /vagrant/vagrantconf/racktemp.conf /etc/nginx/sites-enabled/racktemp.conf

# php setup
sudo echo "display_errors = On" | sudo tee -a /etc/php5/fpm/php.ini

sudo service nginx restart
sudo service mysql restart
sudo service php5-fpm restart
