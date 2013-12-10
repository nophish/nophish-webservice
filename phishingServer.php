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
			<p>Wie du im Absender siehst, hast du dir gerade selbst eine E-Mail mit gefälschtem Absender geschickt. Hier ist außerdem dein privater Text: {$usermessage}</p>
			<p>Für einen Angreifer ist es ebenso einfach automatisierte E-Mails mit gefälschtem Absender und Inhalt zu verschicken. Daher ist weder der Absender noch der Inhalt einer E-Mail eine vertrauensvolle Information.</p>
			<p>Diese E-Mail endet mit einem Link, auf den du klicken kannst. Der Link enthält die Webadresse von Google. Es sieht so aus, als würdest du durch Klicken des Links auf die Google Webseite gelangen. Dies ist nicht der Fall. Stattdessen gelangst du zurück in die Anti-Phishing Education App.</p>
			<p><a href="http://pages.no-phish.de/maillink.php">http://www.google.com</a></p>
			<p>Zum Fortfahren mit der App, einfach auf den Google-Link klicken.</p>
			<p>Viele Grüße,</p>
			<p>Deine Anti-Phishing Education App</p>
			</body>
		</html>
EOF;
		// für HTML-E-Mails muss der 'Content-type'-Header gesetzt werden
		$header  = 'MIME-Version: 1.0' . "\r\n";
		$header .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

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
