# HubSpot Plugin

This plugin allows you to link your gravity forms to hubspot forms, so when you submit the gravity form, it pushes to the hubspot form.

## Hubspot steps

* Marketing > Forms - add new form. Add fields you need.
* Copy portal id from hubspot url. Add to plugin.
* Settings Cog > Integrations > Private App - Add private app; for Scopes enable Standard > Forms. Add access token to plugin.

## Gravity forms steps

* Add new form - match the fields of the hubspot form.
* Add css class to each field to identify which hubspot field it goes to, e.g. `hs_field_firstname`
* Add hidden field with label `hs_formid`, and add the form id as the default value.
* Special context variable available `hs_context_pageName`, which you can pull in the page name dynamically.

## TODO

* turn it into a GF add on. currently it just uses plain old wp hooks.
* add admin settings to add the portal id and access token in admin. the GF addon might make this easier.
* support hubspot "consent" fields.
* include hubspot tracking script on all pages for hubspot cookie to work.
