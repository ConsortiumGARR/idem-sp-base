#!/bin/bash
set -e

# Templates by Gomplate
if [ -f "/tmp/idem-sp.conf.template" ]; then
    echo "[INFO] Template idem-sp.conf.template found, processing with gomplate..."
    gomplate -f /tmp/idem-sp.conf.template -o /etc/apache2/sites-available/idem-sp.conf
    rm /tmp/idem-sp.conf.template
    echo "[OK] Template idem-sp.conf.template processed and removed."
else
    echo "[INFO] Template idem-sp.conf.template not found, skipping gomplate processing."
fi

if [ -f "/tmp/shibboleth2.xml.template" ]; then
    echo "[INFO] Template shibboleth2.xml.template found, processing with gomplate..."
    rm /etc/shibboleth/shibboleth2.xml
    gomplate -f /tmp/shibboleth2.xml.template -o /etc/shibboleth/shibboleth2.xml
    rm /tmp/shibboleth2.xml.template
    echo "[OK] Template shibboleth2.xml.template processed and removed."
else
    echo "[INFO] Template shibboleth2.xml.template not found, skipping gomplate processing."
fi

if [ -f "/var/www/html/sp/index.html.template" ]; then
    echo "[INFO] Template index.html.template found, processing with gomplate..."
    gomplate -f /var/www/html/sp/index.html.template -o /var/www/html/sp/index.html
    rm /var/www/html/sp/index.html.template
    echo "[OK] Template index.html.template processed and removed."
else
    echo "[INFO] Template index.html.template not found, skipping gomplate processing."
fi

if [ -f "/var/www/html/sp/secureMFA/index.php.template" ]; then
    echo "[INFO] Template secureMFA/index.php.template found, processing with gomplate..."
    gomplate -f /var/www/html/sp/secureMFA/index.php.template -o /var/www/html/sp/secureMFA/index.php
    rm /var/www/html/sp/secureMFA/index.php.template
    echo "[OK] Template secureMFA/index.php.template processed and removed."
else
    echo "[INFO] Template secureMFA/index.php.template not found, skipping gomplate processing."
fi

if [ -f "/var/www/html/sp/secure/index.php.template" ]; then
    echo "[INFO] Template secure/index.php.template found, processing with gomplate..."
    gomplate -f /var/www/html/sp/secure/index.php.template -o /var/www/html/sp/secure/index.php
    rm /var/www/html/sp/secure/index.php.template
    echo "[OK] Template secure/index.php.template processed and removed."
else
    echo "[INFO] Template secure/index.php.template not found, skipping gomplate processing."
fi

# Generating signing/encryption certs if not already included, else decrypt.
echo "[INFO] Checking Shibboleth certs in /etc/shibboleth..."
cd /etc/shibboleth

if [ ! -f sp-signing-cert.pem ] || [ ! -f sp-encrypt-cert.pem ]; then
    echo "[WARN] One or more certs missing. Regenerating all keys and certs..."
    shib-keygen -u _shibd -g _shibd -h "${SERVER_NAME}" -y 30 -e "https://${SERVER_NAME}/shibboleth" -n sp-signing -f
    shib-keygen -u _shibd -g _shibd -h "${SERVER_NAME}" -y 30 -e "https://${SERVER_NAME}/shibboleth" -n sp-encrypt -f
    echo "[OK] Certificates generated."
elif [ -f sp-signing-key-aes256.pem ] && [ -f sp-encrypt-key-aes256.pem ]; then
    echo "[INFO] Encrypted keypair found. Decrypting..."
    openssl rsa -in "sp-signing-key-aes256.pem" --passin pass:"${CERT_PASSPHRASE}" -out "sp-signing-key.pem"
    openssl rsa -in "sp-encrypt-key-aes256.pem" --passin pass:"${CERT_PASSPHRASE}" -out "sp-encrypt-key.pem"
    rm "sp-signing-key-aes256.pem" "sp-encrypt-key-aes256.pem"
    echo "[OK] Keys successfully decrypted."
elif [ -f sp-signing-key-aes256.pem ] || [ -f sp-encrypt-key-aes256.pem ]; then
    echo "[ERROR] Incomplete encrypted keypair detected!"
    [ -f sp-signing-key-aes256.pem ] || echo "  - Missing: sp-signing-key-aes256.pem"
    [ -f sp-encrypt-key-aes256.pem ] || echo "  - Missing: sp-encrypt-key-aes256.pem"
    echo "Aborting to prevent inconsistent state."
    exit 1
else
    echo "[OK] Certs present. Keys assumed present or previously decrypted."
fi

if [ ${EDS_ENABLED} ]; then
    cd /usr/local/src
    wget https://shibboleth.net/downloads/embedded-discovery-service/latest/shibboleth-embedded-ds-1.3.0.tar.gz -O shibboleth-eds.tar.gz
    tar xzf shibboleth-eds.tar.gz
    cd shibboleth-embedded-ds-1.3.0
    apt-get update
    apt-get install -y make
    make install
    mv /etc/shibboleth-ds/shibboleth-ds.conf /etc/apache2/conf-available/shibboleth-ds.conf
    a2enconf shibboleth-ds.conf
    ESCAPED_SERVER_NAME=$(printf '%s' "${SERVER_NAME}" | sed -e 's/\//\\\//g' -e 's/\./\\./g')
    export ESCAPED_SERVER_NAME
    gomplate -f /var/www/html/sp/eds/idpselect_config.js.template -o /etc/shibboleth-ds/idpselect_config.js
    echo "[OK] EDS Template idpselect_config.template processed."
    gomplate -f /var/www/html/sp/eds/index.html.template -o /etc/shibboleth-ds/index.html
    echo "[OK] EDS Template index.html.template processed."
    rm -rf /var/www/html/sp/eds
    echo "[OK] EDS Templates removed."


    if [ -n ${MDX_FED_TYPE} ]; then
        mkdir /opt/idem_jwt_to_json
        # si potrebbe cambiare questa wget tenendo un semplice file statico?
        wget "https://mdx.idem.garr.it/idem-mdx-service-pubkey.pem" -O /opt/idem_jwt_to_json/idem-mdx-service-pubkey.pem
        apt-get install -y python3-jwt python3-requests python3-pem cron
        apt-get clean
        rm -rf /var/lib/apt/lists/*
        wget https://mdx.idem.garr.it/decodeToken.py -O /opt/idem_jwt_to_json/decodeToken.py
        chmod +x /opt/idem_jwt_to_json/decodeToken.py
        mv /etc/supervisor/supervisord-eds.conf /etc/supervisor/supervisord.conf

        if [ ${MDX_FED_TYPE} == "idem-test" ]; then
            echo "*/30 * * * *   /opt/idem_jwt_to_json/decodeToken.py -j https://mdx.idem.garr.it/idem-test-token -o /var/www/html/sp/feed-eds.json -k /opt/idem_jwt_to_json/idem-mdx-service-pubkey.pem > /opt/idem_jwt_to_json/jwt_to_json.log 2>&1" > /etc/cron.d/eds-refresh
            /opt/idem_jwt_to_json/decodeToken.py -j https://mdx.idem.garr.it/idem-test-token -o /var/www/html/sp/feed-eds.json -k /opt/idem_jwt_to_json/idem-mdx-service-pubkey.pem > /opt/idem_jwt_to_json/jwt_to_json.log 2>&1
        elif [ ${MDX_FED_TYPE} == "edugain" ]; then
            echo "*/30 * * * *   /opt/idem_jwt_to_json/decodeToken.py -j https://mdx.idem.garr.it/edugain2idem-token -o /var/www/html/sp/feed-eds.json -k /opt/idem_jwt_to_json/idem-mdx-service-pubkey.pem > /opt/idem_jwt_to_json/jwt_to_json.log 2>&1" > /etc/cron.d/eds-refresh
            /opt/idem_jwt_to_json/decodeToken.py -j https://mdx.idem.garr.it/edugain2idem-token -o /var/www/html/sp/feed-eds.json -k /opt/idem_jwt_to_json/idem-mdx-service-pubkey.pem > /opt/idem_jwt_to_json/jwt_to_json.log 2>&1
        elif [ ${MDX_FED_TYPE} == "idem" ]; then
            echo "*/30 * * * *   /opt/idem_jwt_to_json/decodeToken.py -j https://mdx.idem.garr.it/idem-token -o /var/www/html/sp/feed-eds.json -k /opt/idem_jwt_to_json/idem-mdx-service-pubkey.pem > /opt/idem_jwt_to_json/jwt_to_json.log 2>&1" > /etc/cron.d/eds-refresh
            /opt/idem_jwt_to_json/decodeToken.py -j https://mdx.idem.garr.it/idem-token -o /var/www/html/sp/feed-eds.json -k /opt/idem_jwt_to_json/idem-mdx-service-pubkey.pem > /opt/idem_jwt_to_json/jwt_to_json.log 2>&1
        fi
    fi
else
    rm -rf /var/www/html/sp/eds
fi

# Give to the certs the right permissions
chown www-data:www-data -R "/etc/letsencrypt/live/${SERVER_NAME}"
chmod 600 "/etc/letsencrypt/live/${SERVER_NAME}/privkey.pem"

# Enable the SP site configuration
echo "[INFO] Enabling Apache Shibboleth site configuration..."
a2dissite 000-default
a2enmod ssl headers alias include negotiation shib
a2ensite idem-sp.conf

# Exec del supervisor
echo "[START] Starting supervisord..."
exec /usr/bin/supervisord -c /etc/supervisor/supervisord.conf
