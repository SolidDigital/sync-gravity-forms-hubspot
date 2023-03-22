# HubSpot Plugin

This plugin allows you to link your gravity forms to hubspot forms, so when you submit the gravity form, it pushes to the hubspot form.

## Dev workflow
- Clone the project to your local
- dev
- run `./deploy.sh` to deploy your changes to https://hubspotsd.wpengine.com/

## Hubspot steps
- Marketing > Forms - add new form. Add fields you need.
- Copy portal id from hubspot url. Add to plugin.
- Settings Cog > Integrations > Private App - Add private app; for Scopes enable Standard > Forms. Add access token to plugin.

## Gravity forms steps
- Add new form - match the fields of the hubspot form.
- Special context variable available `hs_context_pageName`, which you can pull in the page name dynamically.
- Add fields and set the HubSpot Field Name for each
- Add the form ID as a Settings Option for the GF Form
- Currently support HS fields
  - email
  - hidden
  - text
  - text area

## TODO
- [ ] turn it into a GF add on. currently it just uses plain old wp hooks.
- [ ] add admin settings to add the portal id and access token in admin. the GF addon might make this easier.
- [ ] support hubspot "consent" fields.
- [ ] add context support back in
- [ ] include hubspot tracking script on all pages for hubspot cookie to work.
-
