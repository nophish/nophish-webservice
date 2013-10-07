<?php
class phishingServer {
    const PHISH_URLS = 0;
	const VALID_URLS = 1;
	const PHISHTANK_URLS = 2;
	const PHISHTANK_KEY = "7623396d52d879bf484826bc99a3b3a814c746ff4c4a88102ea6111338821247";
	//60*60*12
	const MAX_CACHE_AGE = 43200;
	const CACHE_SIZE = 500;
	const URL_CACHE_FILE = "urlcache.serialized.php";
	
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
	
	private function updatePhishURLCache(){
	    if(!file_exists(self::URL_CACHE_FILE) || time() - filectime(self::URL_CACHE_FILE) > self::MAX_CACHE_AGE){
		    $urls = Array();
			$phishtank_url="http://data.phishtank.com/data/".self::PHISHTANK_KEY."/online-valid.csv";
			//$phishtank_url="http://data.phishtank.com/data/online-valid.csv";
			$all_info=file($phishtank_url);
			array_shift($all_info);
			foreach($all_info as $index => $info){
			  $parts = explode(",",$info);
			  $urls[]=$parts[1];
			  if($index >= self::CACHE_SIZE){
			    break;
			  }
			}
			file_put_contents(self::URL_CACHE_FILE,serialize($urls));
		}
	}
	
	private function getPhishURLs($count){
	  self::updatePhishURLCache();
	  $info = unserialize(file_get_contents(self::URL_CACHE_FILE));
	  return array_slice($info,0,$count);
	}
	
	private function getPhishTankURLs($count){
	  self::updatePhishURLCache();
	  $urls = unserialize(file_get_contents(self::URL_CACHE_FILE));
	  return array_slice($info,0,$count);
	}
	
	private function getValidURLs($count){
	  $urls = Array();
	  $urls[] = "http://www.google.de/merchants/merchantdashboard";
	  
	  $result = Array();
	  foreach($urls as $url){
		$object = new stdClass();
		$object->attackType="NoPhish";
		$object->siteType="AnyPhish";
		$object->points=Array(0,0);
		$object->correctparts=Array();
		$flags = PREG_SPLIT_DELIM_CAPTURE;
		$urlparts = explode("/",$url);
		$regex = '#(/)#';
		$flags = PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY;
		$object->parts = preg_split( $regex, $url, -1, $flags);
		$result[] = $object;
	  }
	  return $result;
	}
	
	public function getURLs($count, $type){
	  if($type == self::PHISH_URLS){
	    return self::getPhishURLs($count);
	  }else if($type == self::VALID_URLS){
	    return self::getValidURLs($count);
	  }if($type == self::PHISHTANK_URLS){
	    return self::getPhishTankURLs($count);
	  }else{
		throw new Exception('Invalid type parameter '.$type);
	  }
	}
	
	
	
}