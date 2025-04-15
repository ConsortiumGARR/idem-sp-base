# SP-BASE

DOCKER_BUILDKIT=1 docker build --no-cache -f Dockerfile --target stage .

Per stage intendiamo:
- example
- idem-test
- edugain

FEDERATION_TYPE='IDEM and eduGAIN'
FEDERATION_TYPE='IDEM Test'
FEDERATION_TYPE='Example'
