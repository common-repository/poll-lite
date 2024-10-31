<?php 
if (isset($_POST['merlic_poll_submit'])) {
	update_option('merlic_poll_limit', $_POST['merlic_poll_limit']);
	update_option('merlic_poll_results', $_POST['merlic_poll_results']);
	update_option('merlic_poll_result_details_partial_votes', $_POST['merlic_poll_result_details_partial_votes']);
	update_option('merlic_poll_result_details_total_votes', $_POST['merlic_poll_result_details_total_votes']);

	$output = '<p>'.__('Your settings have been saved').'.</p>';
}

?>

<h3>Preferences</h3>

<form method="POST" accept-charset="utf-8" target="_self" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
    <table class="form-table">
        <tr valign="top">
            <th scope="row">
                <label for="merlic_poll_limit">
                    <?php _e('Limit Votes'); ?>
                </label>
            </th>
            <td>
                <input type="radio" name="merlic_poll_limit" value="0" <?php echo (get_option('merlic_poll_limit')==0?'checked="checked"':''); ?> ><span>No limits, visitors can vote any time</span><br />
                <input type="radio" name="merlic_poll_limit" value="1" disabled="disabled"><span>Visitors can vote only once (available in <a href="http://wordpress.phpanswer.com/poll/" target="_blank">full version</a>)</span><br />
                <input type="radio" name="merlic_poll_limit" value="2" disabled="disabled"><span>Visitors can vote once a day (available in <a href="http://wordpress.phpanswer.com/poll/" target="_blank">full version</a>)</span>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row">
                <label for="merlic_poll_results">
                    <?php _e('Show Results'); ?>
                </label>
            </th>
            <td>
                <input type="radio" name="merlic_poll_results" value="0" <?php echo (get_option('merlic_poll_results')==0?'checked="checked"':''); ?> ><span>Visitors can see results only after they have voted</span><br />
                <input type="radio" name="merlic_poll_results" value="1" disabled="disabled"><span>Anyone can see results (available in <a href="http://wordpress.phpanswer.com/poll/" target="_blank">full version</a>)</span>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row">
                <label for="merlic_poll_result_details">
                    <?php _e('Result Details'); ?>
                </label>
            </th>
            <td>
            	<p><span class="description"><?php _e("Results will always show percentages by default. In addition you may want to:"); ?></span></p>
                <input type="checkbox" name="merlic_poll_result_details_partial_votes" value="1" <?php echo (get_option('merlic_poll_result_details_partial_votes')==1?'checked="checked"':''); ?> ><span>Show the number of votes for each answer</span><br />
                <input type="checkbox" name="merlic_poll_result_details_total_votes" value="1" <?php echo (get_option('merlic_poll_result_details_total_votes')==1?'checked="checked"':''); ?> ><span>Show the total number of votes for the whole poll</span>
            </td>
        </tr>
    </table>
    <br/>
    <br/>
    <input class="button-primary" type="submit" name="merlic_poll_submit" value="<?php _e('Save Options'); ?>" />
	<?php echo $output; ?>
</form>
