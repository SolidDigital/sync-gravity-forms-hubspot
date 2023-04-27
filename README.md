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

This will require a few steps to set up. We recommend using a text editor to hold account ids and app tokens as you copy/paste them between Hubspot and Gravity Forms.

**Hubspot steps**
- Marketing > Forms - Add a new form or pick an existing form.
- with your chosen forms submissions page open, copy the form ID. This is a 37 character string following `/forms/` in the URL.
- Go to Settings cog > Integrations > Private App - Add private app; for Scopes enable Standard > Forms
- Copy your newly created Private App Token from here.
- Copy your account ID. It should be the string of numbers following `/private-apps/` in the page url.

**Gravity forms steps**
- From the WP dashboard, go to Forms > Settings in the sidebar.
- Click "Hubspot Sync"
- Paste in your Private App Token and Account ID in the respective text inputs, and click "save settings".
- Click Forms > New Form in the sidebar.
- Choose the "Blank Form" template. Name it whatever strikes your fancy.
- Click "Create Blank Form".
- Don't add any fields to this new form.
- Go to the form settings > Hubspot Sync. 
- Paste your Hubspot form ID into the text input, and click "Save settings & sync fields"


From here, a couple things will happen. First, the plugin will recreate all of the fields from your Hubspot form on this new Gravity Form. Second, all submissions to this form will be sent along to Hubspot and treated the same as submissions to the Hubspot form.

- Currently supported HS fields
  - email
  - hidden
  - text
  - text area

== Contributing ==

The code is managed on [github](https://github.com/SolidDigital/gravity-hub-sync)

== Installation ==
1. Download, unzip, and upload the plugin folder to `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

== Screenshots ==
1. Add your app token and account ID in the Gravity Forms settings menu.
2. Add the hubspot form ID in the form settings.

== Changelog ==
= 1.0.0 =
- Feature: Initial release
