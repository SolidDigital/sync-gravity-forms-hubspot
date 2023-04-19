=== Gravity Hub Sync ===

== Description ==
This plugin allows you to link your Gravity Forms to the forms in your Hubspot account, so when you submit the Gravity Form, it pushes to the Hubspot form.

This will require a few steps to set up:

**Hubspot steps**
- Marketing > Forms - add new form. Add fields you need.
- Copy portal id from hubspot url. Add to plugin.
- Settings Cog > Integrations > Private App - Add private app; for Scopes enable Standard > Forms. Add access token to plugin.

**Gravity forms steps**
- Add new form - match the fields of the hubspot form.
- Special context variable available `hs_context_pageName`, which you can pull in the page name dynamically.
- Add fields and set the HubSpot Field Name for each
- Add the form ID as a Settings Option for the GF Form
- Currently supports HS fields
  - email
  - hidden
  - text
  - text area

== Contributing ==. 
The code is managed on [github](https://github.com/SolidDigital/hubspot-plugin), and synced to [WordPress' HubSpot Plugin SVN repo](https://plugins.trac.wordpress.org/browser/hubspot-plugin/).

== Installation ==

== Screenshots ==
1. Add your app token and account ID in the Gravity Forms settings menu.
2. Add the hubspot form ID in the form settings.

== Changelog ==
= 1.0.0 =
- Feature: Initial release
