#!/bin/bash

docker-compose --env-file ./.env -f ./makermanager/docker-compose.prod.yml down
