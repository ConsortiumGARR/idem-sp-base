#!/bin/bash
set -e

# Templates by Gomplate
templates=(
    "/etc/apache2/sites-available/sp.conf.template"
    "/etc/shibboleth/shibboleth2.xml.template"
    "/var/www/html/sp/index.php.template"
    "/var/www/html/sp/shared/header.php.template"
    "/var/www/html/sp/shared/footer.html.template"
    "/var/www/html/sp/shared/logout.php.template"
    "/etc/supervisor/supervisord.conf.template"
    "/var/www/html/sp/privacy.php.template"
)

for src in "${templates[@]}"; do
    dst="${src%.template}" # Remove .template extentions
    filename=$(basename "$src")
    if [ -f "$src" ]; then
        echo "[setup][INFO] Template $filename found, processing with gomplate..."
        # Handle shibboleth2.xml particular case
        if [[ "$filename" == "shibboleth2.xml.template" && -f "$dst" ]]; then
            rm "$dst"
        fi
        gomplate -f "$src" -o "$dst"
        rm "$src"
        echo "[setup][OK] Template $filename processed and removed."
    else
        echo "[setup][INFO] Template $filename not found, skipping gomplate processing."
    fi
done

# Generating signing/encryption certs if not already included, else decrypt.
echo "[setup][INFO] Checking Shibboleth certs in /etc/shibboleth..."
cd /etc/shibboleth

if [ ! -f sp-signing-cert.pem ] || [ ! -f sp-encrypt-cert.pem ]; then
    echo "[setup][WARN] One or more certs missing. Regenerating all keys and certs..."
    shib-keygen -u _shibd -g _shibd -h "${SERVER_NAME}" -y 30 -e "https://${SERVER_NAME}/shibboleth" -n sp-signing -f
    shib-keygen -u _shibd -g _shibd -h "${SERVER_NAME}" -y 30 -e "https://${SERVER_NAME}/shibboleth" -n sp-encrypt -f
    echo "[setup][OK] Certificates generated."
elif [ -f sp-signing-key-encrypted.pem ] && [ -f sp-encrypt-key-encrypted.pem ]; then
    echo "[setup][INFO] Encrypted keypair found. Decrypting..."
    openssl rsa -in "sp-signing-key-encrypted.pem" --passin pass:"${CERT_PASSPHRASE}" -out "sp-signing-key.pem"
    openssl rsa -in "sp-encrypt-key-encrypted.pem" --passin pass:"${CERT_PASSPHRASE}" -out "sp-encrypt-key.pem"
    rm "sp-signing-key-encrypted.pem" "sp-encrypt-key-encrypted.pem"
    echo "[setup][OK] Keys successfully decrypted."
elif [ -f sp-signing-key-encrypted.pem ] || [ -f sp-encrypt-key-encrypted.pem ]; then
    echo "[setup][ERROR] Incomplete encrypted keypair detected!"
    [ -f sp-signing-key-encrypted.pem ] || echo "  - Missing: sp-signing-key-encrypted.pem"
    [ -f sp-encrypt-key-encrypted.pem ] || echo "  - Missing: sp-encrypt-key-encrypted.pem"
    echo "[setup] Aborting to prevent inconsistent state."
    exit 1
else
    echo "[setup][OK] Certs present. Keys assumed present or previously decrypted."
fi

if [ -n ${EDS_ENABLED} ] && [ "${EDS_ENABLED}" = "true" ]; then
    export ESCAPED_SERVER_NAME=$(printf '%s' "${SERVER_NAME}" | sed -e 's/\//\\\//g' -e 's/\./\\./g')
    templates=(
    "/etc/shibboleth-ds/idpselect_config.js.template"
    "/etc/shibboleth-ds/index.php.template"
    )
    for src in "${templates[@]}"; do
        dst="${src%.template}"  # Remove .template extentions
        filename=$(basename "$src")
        if [ -f "$src" ]; then
            echo "[setup][INFO] Template $filename found, processing with gomplate..."
            gomplate -f "$src" -o "$dst"
            rm "$src"
            echo "[setup][OK] Template $filename processed and removed."
        else
            echo "[setup][INFO] Template $filename not found, skipping gomplate processing."
        fi
    done

    if [ ${MDX_FED_TYPE} == "idem-test" ]; then
        echo "*/30 * * * * root /opt/idem_jwt_to_json/decodeToken.py -j https://mdx.idem.garr.it/idem-test-token -o /var/www/html/sp/feed-eds.json -k /opt/idem_jwt_to_json/idem-mdx-service-pubkey.pem >> /proc/1/fd/1 2>&1" > /etc/cron.d/eds-refresh
        # Launch the command the first time on start
        /opt/idem_jwt_to_json/decodeToken.py -j https://mdx.idem.garr.it/idem-test-token -o /var/www/html/sp/feed-eds.json -k /opt/idem_jwt_to_json/idem-mdx-service-pubkey.pem > /dev/stdout
    elif [ ${MDX_FED_TYPE} == "edugain" ]; then
        echo "*/30 * * * * root /opt/idem_jwt_to_json/decodeToken.py -j https://mdx.idem.garr.it/edugain2idem-token -o /var/www/html/sp/feed-eds.json -k /opt/idem_jwt_to_json/idem-mdx-service-pubkey.pem >> /proc/1/fd/1 2>&1" > /etc/cron.d/eds-refresh
        # Launch the command the first time on start
        /opt/idem_jwt_to_json/decodeToken.py -j https://mdx.idem.garr.it/edugain2idem-token -o /var/www/html/sp/feed-eds.json -k /opt/idem_jwt_to_json/idem-mdx-service-pubkey.pem > /dev/stdout
    elif [ ${MDX_FED_TYPE} == "idem" ]; then
        echo "*/30 * * * * root /opt/idem_jwt_to_json/decodeToken.py -j https://mdx.idem.garr.it/idem-token -o /var/www/html/sp/feed-eds.json -k /opt/idem_jwt_to_json/idem-mdx-service-pubkey.pem >> /proc/1/fd/1 2>&1" > /etc/cron.d/eds-refresh
        # Launch the command the first time on start        
        /opt/idem_jwt_to_json/decodeToken.py -j https://mdx.idem.garr.it/idem-token -o /var/www/html/sp/feed-eds.json -k /opt/idem_jwt_to_json/idem-mdx-service-pubkey.pem > /dev/stdout
    fi

    echo "[setup][OK] EDS configuration done."
fi

# Give to the certs the right permissions
chown www-data:www-data -R "${SSL_CERT_LOCATION}"
find "${SSL_CERT_LOCATION}" -type f -name '*privkey.pem' -exec chmod 600 {} \;

# Enable the SP site configuration
echo "[setup][INFO] Enabling Apache Shibboleth site configuration..."
ln -f -s /var/www/html/sp/shared/index.php /var/www/html/sp/secure/index.php
ln -f -s /var/www/html/sp/shared/index.php /var/www/html/sp/secureMFA/index.php
chown -R www-data:www-data /var/www/html/sp/secure*
a2dissite 000-default
a2enmod ssl headers alias include negotiation shib remoteip
a2ensite sp.conf

# Permissions
chown -R _shibd:_shibd /var/run/shibboleth /etc/shibboleth 
chown -R www-data /etc/apache2 "${SSL_CERT_LOCATION}" /var/log/apache2 /var/run/apache2

# Exec del supervisor
echo "[setup][START] Starting supervisord..."
exec /usr/bin/supervisord -c /etc/supervisor/supervisord.conf
