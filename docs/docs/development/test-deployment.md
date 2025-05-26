# Test Deployment

If you want to test this service on your personal machine you can follow the subsequent steps.

## Software Requirements

### Docker Engine

DOC: <https://docs.docker.com/engine/install/debian/>

1. Download the script:

      ```bash
      curl -fsSL https://get.docker.com -o install-docker.sh
      ```

2. Verify the script's content:

      ```bash
      cat install-docker.sh
      ```

3. Run the script with `--dry-run` to verify the steps it executes:

      ```bash
      sh install-docker.sh --dry-run
      ```

4. Run the script either as root, or using sudo to perform the installation:

      ```bash
      sudo sh install-docker.sh
      ```

### Ansible

DOC: <https://docs.ansible.com/ansible/latest/installation_guide/installation_distros.html>

- [Debian install](https://docs.ansible.com/ansible/latest/installation_guide/installation_distros.html#installing-ansible-on-debian)
- [Ubuntu install](https://docs.ansible.com/ansible/latest/installation_guide/installation_distros.html#installing-ansible-on-ubuntu)

## Create a Docker-in-Docker container

1. Install Sysbox:

      DOC: <https://github.com/nestybox/sysbox>

      ```bash
      sudo apt install jq

      wget "https://downloads.nestybox.com/sysbox/releases/v0.6.4/sysbox-ce_0.6.4-0.linux_amd64.deb"

      sudo apt install ./sysbox-ce_0.6.4-0.linux_amd64.deb

      rm sysbox-ce_0.6.4-0.linux_amd64.deb
      ```

2. Create the `dev-sp-demo` Container:

      ```bash
      docker run --runtime=sysbox-runc -id -h dev-sp-demo --name dev-sp-demo nestybox/ubuntu-noble-systemd-docker:latest
      ```

3. Configure OpenSSH into `dev-sp-demo` container:

      ```bash
      docker exec -i dev-sp-demo bash -c "apt-get update && apt-get install -y python3 docker-compose --no-install-recommends --no-install-suggests && echo 'admin ALL=(ALL) NOPASSWD: ALL' >> /etc/sudoers"
      ```

4. Get the IP address of the `dev-sp-demo` container:

      ```bash
      {% raw %}docker inspect -f '{{range .NetworkSettings.Networks}}{{.IPAddress}}{{end}}' dev-sp-demo{% endraw %}
      ```

5. Load your personal SSH credential (id_rsa) in the `dev-sp-demo` container:

      `ssh-copy-id admin@<CONTAINER-IP>`   (password: `admin`)

6. Now you can log-in the `dev-sp-demo` container with:

      `ssh admin@<CONTAINER-IP>`

      (it works also on Visual Studio Code)

## Configure SP DEMO Service into the Docker-in-Docker container

1. Download the source code into the `HOME` of your personal computer:

      ```bash
      sudo apt install git
      
      cd $HOME/idem-sp
      
      git clone https://gitlab.dir.garr.it/IDEM/idem-sp/sp-base.git
      ```

2. Follow the Setup instructions in `sp-base/docs/docs/development/deployment.md`.

3. Run Ansible-Playbook to configure the Docker-in-Docker container:

      ```bash
      cd $HOME/idem-sp/
      
      ansible-playbook sp-base/ansible/sp-demo-playbook.yml -i sp-base/ansible/inventories/dev/inventory.ini
      ```

## Structure of the Repository

- `ansible/`: Contains the playbook and roles to configure a new IDEM SP Service instance
- `docs/`: Contains the documentation regarding the project
- `docker/sp-demo/sp-base/Dockerfile`: File needed to create a new Docker image of IDEM SP Service
- `ansible/roles/templates/docker-compose.yml.j2`: Template needed to power up the IDEM SP Service docker environment

## Miscellanous

1. To force a new generation of the configuration file, it is enough to run the following command:

      - `/usr/bin/docker exec <CONTAINER_NAME> bash -c 'python3 idem-arp-generator.py --all'`

2. To take a look of the containers' logs, use:

      - `docker ps`
      - `docker logs -f <CONTAINER_NAME>`
