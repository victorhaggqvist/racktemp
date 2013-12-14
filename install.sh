clear
#off we go!

echo "############################"
echo "#                          #"
echo "#    RackTemp installer    #"
echo "#       version 0.1        #"
echo "#                          #"
echo "#    by Victor Häggqvist   #"
echo "#                          #"
echo "############################"
echo ""
echo ""
echo ""
echo ""
echo "Enabeling kernel modules.."
#echo "#Enable gpio for reading temp" >> /etc/modules
sudo sh -c 'echo "w1-gpio" >> /etc/modules'
sudo sh -c 'echo "w1-therm" >> /etc/modules'

#tell user stuff
echo "Updating APT cache"
sudo aptitude update
echo "Installing dependencies.."
echo ""
echo "You will soon be prompted to enter a mysql root password"
echo "Enter RackTempRocks in the box that appears"
sleep 10

#install dependencies

sudo aptitude install -y git apache2 php5 php5-gd php5-curl php5-mysql mysql-server whois  #note, whois is needed for the mkpasswd command

echo "Downloading and installing the lates version of RackTemp.."
git clone https://github.com/victorhaggqvist/racktemp.git #download the application
sudo mv racktemp/* /var/www/  #move application to web root
sudo rm -r racktemp/


echo "Adding cronjob for scheduled reading"
sudo crontab -l > crons      #read current crons, in case there is any
echo "*/5 * * * * php /var/www/lib/readtempcron.php >> /var/www/read.log" >> crons #add append our cron
sudo crontab crons           #install the complete list
rm crons

cd /var/www/
sudo rm install.sh         #no need to store installer here

echo "Setting up DB"
#mysql -uroot -pRackTempRocks -e "CREATE DATABASE racktemp"
#mysql -uroot -pRackTempRocks -e "CREATE USER 'racktemp'@'localhost' IDENTIFIED BY 'TGjJOcDT8gRnN0LqQ7gL'"
#mysql -uroot -pRackTempRocks -e "GRANT SELECT,INSERT,UPDATE,DELETE,CREATE,DROP ON racktemp.* TO 'racktemp'@'localhost'"
#mysql -uracktemp -pTGjJOcDT8gRnN0LqQ7gL racktemp < racktemp.sql
mysql -uroot -pRackTempRocks -e "source racktemp.sql"
sudo rm racktemp.sql
sudo rm index.html

sudo usermod -a -G shadow www-data #add www-data to shadow group to make authentication work

echo "Installation finished"
echo "Rebooting in 15 sec..."
echo "After the reboot, open $(ifconfig eth0 | grep 'inet addr' | cut -d: -f2 | awk '{ print $1}') in your browser to meet RackTemp"
echo "You may press Ctrl-C you want to reboot later instead"
sleep 15
sudo reboot                  #reboot, cuz then kernel modules will be loaded
