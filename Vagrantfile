Vagrant.configure("2") do |config|
  # https://docs.vagrantup.com.

  config.vm.box = "debian/stretch64"
  config.vm.box_check_update = true

  config.ssh.shell = "bash -c 'BASH_ENV=/etc/profile exec bash'"

  config.vm.network "forwarded_port", guest: 80, host: 8080, host_ip: "0.0.0.0"
  config.vm.network "forwarded_port", guest: 3306, host: 3366

  config.vm.provider "virtualbox" do |vb|
    vb.memory = "2048"
  end

  config.vm.provision "shell", inline: <<-SHELL
    export DEBIAN_FRONTEND=noninteractive
    apt-get update
    apt-get autoremove -y --purge apache2
    apt-get upgrade -y
    apt-get install -y nginx php7.0-fpm php7.0-json php7.0-curl php7.0-mysql mariadb-server
    ln -s /vagrant /var/www/passy
    cp /vagrant/examples/nginx_site.conf /etc/nginx/sites-enabled/default
    service nginx restart
    mysql -uroot -e "CREATE DATABASE IF NOT EXISTS passy;"
    mysql -uroot -e "CREATE USER 'passy'@'%' IDENTIFIED BY '';"
    mysql -uroot -e "GRANT USAGE ON *.* TO 'passy'@'%' IDENTIFIED BY ''"
    mysql -uroot -e "GRANT ALL PRIVILEGES ON passy.* TO 'passy'@'%';"
    mysql -uroot -e "FLUSH PRIVILEGES;"
    sed -i "s/.*bind-address.*/bind-address = 0.0.0.0/" /etc/mysql/my.cnf
    service mysql restart
  SHELL
end
