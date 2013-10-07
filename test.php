<?php
require_once 'jsonRPCClient.php';
$myExample = new jsonRPCClient('https://api.no-phish.de/api.php');
if(isset($_GET["function"]) && $_GET["function"] == "sendmail"){
  print "<pre>";
  print_r($myExample->sendMail('cbergmann@schuhklassert.de','cbergmann@schuhklassert.de','I had a Dream.'));
  print "</pre>";
}else if(isset($_GET["function"]) && $_GET["function"] == "getURLs"){
  print "<pre>";
  print("phish urls:\n");
  print_r(json_encode($myExample->getURLs(100,0)));
  print("valid urls:\n");
  print_r(json_encode($myExample->getURLs(100,1)));
  print "</pre>";
}else{
?>
  Currently we support the following functions:</br>
  <ul>
  <li><a href="/test.php?function=sendmail">sendmail</a></li>
  <li><a href="/test.php?function=getURLs">getURLs</a></li>
  </ul>
<?php
phpinfo();
}
?>
