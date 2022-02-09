#!/bin/bash

ssh root@my.mdpcrm.com -q -p 22022 /bin/bash << EOF
cd /usr/share/nginx/html

/usr/bin/git pull
/usr/local/bin/composer install

chown -R www-data:www-data /usr/share/nginx/html
systemctl restart nginx.service
EOF
