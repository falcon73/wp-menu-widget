# Wordpress Menu widget

Widget outputs menus that were generated in Menu Editor.

#### Update 1.2.1

* Code cleanup.
* Improved page specific menu output. Now supports guest and logged in user menus.
* Shortcode for pages does not require parameters anymore. It uses "page specific menu output".
* Template function dropped due to instability.

Features

* Output widget specific menu

Specify menus to show to logged in and guest users separately.

* Output subpages list

Automatically outputs the list of child pages for current active page.

* Output page specific menu 

Use this option when you need to output specific static (generated in Menu Editor) menu on a specific page. After plugin is activated "Select menu" metabox is added to Pages and you can choose the menu you want to output directly from page editing screen below the main editor.

Or in page with shortcode:

```
[wp_menu_output]
```	

