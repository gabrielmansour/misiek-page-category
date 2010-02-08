=== Misiek Page Category ===
Contributors: Michal Augustyniak
Donate link: http://maugustyniak.com/misiek-page-category
Tags:page, category, categories
Requires at least:2.7.1
Tested up to:2.8.5
Stable tag:2.1

Plugin allows you to create categories for pages.


== Description ==

Plugin allows you to create categories for pages and display them as a widget.

All updates available in changlog section


== Installation ==

1. Upload `misiek-page-category` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Manage albums from administration menu..

= In order to upgrade to 2.1 version you must reinstall the plugin by simply clicking uninstall and install again = 
This process should be not necessary if you use auto upgrade. But if something will go wrong please uninstall and the install again.

Uninstallation will not remove your categories created before but just in case please make a backup of your database before any upgrade. 

== Screenshots ==

1. Option under Pages section
2. Adding/List of Categories
3. Add Widget
4. Display Widget
5. List of Categories on "Add New" page 
6. Add Widget ( view in 2.8.x WP version ), more options in plugin 2.1 version.


== Frequently Asked Questions ==

= How can I display all or individual categories in a page not in the widget ? =

In order to do it you need to enable php in your page body by exex-php plugin, then simply use below function:

`mpc_widget_categories($title, $total, $expend, $category_names, $category_ids);`

**$title** variable displays title name of page category and must be false or string. 

**$total** variable, if set true it displays total pages in (n) next to the category name. Must be true or false.

**$extend** variable if set true, you won't see a links under categories (added this for users request, you always should set false here), must be true or false.

**$category_names** variable must be an array with your pages names, correct format is:

array('Some Name', 'Another Name');

**$category_ids** variable must be an array with your pages id's, correct format is:

array(1, 2);

= version 2.1 allow more options = 

`mpc_widget_categories($title, $total, $expend, $category_names, $category_ids, $desc, $catpages_in_uncat)`

**$desc** variable , if set to true the description under category name will be displayed

**$catpages_in_uncat** variable, if set to true, the pages belongs to the categories will be displayed under Uncategorized category.


== Changelog ==

= version 2.1 - 10/21/2009 = 

* added option to create a page for category !
* added option to display category description under category name
* added updateable category informations: name, description
* added option to display or not pages belongs to category under 'Uncategorized' category

= version 2.0 - 10/16/2009 =

Added more featured for 2.8.x WP version, such us:

* changable name of the widget
* ability to show number of pages next to the ctagory name
* ability to expend/collapse links under categories
* select individual categories per widget
* usage php function `mpc_widget_categories()` to show page categories on individual page. See instruction in Faq section.
* added "current" css class to li link tag if this post is on 

Bug Fixed
* do not show not published pages links  

= version 1.2 =

* fixed several bugs displaying not existing category pages
* added order of displaying pages for category by post_title, changeable only in source code

== Bugs ==

= version 2.1 = 
* I have noticed that upgrade from 2.0 to 2.1 made Uncategorized category hidden, then went to categories page and edited one of the category already created, that fixed the problem.
Don't see the relations with that action and not sure why that happend.

Please notify me if you will notice that issue !!!