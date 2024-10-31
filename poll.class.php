<?php 
class Merlic_Poll {

    public function init() {
    
        self::create_post_type();
        //self::add_meta_boxes();
        
        if (!wp_script_is('jquery', 'queue')) {
            wp_enqueue_script("jquery");
        }
        
        //default stylesheet
        $css_url = WP_PLUGIN_URL.'/poll-lite/style/default.css';
        $css_dir = WP_PLUGIN_DIR.'/poll-lite/style/default.css';
        self::include_css($css_url, $css_dir, 'merlic_poll_css_default');
        
        //default js
        $default_js_url = WP_PLUGIN_URL.'/poll-lite/js/default.js';
        $default_js_dir = WP_PLUGIN_DIR.'/poll-lite/js/default.js';
        self::include_js($default_js_url, $default_js_dir, 'merlic_poll_js_default');
    }
    
    public function admin_menu() {
        add_options_page("Poll Lite", "Poll Lite", 'manage_options', 'merlic_poll_admin', array('Merlic_Poll', "draw_admin_menu"));
    }
    
    public function draw_admin_menu() {
        include 'admin/options-page.php';
    }
    
    public function shortcode($atts) {
        global $wpdb;
        global $poll_table_name;
        global $pollresult_table_name;
        
        //extract shortcode attributes
        extract(shortcode_atts(array('id'=>''), $atts));
        
        if (get_post_status($id) == 'publish') {
        
            $poll = self::get_poll($id);
            
            $output .= '<h3>'.$poll['question'].'</h3>';
            
            if (is_array(get_post_meta($id, 'show_answers', true))) {
            
                if ($_POST['poll_id'] == $id) {
                    //-------------------- show results -------------------------//
                    if (isset($_POST['submit_poll'])) {
                    
                        if (self::can_vote($_POST['poll_id'])) {
                            //store vote here
                            $query = "INSERT INTO `$pollresult_table_name` VALUES(NULL, '$id', '".$_POST['merlic_poll_vote']."', NULL, '".self::get_real_ip_address()."', NOW())";
                            $wpdb->query($query);
                        }
                    }
                    
                    $output .= '<p>';
                    foreach (range(1, 5) as $i) {
                        if (in_array($i, get_post_meta($id, 'show_answers', true))) {
                            $output .= '
								<div class="merlic_poll" style="margin-right: 20px;">'.$poll['a'.$i].'</div>
								<span class="merlic_poll_result"><b>'.self::get_votes_perc($id, $i).'%</b></span>';
								
                            if (get_option('merlic_poll_result_details_partial_votes') == 1)
                                $output .= '<span class="merlic_poll_result">&nbsp;('.self::get_votes($id, $i).' '.__('votes').')</span>';
                                
                            $output .= '<br />';
                        }
                    }
                    $output .= '</p>';
                    
                    if (get_option('merlic_poll_result_details_total_votes') == 1)
                        $output .= '<p><span class="merlic_poll_result">('.self::get_votes_total($id).' '.__('votes received').')</span></p>';
                } else {
                    //-------------------- show poll -------------------------//
                    $output .= '<form action="'.$_SERVER['REQUEST_URI'].'" method="post">';
                    
                    foreach (range(1, 5) as $i) {
                        if (in_array($i, get_post_meta($id, 'show_answers', true)))
                            $output .= '<input type="radio" name="merlic_poll_vote" value="'.$i.'"/>'.$poll['a'.$i].'<br />';
                    }
                    
                    $output .= '<br />';
                    
                    $output .= '<input type="submit" class="merlic_poll_submit" name="submit_poll" value="'.__('Vote').'" disabled="disabled"/>';
                    
                    $output .= '<input type="hidden" name="poll_id" value="'.$id.'" />';
                    
                    $output .= '</form>';
                }
            } else
                $output .= __('No answers available.');

                
            return '<div class="merlic_poll_container">'.$output.'</div>';
        }
    }
    
    public function widget_title() {
        if (isset($_POST['merlic_poll_widgettitle_submit'])) {
            update_option('merlic_poll_widget_title', $_POST['merlic_poll_widget_title']);
            update_option('merlic_poll_widget_field', $_POST['merlic_poll_widget_field']);
        }
        
        echo '
			<p>
				<label for="merlic_poll_widget_title">'.__('Title:').'</label><br />
				<input name="merlic_poll_widget_title" type="text" value="'.get_option('merlic_poll_widget_title').'" />
			</p>
			<p>
				<label for="merlic_poll_widget_field">'.__('Widget Field:').'</label><br />
				<input name="merlic_poll_widget_field" type="text" value="'.get_option('merlic_poll_widget_field').'" />
			</p>
			<input type="hidden" id="merlic_poll_widgettitle_submit" name="merlic_poll_widgettitle_submit" value="1" />
		';
    }
    
    public function add_meta_boxes() {
        global $post;
        
        add_meta_box('merlic_poll_answers_id', __('Answers'), array('Merlic_Poll', 'draw_answers_box'), 'poll', 'advanced');
        
        if ($post->ID > 0) {
            remove_meta_box('postcustom', 'poll', 'normal');
            add_meta_box('merlic_poll_shortcode_id', __('Shortcode'), array('Merlic_Poll', 'draw_shortcode_box'), 'poll', 'side');
        }
    }
    
    public function draw_answers_box() {
        global $post;
        global $poll_table_name;
        
        $checked = null;
        
        $poll = self::get_poll($post->ID);
        
        $output = '<p>NOTE: only the answers that contain information will show.</p>';
        
        $show_answers = get_post_meta($post->ID, 'show_answers', true);
        
        $output .= '
			<table id="merlic_poll">
				<tr>
					<th>&nbsp;</th>
					<th>Show</th>
					<th>Output</th>
					<th>Votes</th>
					<th>Percentage</th>
				</tr>';
				
        foreach (range(1, 5) as $i) {
            if (is_array($show_answers))
                $checked = in_array($i, $show_answers) ? 'checked="checked"' : '';
            $output .= '
				<tr>
					<td>Answer '.$i.'</td>
					<td><input type="checkbox" name="merlic_poll_answer_show[]" value="'.$i.'" '.$checked.'/></td>
					<td><input type="text" name="merlic_poll_answer[]" value="'.$poll['a'.$i].'" class="merlic_poll_admin_input"/></td>
					<td class="votes">'.self::get_votes($post->ID, $i).'</td>
					<td class="votes">'.self::get_votes_perc($post->ID, $i).'%</td>
				</tr>';
        }
        
        $output .= '</table>';
        
        echo $output;
    }
    
    public function draw_shortcode_box() {
        global $post;
        
        $output = '<p>Paste this shortcode where you want to display the poll (pages, posts, sidebar):</p>';
        $output .= '<p style="text-align: center; margin-top: 20px;">[merlic_poll id="'.$post->ID.'"]</p>';
        
        echo $output;
    }
    
    public function wp_insert_post() {
        global $wpdb;
        global $post;
        global $poll_table_name;
        
        if ($post->post_type == 'poll') {
        
            $check = "SELECT COUNT(ID) AS num FROM `$poll_table_name` WHERE ID = ".$post->ID;
            $res = $wpdb->get_results($check);
            
            if ($res[0]->num == 0) {
            
                $query = "
					INSERT INTO `$poll_table_name`
					VALUES(
						".$post->ID.",
						'".$_POST['post_title']."',
						'".(isset($_POST['merlic_poll_answer'][0]) ? $_POST['merlic_poll_answer'][0] : '')."',
						'".(isset($_POST['merlic_poll_answer'][1]) ? $_POST['merlic_poll_answer'][1] : '')."',
						'".(isset($_POST['merlic_poll_answer'][2]) ? $_POST['merlic_poll_answer'][2] : '')."',
						'".(isset($_POST['merlic_poll_answer'][3]) ? $_POST['merlic_poll_answer'][3] : '')."',
						'".(isset($_POST['merlic_poll_answer'][4]) ? $_POST['merlic_poll_answer'][4] : '')."',
						'".(isset($_POST['merlic_poll_answer'][5]) ? $_POST['merlic_poll_answer'][5] : '')."',
						'".(isset($_POST['merlic_poll_answer'][6]) ? $_POST['merlic_poll_answer'][6] : '')."',
						'".(isset($_POST['merlic_poll_answer'][7]) ? $_POST['merlic_poll_answer'][7] : '')."',
						'".(isset($_POST['merlic_poll_answer'][8]) ? $_POST['merlic_poll_answer'][8] : '')."',
						'".(isset($_POST['merlic_poll_answer'][9]) ? $_POST['merlic_poll_answer'][9] : '')."',
						NOW()
					)
				";
            } else {
                $query = "
					UPDATE `$poll_table_name`
					SET
						question = '".$_POST['post_title']."',
						a1 = '".(isset($_POST['merlic_poll_answer'][0]) ? $_POST['merlic_poll_answer'][0] : '')."',
						a2 = '".(isset($_POST['merlic_poll_answer'][1]) ? $_POST['merlic_poll_answer'][1] : '')."',
						a3 = '".(isset($_POST['merlic_poll_answer'][2]) ? $_POST['merlic_poll_answer'][2] : '')."',
						a4 = '".(isset($_POST['merlic_poll_answer'][3]) ? $_POST['merlic_poll_answer'][3] : '')."',
						a5 = '".(isset($_POST['merlic_poll_answer'][4]) ? $_POST['merlic_poll_answer'][4] : '')."',
						a6 = '".(isset($_POST['merlic_poll_answer'][5]) ? $_POST['merlic_poll_answer'][5] : '')."',
						a7 = '".(isset($_POST['merlic_poll_answer'][6]) ? $_POST['merlic_poll_answer'][6] : '')."',
						a8 = '".(isset($_POST['merlic_poll_answer'][7]) ? $_POST['merlic_poll_answer'][7] : '')."',
						a9 = '".(isset($_POST['merlic_poll_answer'][8]) ? $_POST['merlic_poll_answer'][8] : '')."',
						a10 = '".(isset($_POST['merlic_poll_answer'][9]) ? $_POST['merlic_poll_answer'][9] : '')."'
					WHERE ID = ".$post->ID;
					
            }
            
            $wpdb->query($query);
            update_post_meta($post->ID, 'show_answers', (isset($_POST['merlic_poll_answer_show']) ? $_POST['merlic_poll_answer_show'] : ''));
        }
        
    }
    
    public function delete_post($pid) {
        global $wpdb;
        global $poll_table_name;
        global $pollresult_table_name;
        
        if (get_post_type($pid) == 'poll') {
            $query = "DELETE FROM `$poll_table_name` WHERE ID = $pid";
            $wpdb->query($query);
            
            $query = "DELETE FROM `$pollresult_table_name` WHERE pollID = $pid";
            $wpdb->query($query);
        }
    }
    
    //=========================================================================================================================//
    
    private function create_post_type() {
        $poll['labels']['name'] = __('Polls');
        $poll['labels']['singular_name'] = __('Poll');
        $poll['labels']['add_new'] = _x('Add New', 'Poll');
        $poll['labels']['add_new_item'] = __('Add New Poll');
        $poll['labels']['edit_item'] = __('Edit Poll');
        $poll['labels']['not_found'] = __('No polls found');
        $poll['labels']['ot_found_in_trash'] = __('No polls found in trash');
        $poll['public'] = true;
        $poll['show_ui'] = true;
        $poll['hierarchical'] = false;
        $poll['publicly_queryable'] = true;
        $poll['query_var'] = true;
        $poll['rewrite'] = array('slug'=>'poll');
        $poll['supports'] = array('title', 'page-attributes');
        
        register_post_type('poll', $poll);
    }
    
    private function include_css($url, $dir, $handle) {
        if (file_exists($dir)) {
            wp_register_style($handle, $url);
            wp_enqueue_style($handle);
        } else
            wp_die($dir.' not found');
    }
    
    private function include_js($url, $dir, $handle) {
        if (file_exists($dir)) {
            wp_register_script($handle, $url);
            wp_enqueue_script($handle);
        } else
            wp_die($dir.' not found');
    }
    
    private function println($text) {
        if (is_array($text) or is_object($text)) {
            echo '<pre>';
            print_r($text);
            echo '</pre>';
        } else {
            echo '<pre>';
            echo $text;
            echo '</pre>';
        }
        
        echo '<br />'."\n";
    }
    
    private function get_votes($poll_id, $a) {
        global $wpdb;
        global $pollresult_table_name;
        
        $query = "SELECT COUNT(*) AS votes FROM `$pollresult_table_name` WHERE pollID = ".$poll_id." AND answer = '$a'";
        $res = $wpdb->get_results($query);
        
        if ($res[0]->votes > 0)
            $votes = $res[0]->votes;
        else
            $votes = 0;
            
        return $votes;
    }
    
    private function get_votes_total($poll_id) {
        global $wpdb;
        global $pollresult_table_name;
        
        if (is_array(get_post_meta($poll_id, 'show_answers', true))) {
            $query = "SELECT COUNT(*) AS tot_votes FROM `$pollresult_table_name` WHERE pollID = ".$poll_id." AND answer IN (".implode(',', get_post_meta($poll_id, 'show_answers', true)).")";
            $res = $wpdb->get_results($query);
            $tot_poll_votes = $res[0]->tot_votes;
        }
        
        return $tot_poll_votes;
    }
    
    private function get_votes_perc($pollID, $a) {
        global $wpdb;
        global $pollresult_table_name;
        
        $tot_poll_votes = null;
        
        $query = "SELECT COUNT(*) AS votes FROM `$pollresult_table_name` WHERE pollID = ".$pollID." AND answer = '$a'";
        $res = $wpdb->get_results($query);
        $this_answer_votes = $res[0]->votes;
        
        if (is_array(get_post_meta($pollID, 'show_answers', true))) {
            $tot_poll_votes = self::get_votes_total($pollID);
        }
        
        if ($tot_poll_votes > 0)
            $perc = number_format(($this_answer_votes / $tot_poll_votes) * 100, 1);
        else
            $perc = 0;
            
        return $perc;
    }
    
    private function get_poll($id) {
        global $wpdb;
        global $poll_table_name;
        
        $query = "SELECT * FROM `$poll_table_name` WHERE ID = ".$id;
        
        $poll = $wpdb->get_results($query, ARRAY_A);
        $poll = $poll[0];
        
        return $poll;
    }
    
    private function get_real_ip_address() {
        //check ip from share internet
        if (! empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        }
        //to check ip is pass from proxy
        elseif (! empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }
    
    private function can_vote($poll_id) {
        return true;
    }
}
?>
