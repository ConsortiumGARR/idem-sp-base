# Architecture

![Architecture]( da creare immagine nuova )

## Components

The main, and only, component developed in the repository is a Shibboleth Service Provider Docker image composed by:

- **Shibd**: the deamon of a Shibboleth Service Provider, handles requesting authentication and processing attributes from the IdP.
- **Apache2**: Frontend web server, which serves the web content using HTTPS.
- **Rsyslog**: is used to manage the logs, sending them to stderr/stdout.
- **Cron**: active only when the Embedded Discovery Service (EDS) is enabled. It is used to update the json file consumed by the EDS.
- **Supervisor**: manage all the previous processes within the docker image.

The Docker image is built upon the Debian:12-slim Docker image provided by Docker Hub, <https://hub.docker.com/_/debian>.
