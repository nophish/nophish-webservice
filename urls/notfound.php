<?php
    if($_SERVER['REQUEST_URI'] != "/urls/AnyPhish.json"){
	  header('HTTP/1.0 404 Not Found');
?>
This file could not be found: <?= $_SERVER['REQUEST_URI'] ?>
<?php
	  exit;
	}
?>

<?php
    const PHISHTANK_KEY = "7623396d52d879bf484826bc99a3b3a814c746ff4c4a88102ea6111338821247";
	//60*60*12
	const MAX_CACHE_AGE = 43200;
	const CACHE_SIZE = 100;
	const URL_CACHE_FILE = "AnyPhish.json.cache";
	
	function updatePhishURLCache(){
	    if(!file_exists(URL_CACHE_FILE) || time() - filectime(URL_CACHE_FILE) > MAX_CACHE_AGE){
		    $result = Array();
			$phishtank_url="http://data.phishtank.com/data/".PHISHTANK_KEY."/online-valid.csv";
			//$phishtank_url="http://data.phishtank.com/data/online-valid.csv";
			$all_info=file($phishtank_url);
			array_shift($all_info);
			foreach($all_info as $index => $info){
			  $parts = explode(",",$info);
			  $url=$parts[1];
			  
			  $object = new stdClass();
			  $object->attackType="AnyPhish";
			  $object->siteType="AnyPhish";
			  $object->points=Array(0,0);
			  $flags = PREG_SPLIT_DELIM_CAPTURE;
			  $regex = '#(/)#';
			  $flags = PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY;
			  $object->parts = preg_split( $regex, $url, -1, $flags);
			  
			  $result[] = $object;
			  
			  if($index >= CACHE_SIZE){
			    break;
			  }
			}
			file_put_contents(URL_CACHE_FILE,json_encode($result));
		}
	}
	
	updatePhishURLCache();
	
	header('content-type: text/javascript');
	header('Content-Length: ' . filesize(URL_CACHE_FILE));
	ob_clean();
    flush();
    readfile(URL_CACHE_FILE);