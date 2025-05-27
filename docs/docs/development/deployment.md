# Deployment

The deployment of the service is done with Ansible.

## Setup

1. Inventory:

    1. Copy the `sp-base/ansible/inventories/example/inventory-template.ini` file to `sp-base/ansible/inventories/example/inventory.ini`.
    2. Modify it according to your needs.

    To enable the local deployment, just uncomment the line `#localhost ansible_connection=local`.

2. Update the `sp-base/ansible/idem-sp-playbook.yml` file according to your needs.

3. Configuration variables:

    Please follow the instructions listed in the `sp-base/ansible/inventories/example/group_vars/all.yml`

    Here you can find a list of all the variables used to configure the image and for which you should set your own values in the above `all.yml` file:

| Variable Name              | Example Value                        | Note |
|----------------------------|--------------------------------------|------|
| `docker_image_name`        | `"sp.example.org"`                   | Name of the docker image. |
| `container_registry`       |                                      | Name of the Container Registry where the image is uploaded. Leave blank if unused. |
| `version_idem_sp`          | `example`                            | Name of the SP docker image version. |
| `fqdn`                     | `"sp.example.org"`                   | Full Qualified Domain Name used in the Apache2 configuration. |
| `location`                 | `local`                              | Customizes the name of the docker container (e.g., `"local"`). |
| `http_port`                | `8080`                               | HTTP port mapped in Docker Compose for Apache2. |
| `https_port`               | `8443`                               | HTTPS port mapped in Docker Compose for Apache2. |
| `server_admin`             | `admin@example.org`                  | ServerAdmin email in the Apache2 configuration. |
| `entity_id`                | `"https://example.org/shibboleth"`   | Service Provider EntityID. |
| `cert_passphrase`          |                                      | Passphrase to decrypt the private keys (named `sp-signing-key-aes256.pem` and `sp-encrypt-key-aes256.pem`). |
| `ca_cert`                  |                                      | CA certificate filename (required if not included in the main cert file). |
| `remote_idp_entity_id`     | `https://idp.example.org/idp/shibboleth` | EntityID of a single IdP. Configure only one connection method (priority: EDS > WAYF > Single IdP). |
| `remote_idp_metadata_url`  | `https://idp.example.org/idp/shibboleth` | Metadata URL of a single IdP. |
| `wayf_url`                 |                                      | WAYF service URL (e.g., `https://wayf.idem-test.garr.it/WAYF`) for multiple IdPs. |
| `eds_enabled`              |                                      | Set to `true` to enable Shibboleth EDS (highest priority for multiple IdPs). |
| `mdx_fed_type`             |                                      | MDX federation type (e.g., `idem-test`, `idem`, `edugain`). See [MDX docs](https://mdx.idem.garr.it). |
| `federation_type`          |                                      | Federation membership (e.g., `"IDEM Test"`, `"IDEM and eduGAIN"`). |
| `deploy_type`              | `example`                            | Internal use only. Ignore. |
| `node_ip_priv`             |                                      | IP address of a syslog server for log forwarding. |

## Deploy

The default command to launch Ansible is:

`ansible-playbook ansible/idem-sp-playbook.yml -i ansible/inventories/example/inventory.ini`
