# -*- mode: ruby -*-
# vi: set ft=ruby :

VAGRANTFILE_API_VERSION = "2"

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|
  config.vm.box = "precise64"
  config.vm.network :forwarded_port, host: 5000, guest: 80
  config.vm.provision "ansible" do |ansible|
    ansible.playbook = "vagrant/provisioning/provision.yml"
  end
end
