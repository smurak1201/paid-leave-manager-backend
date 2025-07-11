#!/bin/bash
sudo service mysql start
sudo service apache2 start
php artisan serve --host=172.22.227.254 --port=8000
