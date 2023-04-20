=== Sync Gravity Forms and Hubspot Forms ===

Contributors: soliddigital,lukechinworth,peterajtai,cooperhilscher,danwright 
Tags: elementor, dynamic tags, jet engine, macros 
Tested up to: 6.2 
Stable tag: 1.0.0 
Requires PHP: 7.0 
License: GPLv2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Synchronizes functionality of Gravity Forms and Hubspot forms.


== Description ==

https://vimeo.com/819596594?share=copy

This plugin allows you to link your Gravity Forms to the forms in your Hubspot account, so when you submit the Gravity Form, the data is submitted to Hubspot as if it had come from a Regular form, as opposed to a non-hubspot form.

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

== Contributing ==

The code is managed on [github](https://github.com/SolidDigital/hubspot-plugin), and synced to [WordPress' HubSpot Plugin SVN repo](https://plugins.trac.wordpress.org/browser/hubspot-plugin/).

== Installation ==
1. Download, unzip, and upload the plugin folder to `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

== Screenshots ==
1. Add your app token and account ID in the Gravity Forms settings menu.
2. Add the hubspot form ID in the form settings.

== Changelog ==
= 1.0.0 =
- Feature: Initial release
