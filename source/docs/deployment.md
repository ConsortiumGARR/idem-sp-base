# Deployment

The deployment of the service is done with Ansible.

## Setup

1. Inventory:

    1. Copy the `sp-base/ansible/inventories/example/inventory-template.ini` file to `sp-base/ansible/inventories/example/inventory.ini`.
    2. Modify it according to your needs.

    To enable the local deployment, just uncomment the line `#localhost ansible_connection=local`.

2. Copy the `sp-base/ansible/sp-playbook-template.yml` to `sp-base/ansible/sp-playbook.yml` and modify it according to your needs.

3. Configuration variables:

    Please follow the instructions listed in the `sp-base/ansible/inventories/example/group_vars/all.yml`

    Here you can find a list of all the variables used to configure the image and for which you should set your own values in the above `all.yml` file:

| Variable Name              | Example Value                        | Note |
|----------------------------|--------------------------------------|------|
| `docker_image_name`        | `"sp.example.org"`                   | Name of the docker image. |
| `fqdn`                     | `"sp.example.org"`                   | Full Qualified Domain Name used in the Apache2 configuration. |
| `http_port`                | `8080`                               | HTTP port mapped in Docker Compose for Apache2. |
| `https_port`               | `8443`                               | HTTPS port mapped in Docker Compose for Apache2. |
| `entity_id`                | `"https://example.org/shibboleth"`   | Service Provider EntityID. |
| `eds_enabled`              |                                      | Set to `true` to enable Shibboleth EDS (highest priority for multiple IdPs). |
| `wayf_url`                 |                                      | WAYF service URL (e.g., `https://wayf.idem-test.garr.it/WAYF`) for multiple IdPs. |
| `remote_idp_entity_id`     | `https://idp.example.org/idp/shibboleth` | EntityID of a single IdP. Configure only one connection method (priority: EDS > WAYF > Single IdP). |
| `remote_idp_metadata_url`  | `https://idp.example.org/idp/shibboleth` | Metadata URL of a single IdP. |
| `ssl_cert_host_location`   | `/opt/certs` | SSL certificates location on the host. |
| `ssl_cert_location`        | `/opt/certs` | SSL certificates location on the instance. |
| `docker_image_location`    |                                      | Name of the Container Registry where the image is uploaded. Leave blank if unused. |
| `docker_image_version`     | `example`                            | Name of the SP docker image version. |
| `registry_location`        |                                      | URL location of your registry. |
| `registry_user`            |                                      | User used to access the registry. |
| `registry_password`        |                                      | User's password. |
| `location`                 | `local`                              | Customizes the name of the docker container (e.g., `"local"`). |
| `server_admin`             | `admin@example.org`                  | ServerAdmin email in the Apache2 configuration. |
| `cert_passphrase`          |                                      | Passphrase to decrypt the private keys (named `sp-signing-key-encrypted.pem` and `sp-encrypt-key-encrypted.pem`). |
| `ca_cert`                  |                                      | CA certificate filename (required if not included in the main cert file). |
| `mdx_fed_type`             |                                      | MDX federation type (e.g., `idem-test`, `idem`, `edugain`). See [MDX docs](https://mdx.idem.garr.it). |
| `federation_type`          |                                      | Federation membership (e.g., `"IDEM Test"`, `"IDEM and eduGAIN"`). |
| `syslog_ip`                |                                      | IP address of a syslog server for log forwarding. |
| `deploy_type`              | `example`                            | This variable is used for enabling HAProxy with Proxy Protocol when setted to "prod". |
| `haproxy_ip_range`         |                                      | This variable is used to define the IP range of your HAProxy instance when using the Proxy Protocol in front of the SP.|

## Deploy

The default command to launch Ansible is:

`ansible-playbook ansible/sp-playbook.yml -i ansible/inventories/example/inventory.ini`
