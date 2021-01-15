#!/bin/sh
# Ubuntu 16.04 install dev requirements

sudo apt update
sudo apt upgrade -y
sudo apt install apt-transport-https ca-certificates curl software-properties-common -y
curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo apt-key add -
sudo add-apt-repository "deb [arch=amd64] https://download.docker.com/linux/ubuntu bionic stable"
sudo apt update
sudo apt install docker-ce -y
# sudo systemctl status docker # checking docker status
sudo usermod -aG docker ${USER}
sudo apt install docker-compose git make -y
# system need to restart after execute