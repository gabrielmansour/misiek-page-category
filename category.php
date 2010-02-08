<?php
/*
 Plugin Name: Misiek Page Category [MODIFIED]
 Version: 2.1
 Plugin URI: http://wordpress.org/extend/plugins/misiek-page-category/
 Description: Creates categories for pages and displays them as widget
 Author: Michal Augustyniak; modified by Gabriel Mansour
 Author URI: maugustyniak.com

 Copyright YEAR  PLUGIN_AUTHOR_NAME  (email : misiek303@gmail.com)

 This program is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 2 of the License, or
 (at your option) any later version.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with this program; if not, write to the Free Software
 Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

global $wpdb;
define("mpc_path", ABSPATH . 'wp-content/plugins/misiek-page-category/');

define('MPC_CATEGORIES', $wpdb->prefix . "mpc_categories");
define('MPC_PAGES_CATEGORIES', $wpdb->prefix . "mpc_pages_categories");
define('POSTS', $wpdb->prefix . "posts");

if (version_compare($wp_version, '2.8', '>=')) {
	include_once mpc_path . 'widget.php';
} else {
	add_action('widget_init', 'mpc_widget_init');
}

add_action('admin_menu', 'mpc_config');
add_action('admin_menu', 'mpc_attribute');

register_activation_hook( __FILE__, 'mpc_active');

function mpc_active() {

	$categories = array( 'id' => 'int NOT NULL AUTO_INCREMENT',
	'name' => 'varchar(255) NOT NULL',
	'description' => 'text NOT NULL',
	'PRIMARY' => 'KEY (id)'
	);
	mpc_create_table($categories, MPC_CATEGORIES);

	$pages_categories = array( 'id' => 'int NOT NULL AUTO_INCREMENT',
	'category_id' => 'int NOT NULL',
	'post_id' => 'int NOT NULL',
	'PRIMARY' => 'KEY (id)'
	);
	mpc_create_table($pages_categories, MPC_PAGES_CATEGORIES);

	// upgrade table to v2.1
	global $wpdb;
	$wpdb->query("ALTER TABLE " . MPC_CATEGORIES . " ADD COLUMN parent_id int null");
	$wpdb->query("ALTER TABLE " . MPC_CATEGORIES . " ADD COLUMN post_id int null");
}

function mpc_create_table($options, $table) {
	global $wpdb;
	$sql = "CREATE TABLE " . $table . '(';
	foreach($options as $column => $option) {
		$sql .= "{$column} {$option}, ";
	}
	$sql = rtrim($sql, ', ') . ")";

	if($wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE '%s'", $table) != $table) {
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
	}
}

function mpc_config() {
	add_submenu_page('edit-pages.php', __('Categories'), __('Categories'), 8, 'add-category', 'mpc_categories_page');
}

function mpc_attribute() {
	add_meta_box('mpc', __('Categories'), 'mpc_attribute_box', 'page', 'side', 'low');
	add_action('edit_post', 'mpc_hook_post_save');
}

function mpc_attribute_box() {
	global $wpdb;
	$categories = mpc_get_categories();

	foreach((array)$categories as $category) {
		if ($wpdb->get_results($wpdb->prepare("select * from " . MPC_PAGES_CATEGORIES . " where category_id = '%d' and post_id = '%d'", $category->id, $_GET['post']),'ARRAY_A')) {
			$checked = "checked=''";
		} else {
			$checked = "";
		}

		print "<label><input {$checked} type='checkbox' name='categories[]' value='{$category->id}' /> " . $category->name . "</label><br/>";
	}
}

function mpc_categories_page() {
	global $wpdb;

	if ($_POST['add_category']) {
		
		if (isset($_POST['cat_post'])) {
			$post_id = mpc_create_page($_POST['cat_name']);
		}	else {
			$post_id = null;
		}

		$wpdb->query($wpdb->prepare("insert into " . MPC_CATEGORIES . " (name, description, parent_id, post_id) values ('%s','%s','%d', '%d')"), $_POST['cat_name'], $_POST['cat_description'], $_POST['parent_id'], $post_id);
		
	} elseif ($_POST['action'] == 'delete') {
		
		foreach((array)$_POST['cat'] as $id) {
			mpc_delete_category($id);
		}
		
	} elseif ($_POST['edit_category']) {
		
		if (isset($_POST['cat_post_del'])) {
			
			$category_edit = $wpdb->get_row($wpdb->prepare("select * from " . MPC_CATEGORIES . " where id = %d", $_GET['id']));
			
			if (wp_delete_post($category_edit->post_id)) {
				$post_id = 0;	
			}
			
		} elseif (isset($_POST['cat_post'])) {
			$post_id = mpc_create_page($_POST['cat_name']);
		}
		
		$wpdb->query($wpdb->prepare("update " . MPC_CATEGORIES . " set name = '%s', description = '%s', post_id = '%d' where id = %d"), $_POST['cat_name'], $_POST['cat_description'], $post_id, $_GET['id']);
		echo '<SCRIPT language="JavaScript">window.location="/wp-admin/edit-pages.php?page=add-category"</SCRIPT>';
		
	} elseif ($_GET['edit'] == 'true') {
		
		$category_edit = $wpdb->get_row($wpdb->prepare("select * from " . MPC_CATEGORIES . " where id = %d"), $_GET['id']);
		
	}

	$categories = mpc_get_categories();
	include mpc_path . 'categories.php';
}

function mpc_hook_post_save($ID) {
	global $wpdb;

	mpc_delete_all_pages($_POST['post_ID']);

	foreach((array)$_POST['categories'] as $category_id) {
		$wpdb->query($wpdb->prepare("insert into " . MPC_PAGES_CATEGORIES . " (post_id, category_id) values ('%d','%d')"), $_POST['post_ID'], $category_id);
	}
}

function mpc_widget_categories($title = false, $total = false, $expend = true, $category_names = array(), $category_ids = array(), $desc = false, $catpages_in_uncat = false) {
	global $wpdb;
	global $post;
	
	$categories = mpc_all_get_page_categories($category_names, $category_ids);

	if ((in_array('Uncategorized', $category_names) || array_key_exists('Uncategorized', $category_names)) || (!$category_names && !$category_ids)) {
		$uncategorized = mpc_get_uncategorized_pages($catpages_in_uncat);
	}

	if($title===false) {
		$title = 'Page Categories';
	}
	// Set title to NULL to hide it
	if ($title===null)
	  $title = '';
	else 
  	$title = '<h2 class="widgettitle">'.$title.'</h2>';

	print $title . '<ul class="mpc_pages_categories">';

	foreach((array)$categories as $category) {
		$p_categories = mpc_get_page_category($category->category_id);

		if (count($p_categories) > 0) {
			
			if ($category->post_id > 0) {
				$cat_post = get_permalink($category->post_id);
				$class = '';
				if ($post->ID == $category->post_id) {
					$class = 'current';
				}
				echo "<li class=\"{$class}\"><h3><a href=\"{$cat_post}\">$category->name</a></h3>";
			} else {
				echo "<li><h3>$category->name</h3>";
			}
			
			if ($total) {
				echo "(" . count($p_categories). ")";
			}

			if ($desc) {
				echo "<p>" . $category->description . "</p>";
			}

			echo "<ul>";

			if (!$expend) {
				foreach((array)$p_categories as $p_category) {
					$inner_post = &get_post($p_category->post_id);
					$link = get_permalink($p_category->post_id);
					$class = '';
					if ($post->ID == $p_category->post_id) {
						$class = ' class="current"';
					}
					print "<li{$class}><a href=\"{$link}\">" . $inner_post->post_title . "</a></li>";
				}
			}
			
			print "</ul></li>";
		}
	}

	if ($uncategorized)  {
		echo "<li>Uncategorized ";

		if ($total) {
			echo "(" . count($uncategorized). ")";
		}

		echo "</li><ul>";

		if (!$expend) {

			foreach((array)$uncategorized as $uncat_post) {
				
				$cat_post = &get_post($uncat_post->id);
				$link = get_permalink($uncat_post->id);
				
				$class = '';
				if ($post->ID == $uncat_post->id) {
					$class = 'current';	
				}
				
				print "<li class='post_{$cat_post->ID} page_catagory {$class}' ><a href='{$link}' >" . $cat_post->post_title . "</a></li>";
			}

		}
		print "</ul>";
	}

	print "</ul>";
}

function mpc_delete_category($id) {
	global $wpdb;
	return $wpdb->query($wpdb->prepare("delete from  " . MPC_CATEGORIES . " where id = '%d'"), $id);
}

function mpc_get_categories() {
	global $wpdb;
	return $wpdb->get_results("select * from " . MPC_CATEGORIES  . " order by name asc");
}

function mpc_delete_all_pages($id) {
	global $wpdb;
	return $wpdb->query($wpdb->prepare("delete from  " . MPC_PAGES_CATEGORIES . " where post_id = '%d'"), $id);
}


function mpc_get_childrens_for($category_id) {
	global $wpdb;
	$categories = $wpdb->get_results($wpdb->prepare("select * from " . MPC_CATEGORIES  . " where parent_id = %d"), $category_id);
	foreach($categories as $category) {
		$childrens[] = $category->id;
	}
	return $childrens;
	
}

function mpc_all_get_page_categories($category_names = array(), $category_ids = array(), $cascade = false) {
	global $wpdb;

	$conditions = '';

	foreach($category_names as $name) {
		$conditions[] = $wpdb->prepare(MPC_CATEGORIES . ".name = '%s", trim($name));
	}

	foreach($category_ids as $id) {
		$conditions[] = $wpdb->prepare(MPC_CATEGORIES . ".id = '%d'", $id);
	}
	
	if ($category_names || $category_ids || $cascade) {
		 $conditions = "WHERE (" . join(' or ', $conditions) . ")";
	}
	
	return $wpdb->get_results("select * from " . MPC_PAGES_CATEGORIES . " inner join " . MPC_CATEGORIES . " on " . MPC_CATEGORIES . ".id = " . MPC_PAGES_CATEGORIES . ".category_id {$conditions} group by category_id ;");
}

function mpc_get_page_category($id, $order_by = "menu_order,post_title") {
	global $wpdb;
	return $wpdb->get_results($wpdb->prepare("select * from " . MPC_PAGES_CATEGORIES . " inner join " . POSTS . " on " . POSTS . ".ID = " . MPC_PAGES_CATEGORIES . ".post_id where post_status = 'publish' and category_id = %d order by %s"), $id, $order_by));
}

function mpc_get_category_page_number($id) {
	global $wpdb;
	$data = $wpdb->get_row($wpdb->prepare("select count(" . MPC_PAGES_CATEGORIES . ".id) as num from " . MPC_PAGES_CATEGORIES . " inner join " . POSTS . " on " . POSTS . ".ID = " . MPC_PAGES_CATEGORIES . ".post_id where category_id = %d;", $id),'ARRAY_A');
	return $data['num'];
}

function mpc_get_uncategorized_pages($catpages_in_uncat = false) {
	global $wpdb;
	
	if (!$catpages_in_uncat) {
		$conditions = "and " . POSTS . ".id not in (select post_id from wp_mpc_categories)";
	}
	
	return $wpdb->get_results("select " . POSTS . ".id as id from " . POSTS . " left join " . MPC_PAGES_CATEGORIES . " on " . POSTS . ".id = " . MPC_PAGES_CATEGORIES . ".post_id where post_type = 'page' and post_status = 'publish' and " . MPC_PAGES_CATEGORIES . ".id is NULL {$conditions} group by " . POSTS . ".id");
}

function mpc_get_category_name($id) {
	if ($id > 0) {
		global $wpdb;

		$category = $wpdb->get_row($wpdb->prepare("select * from " . MPC_CATEGORIES  . " where id = %d", $id));
		if ($category) {
			$link = get_permalink($category->post_id);
				
			if ($link) {
				return "<a href='{$link}'>{$category->name}</a>";
			}
				
			return $category->name;
		}
	}
}

function mpc_create_page($name) {
	$my_post = array();

	if (file_exists(ABSPATH . WPINC . '/pluggable.php')) {

		require (ABSPATH . WPINC . '/pluggable.php');
		get_currentuserinfo();

		$my_post['post_author'] = $current_user->id;

	} else {
		$my_post['post_author'] = 1;
	}

	$my_post['post_title'] = "Page for the '" . $name . "' Category";
	$my_post['post_status'] = 'publish';

	$my_post['post_type'] = 'page';
	$my_post['page_template'] = 'default';

	return wp_insert_post($my_post);
}

?>