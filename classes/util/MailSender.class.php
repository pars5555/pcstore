<?php
require_once (CLASSES_PATH . "/util/FAZSmarty.class.php");
require_once (CLASSES_PATH . "/managers/LanguageManager.class.php");

class MailSender {

	private $config;

	public function __construct($config = null) {
		$this->config = $config;

	}

	public function send($from, $recipients, $subject, $template, $params = array(), $separate = false) {

		//--proccessing the message
		$smarty = new FAZSmarty();

		$lm = LanguageManager::getInstance(null, null);
		$params["all_phrases"] = $lm->getAllPhrases();
		$ul = $_COOKIE['ul'];
		if (!($ul === 'en' || $ul === 'ru' || $ul === 'am')) {
			$ul = 'en';
		}
		$params["ul"] = $ul;
		$smarty->assign("ns", $params);
		$message = $smarty->fetch($template);

		// To send HTML mail, the Content-type header must be set
		$headers = "";
		//'MIME-Version: 1.0' . "\r\n";

		$headers .= "Reply-To: $from\r\n";
		$headers .= "Return-Path: $from\r\n";
		$headers .= "X-Priority: normal\r\n";
		$headers .= 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
		$headers .= "X-Priority: 3\r\n";
		$headers .= "X-Mailer: PHP" . phpversion() . "\r\n";
		//$headers .= 'Content-type: text/plain; charset=utf-8' . "\n";

		// Additional headers
		//			$headers .= 'To: Mary <mary@example.com>, Kelly <kelly@example.com>' . "\r\n";
		$headers .= "From: $from\r\n";
		//		$headers .= 'From: Birthday Reminder <birthday@example.com>' . "\r\n";
		//		$headers .= 'Cc: birthdayarchive@example.com' . "\r\n";
		//		$headers .= 'Bcc: birthdaycheck@example.com' . "\r\n";

		// multiple recipients
		if ($separate) {
			foreach ($recipients as $recipient) {
				if (!empty($recipient)) {
					mail($recipient, $subject, $message, $headers);
				}
			}
		} else {
			$to = "";
			foreach ($recipients as $recipient) {
				if (!empty($recipient)) {
					$to .= $recipient . ',';
				}
			}
			$to = substr($to, 0, -1);
			mail($to, $subject, $message, $headers);

		}

	}
	
	public function sendHtml($from, $recipients, $subject, $message, $separate = false) {

		
		// To send HTML mail, the Content-type header must be set
		$headers = "";
		//'MIME-Version: 1.0' . "\r\n";

		$headers .= "Reply-To: $from\r\n";
		$headers .= "Return-Path: $from\r\n";
		$headers .= "X-Priority: normal\r\n";
		$headers .= 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
		$headers .= "X-Priority: 3\r\n";
		$headers .= "X-Mailer: PHP" . phpversion() . "\r\n";
		//$headers .= 'Content-type: text/plain; charset=utf-8' . "\n";

		// Additional headers
		//			$headers .= 'To: Mary <mary@example.com>, Kelly <kelly@example.com>' . "\r\n";
		$headers .= "From: $from\r\n";
		//		$headers .= 'From: Birthday Reminder <birthday@example.com>' . "\r\n";
		//		$headers .= 'Cc: birthdayarchive@example.com' . "\r\n";
		//		$headers .= 'Bcc: birthdaycheck@example.com' . "\r\n";

		// multiple recipients
		if ($separate) {
			foreach ($recipients as $recipient) {
				if (!empty($recipient)) {
					mail($recipient, $subject, $message, $headers);
				}
			}
		} else {
			$to = "";
			foreach ($recipients as $recipient) {
				if (!empty($recipient)) {
					$to .= $recipient . ',';
				}
			}
			$to = substr($to, 0, -1);				
			return mail($to, $subject, $message, $headers);

		}

	}

}
?>