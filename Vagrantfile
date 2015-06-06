# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant.configure("2") do |config|

    config.vm.network "private_network", ip: "192.168.33.10"
    config.vm.synced_folder ".", "/var/www", :mount_options => ["dmode=777", "fmode=666"]

    config.vm.provider "docker" do |d|
      d.build_dir = "."
      d.has_ssh = true
    end

    config.ssh.port = 22

end
