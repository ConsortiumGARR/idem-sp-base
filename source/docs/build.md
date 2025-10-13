# Docker Image Build

## Environment Configuration

The following instructions are tested on a Debian/Ubuntu server.

1. Install required packages on the development environment:

    - `sudo apt install git`

2. Install Docker and Docker Compose:

    - <https://docs.docker.com/engine/install/debian/>
    - <https://docs.docker.com/engine/install/linux-postinstall/>

3. Clone the GIT Repository into the `$HOME/sp-base` directory:

    - `cd $HOME`
    - `git clone https://github.com/ConsortiumGARR/idem-sp-base.git sp-base`

## Usage

### 1 - Dockerfile

If you neeed to change the Dockerfile in order, for example, to update the debian base image, you can find it in `$HOME/sp-base/docker/Dockerfile`.

It is a multi-stage Dockerfile:

- `base`: it contains all the installation of the processes that were described before.
- `example`: it contains the configuration that allows you to have a complete SP example.
- `idem`: it contains all the configuration needed to work within the IDEM federation.

### 2 - Build the Docker image

1. Move to the sp-base folder:

    - `cd $HOME/sp-base`

2. Build the image depending on what you want to achieve:

    a. Base image - this is a template image, used only to check all the packages installed and all the templates/files used by the image:

    - `docker build --no-cache -f sp-base/docker/Dockerfile --target base -t {{ docker_image_location }}{{ docker_image_name }}:{{ docker_image_version }} .`

    b. Custom image - this is a working image. If you don't have any certificates you can leave the `sp-base/docker/shibboleth/certs` folder empty and they will be automatically created:

    - `docker build --build-arg SP_CERT_PATH="sp-base/docker/shibboleth/certs" --build-arg SP_PRIVPOLICY_PATH="sp-base/docker/web/policy" --no-cache -f sp-base/docker/Dockerfile --target custom -t {{ docker_image_location }}{{ docker_image_name }}:{{ docker_image_version }} .`

    c. Idem image - the difference from the example one is that this image use [MDX](https://mdx.idem.garr.it/en/) as Metadata provider and the configuration to use the [Embedded Discovery Service with MDX](https://mdx.idem.garr.it/en/EDS/shibboletheds/):

    - `docker build --build-arg SP_CERT_PATH="sp-base/docker/shibboleth/certs" --build-arg SP_PRIVPOLICY_PATH="sp-base/docker/web/policy" --no-cache -f sp-base/docker/Dockerfile --target idem -t {{ docker_image_location }}{{ docker_image_name }}:{{ docker_image_version }} .`

[Semantic Versioning](https://semver.org/) is generally used to generate new Docker images.

## Testing

### 1 - Run Docker

- `docker run --name sp-base-local -d {{ docker_image_location }}{{ docker_image_name }}:{{ docker_image_version }}`

## Support

- Calogero Costa (<calogero.costa@garr.it>)
- Mario Di Lorenzo (<mailto:mario.dilorenzo@garr.it>)

## Authors

- Calogero Costa (<calogero.costa@garr.it>)
- Mario Di Lorenzo (<mailto:mario.dilorenzo@garr.it>)

## License

TBD
