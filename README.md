# IDEM SP BASE

**IDEM SP BASE** is a containerized Shibboleth Service Provider and a set of ansible roles developed by the IDEM GARR AAI Service to ease service providers deployment. 

Please visit https://www.idem.garr.it for more information on the IDEM GARR AAI Service and the IDEM Federation.
---

IDEM SP BASE includes a lightweight **SAML attribute viewer** designed to help visualize attributes released by an Identity Provider (IdP) during authentication.

It is currently used by the IDEM GARR AAI Service to implement two **test Service Providers** for the IDEM and the eduGAIN Communities. It can be used as a base image to develop Service Providers to be run in production environments, including within **federations** such as **IDEM** and **eduGAIN**.

When accessed via SAML login, SP-BASE presents a summary page displaying the attributes received from the IdP for the current session.

The service also supports **Multi-Factor Authentication (MFA)** by requesting the **[REFEDS MFA Profile](https://refeds.org/profile/mfa)**.

---

## Features

- Acts as an attribute viewer for SAML-based logins
- Displays all attributes released by the IdP
- Supports MFA authentication using the REFEDS MFA Profile
- Federation-ready (IDEM / eduGAIN compatible)
- Easily deployable using **Docker** and **Ansible**

---

## Requirements

This project requires both **Docker** and **Ansible** to build and deploy.

---

## Documentation

- **Architecture overview:** [docs/architecture.md](./docs/docs/architecture.md)  
- **Build instructions:** [docs/development/build.md](./docs/docs/development/build.md)  
- **Deployment guide:**
  - Local Deployment: [docs/development/test-deployment.md](./docs/docs/development/test-deployment.md)
  - Remote Deployment: [docs/development/deployment.md](./docs/docs/development/deployment.md)

## Copyright and license

Copyright 2025 Consortium GARR

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

    http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.


