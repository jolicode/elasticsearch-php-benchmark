ElasticSearch PHP clients benchmark
===================================

We run all the PHP clients for Elasticsearch (if we miss one, send a PR!) and ask them to perform a lot of requests,
we compute some statistics about it.

More than the speed or memory, this benchmark is also about usage, API and examples for each clients.

They are configured the same way:
- no logs
- two nodes cluster (as we run a test with the master node down, to test fallback)
- keep alive on the connection (if possible)

About versions
--------------

- **ruflin/elastica**: v0.90.5.0
- **sherlock/sherlock**: dev-master
- **elasticsearch/elasticsearch**: v0.4.2
- **nervetattoo/elasticsearch**: v2.3.0


Data extracted 2013/11/25
-------------------------

Runned on the VM (PHP 5.5.5, ES 0.90.6, Ubuntu precise64).

<table>
<tr><th></th><th>elasticsearch       </th><th>sherlock            </th><th>elastica            </th><th>nervetattoo         </th></tr>
 <tr><td>getDocument         </td><td>1055</td><td>1123</td><td>694</td><td>785</td></tr>
 <tr><td>searchDocument      </td><td>873</td><td>1206</td><td>724</td><td>724</td></tr>
 <tr><td>searchDocumentWithFacet</td><td>918</td><td>1409</td><td>786</td><td>691</td></tr>
 <tr><td>searchOnDisconnectNode</td><td>1169</td><td>818</td><td>924</td><td>0</td></tr>
 <tr><td>searchSuggestion    </td><td>1532</td><td>0</td><td>1237</td><td>1292</td></tr>
 <tr><td>indexRefresh        </td><td>684</td><td>0</td><td>604</td><td>843</td></tr>
 <tr><td>indexStats          </td><td>831</td><td>0</td><td>719</td><td>904</td></tr>
 <tr><td>Total time</td><td>7062</td><td>4556</td><td>5688</td><td>5239</td></tr>
 <tr><td>Memory</td><td>9437184</td><td>6029312</td><td>4194304</td><td>3670016</td></tr>
</table>

Read about the whole results
----------------------------

This benchmark is part of an article describing [Elasticsearch PHP clients](http://jolicode.com/).

Vagrantfile
-----------

To run this benchmark you can use the provided Vagrantfile. It require:

- Vagrant
- [Ansible](http://www.ansibleworks.com/docs/intro_installation.html)
- 15 min to boot :D

```sh
git clone
composer install
vagrant up
vagrant ssh
$ sudo service elasticsearch stop
$ cd /vagrant/
$ ./bin/run-all.sh "sudo /opt/elasticsearch/bin/elasticsearch" && sudo killall /usr/bin/java
```

We use a modified version of https://github.com/davialexandre/vagrant-php for the Vagrant box (MIT License).
