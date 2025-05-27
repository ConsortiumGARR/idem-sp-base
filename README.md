# SP-BASE

**SP-BASE** is a lightweight **SAML attribute viewer** designed to help visualize attributes released by an Identity Provider (IdP) during authentication.
It is primarily intended as a **test Service Provider**, but can also be used in production-like environments, including within **federations** such as **IDEM** and **eduGAIN**.

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
