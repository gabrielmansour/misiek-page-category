<div class="wrap nosubsub">
<div id="icon-edit" class="icon32"><br />
</div>
<h2>Page Categories</h2>

<br class="clear" />

<div id="col-container">

<div id="col-right">
<div class="col-wrap">

<form id="posts-filter" action="" method="post">
<div class="tablenav">

<div class="alignleft actions">
<select name="action">
	<option value="" selected="selected"><?php _e('Bulk Actions'); ?></option>
	<option value="delete"><?php _e('Delete'); ?></option>
</select> 

<input type="submit" value="<?php _e('Apply'); ?>" name="doaction" id="doaction" class="button-secondary action" /></div>

<br class="clear" />
</div>

<div class="clear"></div>

<table class="widefat fixed" cellspacing="0">
	<thead>
		<tr>
			<th id="cb" class="manage-column column-cb check-column" style="" scope="col"><input type="checkbox" class='checkall'/></th>
			<th id="name" class="manage-column column-name" style="" scope="col">Name</th>
			<th id="description" class="manage-column column-description" style="" scope="col">Description</th>
			<th id="posts" class="manage-column column-posts num" style="" scope="col">Pages</th>
		</tr>
	</thead>

	<tfoot>
		<tr>
			<th id="cb" class="manage-column column-cb check-column" style="" scope="col"><input type="checkbox" class='checkall'/></th>
			<th id="name" class="manage-column column-name" style="" scope="col">Name</th>
			<th id="description" class="manage-column column-description" style="" scope="col">Description</th>
			<th id="posts" class="manage-column column-posts num" style="" scope="col">Pages</th>
		</tr>
	</tfoot>

	<tbody id="the-list" class="list:cat">
	<?php foreach((array)$categories as $category):?>
		<?php if ($i == 1) {$class = 'alternate';$i=0;} else {$class = '';$i++;} ?>
		<tr id="cat-<?php print $category->id?>" class="iedit <?php print $class ?>">
			<th  class="check-column" scope="row"><input type="checkbox" name='cat[]' value="<?php print $category->id?>"/></th>
			<td class="name column-name">
				<?php print mpc_get_category_name($category->id) ?>
				<div class="row-actions">
				<span class="edit" id='<?php print $category->id?>'>
					
					<span><a class="editinline" href="?page=add-category&edit=true&id=<?php print $category->id?>">Edit</a></span>
					
				</div>

				
			</td>
			<td class="description column-description"><?php print $category->description ?></td>
			
			<td class="posts column-posts num">( <?php print mpc_get_category_page_number($category->id)?> )</td>
		</tr>
	
	<? endforeach;?>
	</tbody>
</table>

<div class="tablenav">

<div class="alignleft actions"><select name="action2">
	<option value="" selected="selected"><?php _e('Bulk Actions'); ?></option>
	<option value="delete"><?php _e('Delete'); ?></option>
</select> <input type="submit" value="<?php _e('Apply'); ?>" name="doaction2" id="doaction2" class="button-secondary action" /></div>

<br class="clear" />
</div>

</form>
<div class="form-wrap">
<p><strong>Note:</strong><br/>Deleting a category does not delete the pages in that category or the page belongs to that category.</p>
</div>

</div>
</div>
<!-- /col-right -->

<div id="col-left">

<div class="col-wrap">

<div class="form-wrap">

<form name="addcat" id="addcat" method="post" action="" class="add:the-list: validate">

<?php 

if ($_GET['edit'] == 'true') {
	$action = 'Edit';
	echo '<input name="edit_category" value="true"  type="hidden">';
} else {
	$action = 'Add';
	echo '<input name="add_category" value="true"  type="hidden">';
}

?>

<h3><?php echo $action ?> Category</h3>
<div id="ajax-response"></div>

<div class="form-field form-required"><label for="cat_name">Category Name</label> 
<input name="cat_name" id="cat_name" value="<?php echo $category_edit->name ?>" size="40" aria-required="true" type="text">
<p>The name is used to identify the category.</p>
</div>

<?php  if ($category_edit->post_id): ?>
<div class="form-field form-required"><label for="cat_name">It appears that this category has page already created.</label>
<input name="cat_post_del" id="cat_post_del" value="" size="40" aria-required="true" type="checkbox"> Delete Page
<p>If Checked, page for this category will be permanently removed.</p>
</div>
<?php  else: ?>
<div class="form-field form-required"><label for="cat_name"></label>
<input name="cat_post" id="cat_post" value="" size="40" aria-required="true" type="checkbox"> Create Page
<p>If Checked, page for category will be created.</p>
</div>
<?php  endif; ?>


<div class="form-field"><label for="category_description">Description</label> 
<textarea name="cat_description" id="category_description" rows="5" cols="40"><?php echo $category_edit->description ?></textarea>
<p>The description will be displayed under category name. Option in widget must be enabled.</p>
</div>

<p class="submit"><input class="button" name="submit" value="<?php echo $action ?> Category" type="submit"></p>
</form>
</div>

</div>

</div>
<!-- /col-left --></div>
<!-- /col-container --></div>
<!-- /wrap -->