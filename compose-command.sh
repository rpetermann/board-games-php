#!/usr/bin/env sh

set -e

if [ -z "$(docker network ls | grep boardgamesnetwork)" ];
then
  docker network create \
    --driver=bridge \
    --subnet=172.33.0.0/16 \
    --gateway=172.33.0.1 \
    boardgamesnetwork
fi

(cd config/server && exec docker-compose -p "board-games-php" ${@})