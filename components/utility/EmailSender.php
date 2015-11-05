<?php
	/**
	* 
	*/
	class Emailsender extends CComponent
	{
		public static function send($builder){
			extract($builder);
			extract($params);
			/*$temp = Yii::getPathOfAlias("application.views.layouts.email.".$template).'.php';
			
			ob_start();
			include $temp;
			$body = ob_get_clean();*/

			$mail             = new PHPMailer();
			
			$mail->IsSMTP(); // telling the class to use SMTP
			$mail->Host       = "mail.pendapatkita.com"; // SMTP server
			$mail->SMTPDebug  = 0;                     // enables SMTP debug information (for testing)
			// 1 = errors and messages
			// 2 = messages only
			$mail->SMTPAuth   = true;                  // enable SMTP authentication
			$mail->Host       = "mail.pendapatkita.com"; // sets the SMTP server
			$mail->Port       = 587;                    // set the SMTP port for the GMAIL server
			$mail->Username   = "contact@pendapatkita.com"; // SMTP account username
			$mail->Password   = "xlzSlGm38";        // SMTP account password
			$mail->SetFrom("contact@pendapatkita.com", "Pendapatkita");
			$mail->AddReplyTo("contact@pendapatkita.com","Admin Pendapatkita.com");
			$mail->Subject = $subject;
			$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test
		
			$mail->MsgHTML(EmailBuilder::render($template,$params));
			$mail->IsHTML(true);
			$mail->AddAddress($to);
			if(!$mail->Send()) {
				//echo "Mailer Error: " . $mail->ErrorInfo;
				return false;
			} else {
				return true;
				//echo "Message sent!";
			}
		}
	}
?>