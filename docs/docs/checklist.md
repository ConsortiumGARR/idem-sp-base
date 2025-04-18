# Checklist

- Deployment Environment:

  - Hardware Requirements:
        - [x] RAM: <= 2 GB
        - [x] vCPU: 2
        - [x] HDD: 10 GB
        - [x] OS: Debian 12

  - Software Requirements:
        - [x] Ansible
        - [x] Python3 ([compatible with the version of ansible-core](https://docs.ansible.com/ansible/latest/reference_appendices/release_and_maintenance.html#ansible-core-support-matrix))
        - [x] Docker

- [ ] Production:

        - [ ] Bari:
            - [ ] `cnode1-ba1-r1.aai.garr.it` (active & corresponding to `conf.idem.garr.it`)
            - [ ] `cnode1-ba1-r2.aai.garr.it` (active & corresponding to `conf.idem.garr.it`)

        - [x] Palermo:
            - [x] `cnode1-pa1.aai.garr.it` (backup for disaster recovery & corrisponding to `conf.aai.garr.it`)
            - [x] `cnode2-pa1.aai.garr.it` (backup for disaster recovery & corrisponding to `conf.aai.garr.it`)

(DA QUI IN POI BISOGNA STABILIRE)

- Development:
  - [x] Catania:
  - [x] `cnode2-ct1.aai.garr.it` (active & corresponding to `conf.aai-test.garr.it`)

- Network:
  - Ports:
    - [x] `8085` => `80`
    - [x] `8447` => `443`
    - [x] `22` (available only via VPN)

- DNS:
  - [x] Production (domain `aai.garr.it`):
    - [x] `conf.aai.garr.it` CNAME `ha-pa1.aai.garr.it`

  - [x] Development/Staging (domain `aai-test.garr.it`):
    - [x] `conf.aai-test.garr.it` CNAME `ha-ct1.aai.garr.it`

- [x] High-Availability: Yes (`ha-pa1.aai.garr.it`)

- [x] Monitoring:

- [ ] CheckMK - <https://checkmk.aai.garr.it/>(today) o Zabbix <https://zabbix-ha.int.infra.garr.it/> (next one):

  - [ ] Container Name:

    - [ ] `idem-ba1-r1-conf`
    - [ ] `idem-pa1-n1-conf`

- [x] Logging (saved to `/opt/docker-logs/`):

    - [x] Managed by Docker Logging on Rsyslog (514 UDP)

- [x] Container Node/Docker Host SSH access:

    - [x] Users: IDEM Service Operators

        - [x] Service Operator SSH accounts created and enabled to become ROOT with `sudo`. Each SSH account has to have its SSH key with passphrase.

- [x] Security:

    - [x] SSL Credentials loaded into `/opt/idem-certbot/live/conf.idem.garr.it`
    - [x] Root access configured only for Console, not SSH. (useful if anybody can access with SSH)
    - [x] `group_vars/remote_host.yml` file encrypted by Ansible Vault

- [x] Backup: No

- [x] Deployment Tools:

    - [x] Ansible

- [x] Miscellaneous:
    - [x] Generate the Docker Image of IDEM-Conf
    - [x] Manage SSL credentials with Docker volumes
    - [x] Manage NGINX configuration with Docker volumes

- [ ] Documentation:
    - [ ] Update documentation regarding IDEM Conf:
        - [ ] <https://wiki.idem.garr.it/wiki/RilascioAttributi>:
          - [ ] Add banner to new documentation with a grace period of 3 months.
        - [ ] <https://docs-idem-ops.docs.dir.garr.it/it/latest/cnode_port_mapping/>
          - [ ] Add Port Mapping for new Docker Container under each region.
