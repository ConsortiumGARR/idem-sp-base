### Start builder 
FROM debian:12-slim AS builder
COPY --from=hairyhenderson/gomplate:v4.3.2 /gomplate /bin/gomplate

# Installing required packages
RUN apt-get update && apt-get install -y \
    apt-utils \
    wget \
    ca-certificates \
    nano \
    openssl \
    gettext \
    libapache2-mod-shib \
    apache2 \
    ntp \
    libapache2-mod-php \
    php \
    supervisor --no-install-recommends --no-install-suggests \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Creating document root - 'site' folder is the default
RUN mkdir -p /var/www/html/sp 
RUN chown -R www-data:www-data /var/www/html/sp
COPY --chown=www-data:www-data sp-base/docker/web /var/www/html/sp

# APACHE
COPY --chown=www-data:www-data sp-base/docker/apache2/idem-sp.conf.template /tmp/idem-sp.conf.template

# SHIBBOLETH
RUN mkdir -p /var/run/shibboleth
RUN chown -R _shibd:_shibd /var/run/shibboleth
COPY sp-base/docker/shibboleth/attribute-map.xml /etc/shibboleth/attribute-map.xml
COPY sp-base/docker/shibboleth/shibboleth2.xml.template /tmp/shibboleth2.xml.template

# SUPERVISOR
COPY sp-base/docker/supervisor/ /etc/supervisor/
RUN mkdir -p /var/log/supervisor/ \
    && mkdir -p /var/run/supervisord
RUN chmod -R 0755 /var/log/supervisor

EXPOSE 80
EXPOSE 443

COPY sp-base/docker/start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh

### Start example stage building
FROM builder AS example

CMD ["/usr/local/bin/start.sh"]


### Start idem SP stage building
FROM builder AS idem

ARG SP_CERT_PATH

# Copy the SP signing/encryption certificates
COPY ${SP_CERT_PATH}/ /etc/shibboleth/

# Retrieving Federation Certificate for MDX
RUN wget https://mdx.idem.garr.it/idem-mdx-service-crt.pem -O /etc/shibboleth/idem-mdx-service-crt.pem

CMD ["/usr/local/bin/start.sh"]
