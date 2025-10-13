### Start base
FROM debian:12-slim AS base
COPY --from=hairyhenderson/gomplate:v4.3.2 /gomplate /bin/gomplate

# Installing required packages
RUN apt-get update && apt-get install -y \
    apt-utils \
    wget \
    vim \
    ca-certificates \
    cron \
    openssl \
    gettext \
    gzip \
    libapache2-mod-shib \
    apache2 \
    make \
    ntp \
    libapache2-mod-php \
    php \
    python3-cryptography \
    python3-jwt \
    python3-requests \
    python3-pem \
    rsyslog \
    supervisor --no-install-recommends --no-install-suggests \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Creating document root - 'site' folder is the default
RUN mkdir -p /var/www/html/sp /var/www/html/sp/secure /var/www/html/sp/secureMFA
RUN chown -R www-data:www-data /var/www/html/sp
COPY --chown=www-data:www-data sp-base/docker/web /var/www/html/sp

# APACHE
COPY --chown=www-data:www-data sp-base/docker/apache2/sp.conf.template /etc/apache2/sites-available/sp.conf.template

# SHIBBOLETH
RUN mkdir -p /var/run/shibboleth
COPY sp-base/docker/shibboleth/attribute-map.xml /etc/shibboleth/attribute-map.xml
COPY sp-base/docker/shibboleth/shibboleth2.xml.template /etc/shibboleth/shibboleth2.xml.template
COPY sp-base/docker/shibboleth/shibd.logger /etc/shibboleth/shibd.logger
RUN chown -R _shibd:_shibd /var/run/shibboleth /etc/shibboleth 

# EMBEDDED DISCOVERY SERVICE
ARG SHIB_EDS_VERSION=1.4.0
RUN wget https://shibboleth.net/downloads/embedded-discovery-service/${SHIB_EDS_VERSION}/shibboleth-embedded-ds-${SHIB_EDS_VERSION}.tar.gz -O /usr/local/src/shibboleth-eds.tar.gz \
    && cd /usr/local/src/ \
    && tar xzf shibboleth-eds.tar.gz \
    && cd shibboleth-embedded-ds-${SHIB_EDS_VERSION} \
    && make install \
    && sed -i 's/onmousedown/onclick/g' /etc/shibboleth-ds/idpselect.js

# RSYSLOG
COPY sp-base/docker/rsyslog/rsyslog.conf /etc/rsyslog.conf

# SUPERVISOR
COPY sp-base/docker/supervisor/ /etc/supervisor/
RUN mkdir -p /var/log/supervisor/ \
    && mkdir -p /var/run/supervisord
RUN chmod -R 0755 /var/log/supervisor

EXPOSE 80
EXPOSE 443

COPY sp-base/docker/start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh


### Start custom SP stage
FROM base AS custom

ARG SP_CERT_PATH
ARG SP_PRIVPOLICY_PATH

# Copy the SP signing/encryption certificates, leave the folder empty if you don't have certificates.
COPY ${SP_CERT_PATH}/ /etc/shibboleth/

# COpy the SP privacy policy
COPY ${SP_PRIVPOLICY_PATH}/privacy.php.template /var/www/html/sp/

CMD ["/usr/local/bin/start.sh"]


### Start idem SP stage
FROM base AS idem

ARG SP_CERT_PATH
ARG SP_PRIVPOLICY_PATH

# Copy the SP signing/encryption certificates
COPY ${SP_CERT_PATH}/ /etc/shibboleth/

# COpy the SP privacy policy
COPY ${SP_PRIVPOLICY_PATH}/privacy.php.template /var/www/html/sp/

# Retrieving Federation Certificate for MDX
RUN wget https://mdx.idem.garr.it/idem-mdx-service-crt.pem -O /etc/shibboleth/idem-mdx-service-crt.pem

# Shibboleth EDS config
COPY sp-base/docker/shibboleth-eds/idem_jwt_to_json /opt/idem_jwt_to_json/
COPY sp-base/docker/shibboleth-eds/config/ /etc/shibboleth-ds/
RUN chmod +x /opt/idem_jwt_to_json/decodeToken.py

CMD ["/usr/local/bin/start.sh"]
