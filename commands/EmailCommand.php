<?php

class EmailCommand extends Service {
    public function actionSend() {
     	$config = Setting::get('email');
		$mail  = new PHPMailer();
		if(@$config['enabled'] == 'YES'){
			$mails = $this->params['mails'];
			if (!is_array($mails)) {
				echo " \n";
				echo "######## ERROR RUNNING EMAIL SERVICE #########\n";
				echo "### You should run this from Email::send() ###\n";
				echo "##############################################\n";
				return;
			}	
			
			if ($config['transport'] == "smtp") {
				$mail->IsSMTP(); 
				$mail->SMTPDebug  = 3;
			}
			
			$mail->Host = $config['host']; 
			$mail->Port = $config['port'];                    
			
			if ($config['username'] != '') {
				$mail->SMTPAuth   = true;                  
				$mail->Username   = $config['username']; 
				
				if ($config['password']) {
					$mail->Password   = $config['password'];
				}
			}
			
			$mail->SetFrom($config['from']);
			$mail->SMTPOptions = array(
			    'ssl' => array(
			        'verify_peer' => false,
			        'verify_peer_name' => false,
			        'allow_self_signed' => true
			    )
			);
			
			if (is_array($mails)) {		    
				foreach ($mails as $m) {
					$mail->Subject = $m['subject'];
					$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!";
					$mail->MsgHTML($m['body']);
					$mail->IsHTML(true);
					$mail->AddAddress($m['to']);
					if (!$mail->Send()) {
						$this->log("Failed to send email to: " . $m['to']);
					}
					$mail->ClearAddresses();
				}
			} 
		} else {
			echo "Email is not enabled";
		}
    }
}
