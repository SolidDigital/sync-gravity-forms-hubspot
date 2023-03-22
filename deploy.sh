#!/usr/bin/env sh
rsync -rztP --exclude '.idea' ./ hubspotsd@hubspotsd.ssh.wpengine.net:/home/wpe-user/sites/hubspotsd/wp-content/plugins/hubspot-plugin-main/
