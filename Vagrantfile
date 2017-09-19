# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant.configure("2") do |config|
  # https://docs.vagrantup.com.


  # LINUX ONLY! ADD # to disable
  config.vm.provider "lxc"



  config.vm.box = "debian/stretch64"

  config.vm.box_check_update = true

  config.ssh.shell = "bash -c 'BASH_ENV=/etc/profile exec bash'"

  config.vm.network "forwarded_port", guest: 80, host: 8080
  config.vm.network "forwarded_port", guest: 3306, host: 3366

  config.vm.provider "virtualbox" do |vb|
    # Display the VirtualBox GUI when booting the machine
    # vb.gui = true

    vb.memory = "2048"
  end

  config.vm.provision "shell", inline: <<-SHELL
    export DEBIAN_FRONTEND=noninteractive
    echo "deb http://packages.dotdeb.org stretch all" > /etc/apt/sources.list.d/dotdeb.list
    wget -qO - https://www.dotdeb.org/dotdeb.gpg | apt-key add -
    apt-get update
    apt-get upgrade -y
    apt-get install -y apache2 libapache2-mod-php7.0 php7.0 php7.0-json php7.0-curl php7.0-mysql mariadb-server
    rm -rf /var/www/html
    ln -s /vagrant /var/www/html
    phpenmod mysqli
    service apache2 restart
    mysql -uroot -e "CREATE DATABASE IF NOT EXISTS passy;"
    mysql -uroot -e "CREATE USER 'passy'@'%' IDENTIFIED BY '';"
    mysql -uroot -e "GRANT USAGE ON *.* TO 'passy'@'%' IDENTIFIED BY ''"
    mysql -uroot -e "GRANT ALL PRIVILEGES ON passy.* TO 'passy'@'%';"
    mysql -uroot -e "FLUSH PRIVILEGES;"
    sed -i "s/.*bind-address.*/bind-address = 0.0.0.0/" /etc/mysql/my.cnf
    service mysql restart
  SHELL
end
