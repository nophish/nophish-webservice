<?php
class phishingServer {
    public function sendMail($from, $to, $usermessage){
		$nachricht = <<<EOF
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
			<p><a href="http://pages.no-phish.de/maillink.php">http://www.google.com</a></p>
			<p>Viele Grüße,</p>
			<p>Dein NoPhish Team</p>
			</body>
		</html>
EOF;
		// für HTML-E-Mails muss der 'Content-type'-Header gesetzt werden
		$header  = 'MIME-Version: 1.0' . "\r\n";
		$header .= 'Content-type: text/html; charset=utf-8' . "\r\n";

		// zusätzliche Header
		$header .= 'From: '.$from. "\r\n";
		
		/*This is the direct send option
		require("./class.smtpSend.php");
		$smtp = new smtpSend($from);
		if(!$smtp->send($to, "Anti Phishing Education", $nachricht, $header)){
          return $smtp->getError();
        }
		//*/
		
		///*OtherOption with Mail() function
		mail($to, "Anti-Phishing Education", $nachricht, $header);
		//*/
	  return true;
	}	
}
