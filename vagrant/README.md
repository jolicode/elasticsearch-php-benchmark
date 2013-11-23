vagrant-php
===========

Configuration files to setup a vagrant box with PHP (compiled from source), Nginx, MySQL, MongoDB, Memcached and Elasticsearch (with some plugins)

What will be installed?
-----------------------
- PHP 5.5.5 with xdebug, memcached, mongo, opcache, mbstring, mysqli, openssl, zlib, curl, gd, zip, xsl, calendar and ftp
- Composer
- Nginx
- Memcached
- MongoDB
- MySQL
- Elasticsearch with the following plugins: bigdesk, paramedic, elasticsearch-head and elastichq

Nginx, Memcached and MySQL packages are from the Ubuntu repositories. MongoDB is installed using the 10gen official repository.

The MySQL root user has no password.