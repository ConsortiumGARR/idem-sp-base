# SP-BASE

DOCKER_BUILDKIT=1 docker build --no-cache -f Dockerfile --target stage .

DOCKER_BUILDKIT=1 docker build --build-arg SP_CERT_PATH="sp.aai-test.garr.it/sp-certs/staging" --no-cache -f sp-base/docker/Dockerfile --target idem -t gitlab.dir.garr.it:4567/idem/idem-sp/sp.aai-test.garr.it:1.0.1 .

docker push gitlab.dir.garr.it:4567/idem/idem-sp/sp.aai-test.garr.it:1.0.1

Per stage intendiamo:

- example
- idem-test
- edugain

FEDERATION_TYPE='IDEM and eduGAIN'
FEDERATION_TYPE='IDEM Test'
FEDERATION_TYPE='Example'

openssl rsa -aes256 -in your.key -out your.encrypted.key
