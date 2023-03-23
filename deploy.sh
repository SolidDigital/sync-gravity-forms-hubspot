#!/usr/bin/env sh
rsync -rztP --exclude '.idea' --exclude '.git' ./ hubspotsd@hubspotsd.ssh.wpengine.net:/home/wpe-user/sites/hubspotsd/wp-content/plugins/hubspot-plugin-main/
