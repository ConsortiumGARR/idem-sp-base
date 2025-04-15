FROM debian:12-slim AS builder
COPY --from=hairyhenderson/gomplate:v4.3.2 /gomplate /bin/gomplate

# Installing required packages
RUN apt-get update && apt-get install -y \
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
    supervisor --no-install-recommends \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Creating document root - 'site' folder is the default
RUN mkdir /var/www/html/site
RUN chown -R www-data: /var/www/html/site
COPY docker/web /var/www/html/site
COPY docker/web/index.html.template /tmp/index.html.template

# APACHE
COPY docker/apache2/idem-sp.conf.template /tmp/idem-sp.conf.template

# SHIBBOLETH - Attribute map
COPY docker/shibboleth/attribute-map.xml /etc/shibboleth/attribute-map.xml

# SUPERVISOR
COPY docker/supervisor/supervisord.conf /etc/supervisor/supervisord.conf
RUN mkdir -p /var/log/supervisor/ \
    && mkdir -p /var/run/supervisord
RUN chmod -R 0755 /var/log/supervisor

EXPOSE 80
EXPOSE 443

COPY start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh

# Start example stage building
FROM builder AS example

CMD ["/usr/local/bin/start.sh"]

# Start idem-test stage building
FROM builder AS idem-test

# Retrieving Federation Certificate for MDX
RUN wget https://mdx.idem.garr.it/idem-mdx-service-crt.pem -O /etc/shibboleth/idem-mdx-service-crt.pem

CMD ["/usr/local/bin/start.sh"]

# Start edugain stage building
FROM builder AS edugain

# Retrieving Federation Certificate for MDX
RUN wget https://mdx.idem.garr.it/idem-mdx-service-crt.pem -O /etc/shibboleth/idem-mdx-service-crt.pem

CMD ["/usr/local/bin/start.sh"]