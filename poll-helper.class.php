<?php 
class Merlic_Poll_Helper {

	public function notification() {
		if (!ini_get('SMTP'))
			ini_set('SMTP', 'localhost');
			
		if (!ini_get('smtp_port'))
			ini_set('smtp_port', 25);
			
		$body = '
			<p>'.POLL_LITE_NAME.' has been installed on a new blog (<a href="'.get_bloginfo('url').'">'.get_bloginfo('name').'</a>).</p>
			<p>
				<table border="0" cellpadding="3">
					<tr>
						<th align="left">Date:</th>
						<td align="left">'.self::format_date().'</td>
					</tr>
					<tr>
						<th align="left">Site name:</th>
						<td align="left">'.get_bloginfo('name').'</td>
					</tr>
					<tr>
						<th align="left">Site url:</th>
						<td align="left"><a href="'.get_bloginfo('url').'">'.get_bloginfo('url').'</a></td>
					</tr>
				</table>
			</p>
		';
		$sent = self::send_email(array(get_bloginfo('admin_email')=>get_bloginfo('name')), array('contact@wordpress.phpanswer.com'=>'PHP Answers'), POLL_LITE_NAME.' Registration', $body);
	}

	private function format_date( $date = null, $show_time = true ) {
		if (!isset($date)) $date = time();
		else
			$date = strtotime($date);
			
		if ($show_time == true) $formatted_date = date('D, M j Y H:m:s', $date);
		else
			$formatted_date = date('D, M j Y', $date);
			
		return $formatted_date;
	}
	
	private function send_email( $sender, $destinatary, $subject, $body ) {
		if (is_array($sender)) {
			$senderEmail = array_keys($sender);
			$senderEmail = $senderEmail[0];
			$senderName = array_values($sender);
			$senderName = $senderName[0];
		}
		else {
			$senderName = $sender;
			$senderEmail = $sender;
		}
		
		if (is_array($destinatary)) {
			$destEmail = array_keys($destinatary);
			$destEmail = $destEmail[0];
			$destName = array_values($destinatary);
			$destName = $destName[0];
		}
		else
			$destEmail = $destinatary;
			
		$headers = 'MIME-Version: 1.0'."\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1'."\r\n";
		$headers .= "From: $senderName <$senderEmail>\r\n";
		$headers .= "Reply-TO: $senderName <$senderEmail>\r\n";
		
		$body = '<html><body>'.$body.'<br /><br /><br /><br /><br /></body></html>';
		
		$r = wp_mail($destEmail, $subject, $body, $headers);
		
		return $r;
	}
	
}

?>
