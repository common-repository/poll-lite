<!-- ********************** UNINSTALL ********************** -->
<h2>Uninstall</h2>
<?php 
if ($_POST['uninstall']) {
	global $wpdb;
	global $poll_table_name;
	global $pollresult_table_name;
	
	$wpdb->query("DELETE FROM  ".$wpdb->options." WHERE  `option_name` LIKE  '%merlic_poll%'");
	$wpdb->query("DELETE FROM  ".$wpdb->postmeta." WHERE  `meta_key` LIKE  '%merlic_poll%'");
	$wpdb->query("DELETE FROM ".$wpdb->posts." WHERE post_type = 'poll'");
	$wpdb->query("DROP TABLE ".$poll_table_name);
	$wpdb->query("DROP TABLE ".$pollresult_table_name);
	
	echo '<p>All Poll data has been deleted. Now you can deactivate the Poll plugin from the <a href="'.get_permalink().'plugins.php">plugins panel</a></p>';
}
else {
	
?>
<p>
    Are you sure you want to delete all data stored by the Poll plugin?
</p>
<p>
    By accepting you will delete:
    <ol>
        <li>
            All the polls
        </li>
        <li>
            All the votes
        </li>
        <li>
            All the options that have been saved (if you reinstall the plugin you will start again with the default settings)
        </li>
    </ol>
</p>
<form action="<?php echo $_SERVER['REQUEST_URI'].'&tab=uninstall'; ?>" method="POST" accept-charset="utf-8">
    <p class="submit">
        <input class="button-primary" type="submit" name="uninstall" value="<?php _e('Confirm Uninstall'); ?>">
    </p>
</form>
<?php 
}
?>
