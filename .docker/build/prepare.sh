#!/bin/bash

rm -f cednet-driver.tar
eval $(ssh-agent)
ssh-add ~/.ssh/id_rsa
export DOCKER_BUILDKIT=1
docker compose build #--no-cache
docker save -o cednet-driver.tar docker-registry.grupocednet.com.br/cednet-driver:1.0
