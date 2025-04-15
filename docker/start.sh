#!/bin/bash
set -e

# Loading requested index version
if [ "$INDEX_VERSION" == "test" ]; then
    export DISCOVERY_URL="https://wayf.idem-test.garr.it/WAYF"
    cp /opt/html/index-test.html /var/www/html/site/index.html
    echo "✔ Test index loaded."
elif [ "$INDEX_VERSION" == "edugain" ]; then
    export DISCOVERY_URL="https://service.seamlessaccess.org/ds/"
    cp /opt/html/index-eduGain.html /var/www/html/site/index.html
    echo "✔ Edugain index loaded."
else
    echo "✗ INDEX_VERSION is not correctly setted, please use 'test' or 'edugain'."
    exit 1
fi

if [ "$DEBUG" == "true" ]; then
    a2enmod ssl headers alias include negotiation
    a2dissite 000-default.conf default-ssl
else
    a2enmod ssl headers alias include negotiation > /dev/null 2>&1
    a2dissite 000-default.conf default-ssl > /dev/null 2>&1
fi

# Replacing SERVER_NAME
if [ "$SERVER_NAME" != "" ]; then 
    envsubst '${SERVER_NAME}' < /etc/apache2/sites-available/000-idem-sp.conf.template > /etc/apache2/sites-available/000-idem-sp.conf
    echo "✔ Apache configuration updated with: '$SERVER_NAME'"
else
    echo "✗ SERVER_NAME is empty, please insert your Full Qualified Domain Name (FQDN)"
    exit 1
fi

a2ensite 000-idem-sp.conf

if [ ! -d "/var/run/shibboleth" ]; then
    mkdir /var/run/shibboleth
fi

# Updating shibboleth2.xml
envsubst < /etc/shibboleth/shibboleth2.xml.template > /etc/shibboleth/shibboleth2.xml

# Exec del supervisor
exec /usr/bin/supervisord -c /etc/supervisor/supervisord.conf