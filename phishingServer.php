<?php
class phishingServer {
    public function sendMail($from, $to, $usermessage){
		$nachricht = <<<EOF
		<html>
			<head>
				<title>Anti Phishing Education</title>
			</head>
			<body>
			  <p>Sehr geehrter Nutzer,</p>
			  <p>hiermit erhalten Sie eine Mail die Von jedem h&auml;tte versendet werden k&ouml;nnen.</p>
			  <p>Sie enth&auml;lt zum Beispiel einen Link <a href="phishedu://maillink/">http://www.google.com</a>.</p>
			  <p>Sowie ihren Personalisierten Text:</p>
			  <p>{$usermessage}</p>
			</body>
		</html>
EOF;
		// für HTML-E-Mails muss der 'Content-type'-Header gesetzt werden
		$header  = 'MIME-Version: 1.0' . "\r\n";
		$header .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

		// zusätzliche Header
		$header .= 'To: '.$to. "\r\n";
		$header .= 'From: '.$from. "\r\n";

		
		/*This is the direct send option
		require("./class.smtpSend.php");
		$smtp = new smtpSend($from);
		if(!$smtp->send($to, "Anti Phishing Education", $nachricht, $header)){
          return $smtp->getError();
        }
		//*/
		
		///*OtherOption with Mail() function
		mail($to, "Anti Phishing Education", $nachricht, $header);
		//*/
	  return true;
	}	
}