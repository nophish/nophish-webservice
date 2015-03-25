<?php
class phishingServer {
    protected function _getMxHost($to){
    }

	public function sendMail($from, $to, $usermessage){
		//create a boundary for the email. This 
		$boundary = uniqid('np');
		$html_nachricht = <<<EOF
<html>
<head>
<title>Anti Phishing Education</title>
</head>
<body>
<p>Dies ist eine automatisch generierte E-Mail im Rahmen einer Anti-Phishing Education App. Falls diese nicht angefordert wurde, bitte ignorieren.</p>
<p>Ansonsten geht es hier weiter:</p>
<p>Wie du im Absender siehst, hast du dir gerade selbst eine E-Mail mit gefälschtem Absender geschickt. Hier ist außerdem dein Freitext:</p>
<p>{$usermessage}</p>
<p>Für einen Angreifer ist es ebenso einfach automatisierte E-Mails mit gefälschtem Absender und Inhalt zu verschicken. Meist enthalten diese einen Link zu einer Webseite, genau wie diese E-Mail.</p>
<p>Um mit der App fortzufahren, klicke auf den folgenden Link.</p>
<p><a href="https://goog1e.at">https://goog1e.at</a></p>
<p>Viele Grüße,</p>
<p>Dein NoPhish Team</p>
</body>
</html>
EOF;
		$plaintext_nachricht = <<<EOF
Anti Phishing Education

Dies ist eine automatisch generierte E-Mail im Rahmen einer Anti-Phishing Education App. Falls diese nicht angefordert wurde, bitte ignorieren.
Ansonsten geht es hier weiter:
Wie du im Absender siehst, hast du dir gerade selbst eine E-Mail mit gefälschtem Absender geschickt. Hier ist außerdem dein Freitext:
{$usermessage}
Für einen Angreifer ist es ebenso einfach automatisierte E-Mails mit gefälschtem Absender und Inhalt zu verschicken. Meist enthalten diese einen Link zu einer Webseite, genau wie diese E-Mail.
Um mit der App fortzufahren, klicke auf den folgenden Link.

https://goog1e.at

Viele Grüße,
Dein NoPhish Team
EOF;

require 'PHPMailer/PHPMailerAutoload.php';

$mail = new PHPMailer;

$mail->isDirect();
$mail->setLanguage('de');

$mail->setFrom($from); 
$mail->addAddress($to);
$mail->isHTML(true); 
$mail->Subject = 'Anti-Phishing Education';
$mail->CharSet = 'utf-8';
$mail->Body = $html_nachricht;
$mail->AltBody = $plaintext_nachricht;

if(!$mail->send()) {
    return 'Fehler: ' . $mail->ErrorInfo;
}

return "SUCCESS";
/*		// für HTML-E-Mails muss der 'Content-type'-Header gesetzt werden
		$header  = 'MIME-Version: 1.0' . "\r\n";
                $header .= "Content-Type: multipart/alternative;boundary=" . $boundary . "\r\n";

		// zusätzliche Header
		$header .= 'From: '.$from. "\r\n";
                $message_id = time() .'-' . md5($from . $to) . '@no-phish.de';
		$header .= 'Message-Id: <'.$message_id. ">";

                $nachricht = "\n\r\n--" . $boundary . "\r\n";
                $nachricht .= "Content-type: text/plain;charset=utf-8\r\n\r\n";
                $nachricht .= $plaintext_nachricht;
                $nachricht .= "\r\n\r\n--" . $boundary . "\r\n";
                $nachricht .= "Content-type: text/html;charset=utf-8\r\n\r\n";
                $nachricht .= $html_nachricht;
                $nachricht .= "\r\n\r\n--" . $boundary . "--";

		//*This is the direct send option
		require("./class.smtpSend.php");
		$smtp = new smtpSend($from);
		if(!$smtp->send($to, "Anti-Phishing Education SMTP", $nachricht, $header)){
			throw new  Exception($smtp->getError());
			//return false;
		}
		/*/

		/*OtherOption with Mail() function
		  mail($to, "Anti-Phishing Education MAIL", $nachricht, $header);
		//*/
	}	
}
