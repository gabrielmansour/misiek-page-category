<?php 
class MPC_Widget extends WP_Widget {
	function MPC_Widget() {
		$widget_ops = array('classname' => 'page_categories_links', 'description' => 'A list or dropdown of page categories' );
		$this->WP_Widget('page_categories_links', 'Page Categories', $widget_ops);
	}

	function widget($args, $instance) {
		//$instance['title'] = $instance['title'] != '' ? $instance['title'] : false; 
		$instance['category_ids'] = array_keys((array)$instance['category_ids']);
		$instance['category_names'] = array_keys((array)$instance['category_names']);
		mpc_widget_categories($instance['title'], $instance['total'], $instance['expend'], (array)$instance['category_names'], (array)$instance['category_ids'], $instance['desc'], $instance['catpages_in_uncat']);
	}

	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['total'] = $new_instance['total'];
		$instance['expend'] = $new_instance['expend'];
		return $new_instance;
	}

	function form($instance) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'total' => false, 'expend' => false, 'category_ids' => array(),'category_names' => array(), 'desc' => false, 'catpages_in_uncat' => false) );
		$title = strip_tags($instance['title']);
		$total = $instance['total'];
		$expend = $instance['expend'];
		$desc = $instance['desc'];
		$category_ids = $instance['category_ids'];
		$category_names = $instance['category_names'];
		$catpages_in_uncat = $instance['catpages_in_uncat'];
		
		?>
		
		<p><label for="<?php echo $this->get_field_id('title'); ?>">Title: <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo attribute_escape($title); ?>" /></label></p>
		<p><label for="<?php echo $this->get_field_id('total'); ?>"><input class="widefat" id="<?php echo $this->get_field_id('total'); ?>" name="<?php echo $this->get_field_name('total'); ?>" type="checkbox" <?php if ($total == 'on') { echo 'checked="checked"';}?>  /> Display total pages in (n) next to the category name.</label></p>
		<p><label for="<?php echo $this->get_field_id('expend'); ?>"><input class="widefat" id="<?php echo $this->get_field_id('expend'); ?>" name="<?php echo $this->get_field_name('expend'); ?>" type="checkbox" <?php if ($expend == 'on') { echo 'checked="checked"';}?>  /> Collapse all categories.</label></p>
		<p><label for="<?php echo $this->get_field_id('desc'); ?>"><input class="widefat" id="<?php echo $this->get_field_id('desc'); ?>" name="<?php echo $this->get_field_name('desc'); ?>" type="checkbox" <?php if ($desc == 'on') { echo 'checked="checked"';}?>  /> Display category description.</label></p>
		<p><label for="<?php echo $this->get_field_id('catpages_in_uncat'); ?>"><input class="widefat" id="<?php echo $this->get_field_id('catpages_in_uncat'); ?>" name="<?php echo $this->get_field_name('catpages_in_uncat'); ?>" type="checkbox" <?php if ($catpages_in_uncat == 'on') { echo 'checked="checked"';}?>  /> Show pages belongs to categories under Uncategorized category.</label></p>
		<p>Select Categories:</p>
		
		<?php
		
		$mpc_categories = mpc_get_categories();

		foreach((array)$mpc_categories as $category) {
			$p_categories = mpc_get_page_category($category->category_id);
			
			if (array_key_exists($category->id, $category_ids)) {
				$checked = "checked='checked'";
			} else {
				$checked = '';
			}
			
			?>
			
			<p><label for="<?php echo $this->get_field_id('category_ids_' . $category->id); ?>"><input class="widefat" id="<?php echo $this->get_field_id('category_ids_' . $category->id); ?>" name="<?php echo $this->get_field_name('category_ids') . "[{$category->id}]"; ?>" type="checkbox"  <?php echo $checked ?>/> <?php echo $category->name ?></label>
			
			<?php
		}
		
		if (array_key_exists('Uncategorized', $category_names)) {
			$checked = "checked='checked'";
		} else {
			$checked = '';
		}
		
		?>
		<p><label for="<?php echo $this->get_field_id('category_names'); ?>"><input class="widefat" id="<?php echo $this->get_field_id('category_names'); ?>" name="<?php echo $this->get_field_name('category_names') . "[Uncategorized]"; ?>" type="checkbox"  <?php echo $checked ?>/> Uncategorized</label>

		<p>If non selected, widget will display all categories.</p>
		
		<?php 
	}	
}

function register_MPC_Widget(){
	register_widget('MPC_Widget');
}

add_action('init', 'register_MPC_Widget', 1)
?>