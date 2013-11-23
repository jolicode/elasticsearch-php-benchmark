VAGRANTFILE_API_VERSION = "2"

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|
  config.vm.network "private_network", ip: "192.168.50.4"
  config.vm.box = "precise64"
  config.vm.hostname = "192.168.50.4"
  config.vm.network :forwarded_port, host: 5000, guest: 80
  config.vm.provision "ansible" do |ansible|
    ansible.playbook = "./vagrant/provisioning/provision.yml"
    ansible.host_key_checking = false
  end
end
