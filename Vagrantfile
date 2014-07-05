# -*- mode: ruby -*-
# vi: set ft=ruby :

# Vagrantfile API/syntax version. Don't touch unless you know what you're doing!
VAGRANTFILE_API_VERSION = "2"

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|

  # Every Vagrant virtual environment requires a box to build off of.
  config.vm.box = "chef/debian-7.4-i386"
  config.vm.provision :shell, :path => "vagrantconf/bootstrap.sh"
  config.vm.network :forwarded_port, host: 4566, guest: 80    # nginx
  config.vm.network :forwarded_port, host: 4567, guest: 443   # nginx https
  config.vm.network :forwarded_port, host: 4568, guest: 3306  # mysql

end
