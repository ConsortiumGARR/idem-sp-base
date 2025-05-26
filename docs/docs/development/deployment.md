# Deployment

The deployment of the service is done with Ansible.

## Setup

1. Inventory:

    1. Copy the `sp-base/ansible/inventories/example/inventory-template.ini` file to `sp-base/ansible/inventories/example/inventory.ini`.
    2. Modify it according to your needs.

    To enable the local deployment, just uncomment the line `#localhost ansible_connection=local`.

2. Update the `sp-base/ansible/idem-sp-playbook.yml` file according to your needs.

3. Variables:

    Please follow the instructions listed in the `sp-base/ansible/inventories/example/group_vars/all.yml`

## Deploy

The default command to launch Ansible is:

`ansible-playbook ansible/idem-sp-playbook.yml -i ansible/inventories/example/inventory.ini`
