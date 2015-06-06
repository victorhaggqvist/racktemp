FROM ubuntu:14.04.2
MAINTAINER Victor Häggqvist <victor@snilius.com>
ADD README.md /
RUN apt-get update
RUN apt-get upgrade -y
RUN apt-get install -y openssh-server vim unzip git nginx mysql-server php5 php5-fpm php5-cli php5-curl php5-mysql php5-xdebug wget curl make php-apc

EXPOSE 22
EXPOSE 80
EXPOSE 8000

RUN mkdir -p /var/run/sshd
RUN chmod 0755 /var/run/sshd

# Create and configure vagrant user
RUN useradd --create-home -s /bin/bash vagrant
WORKDIR /home/vagrant

# Configure SSH access
RUN mkdir -p /home/vagrant/.ssh
RUN echo "ssh-rsa AAAAB3NzaC1yc2EAAAABIwAAAQEA6NF8iallvQVp22WDkTkyrtvp9eWW6A8YVr+kz4TjGYe7gHzIw+niNltGEFHzD8+v1I2YJ6oXevct1YeS0o9HZyN1Q9qgCgzUFtdOKLv6IedplqoPkcmF0aYet2PkEDo3MlTBckFXPITAMzF8dJSIFo9D8HfdOV0IAdx4O7PtixWKn5y2hMNG0zQPyUecp4pzC6kivAIhyfHilFR61RGL+GPXQ2MWZWFYbAGjyiYJnAmCP3NOTd0jMZEnDkbUvxhMmBYSdETk1rRgm+R4LOzFUGaHqHDLKLX+FIPKcF96hrucXzcWyLbIbEgE98OHlnVYCzRdK8jlqm8tehUc9c9WhQ== vagrant insecure public key" > /home/vagrant/.ssh/authorized_keys
RUN chown -R vagrant: /home/vagrant/.ssh
RUN echo -n 'vagrant:vagrant' | chpasswd

# Enable passwordless sudo for the "vagrant" user
RUN echo 'vagrant ALL=NOPASSWD: ALL' > /etc/sudoers.d/vagrant

RUN curl -s http://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer
CMD rm /etc/nginx/sites-available/default
CMD rm /etc/nginx/sites-enabled/default
CMD ["/setup"]
RUN service nginx restart
RUN service mysql restart
CMD ln -s /var/www/nginx.conf /etc/nginx/sites-enabled/texbot.conf

# give devserver access to /etc/shadow
RUN usermod -a -G shadow vagrant

CMD /usr/sbin/sshd -D -o UseDNS=no -o UsePAM=no
