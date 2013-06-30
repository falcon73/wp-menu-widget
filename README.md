# Wordpress Menu widget

Widget outputs menus that were generated in Menu Editor.

Features

* Output widget specific menu

Specify menus to show to logged in and guest users separately.

* Output subpages list

Automatically outputs the list of child pages for current active page.

* Output page specific menu 

Use this option when you need to output specific static (generated in Menu Editor) menu on a specific page. After plugin is activated "Select menu" metabox is added to Pages and you can choose the menu you want to output directly from page editing screen below the main editor.

You can also embed widget functionality in template with function:

```
wp_menu_output( $loggedmenu, $outputmenu, $output_widget_specific_menu = false, $output_subpages = false, $output_page_specific_menu = false)
```	

Or in page with shortcode:

```
[wp_menu_output]
```	

Parameters:

```loggedmenu``` and ```outputmenu``` - slugs for menus names.

```output_widget_specific_menu```, ```output_subpages```, ```output_page_specific_menu``` - pretty self explanatory parameters. Requires ```true``` or ```false```.
