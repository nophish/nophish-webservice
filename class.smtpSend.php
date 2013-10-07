<?php

/**
 * Direct to SMTP Delivery script
 * Downloaded 2013.09.29 from http://www.php-trivandrum.org/code-snippets/php-smtp-email-direct-to-mail-box-delivery/
 */

class smtpSend
{
    private $fromEmail;
    private $error;
    private $debug; // 1 - 9
    
    public function __construct($fromEmail){
        $this->fromEmail = $fromEmail;
        $this->error = false;
        $this->debug = 1;
    }
    
    public function getError(){
        return $this->error;
    }
    
    protected function _die($msg, $line, $file){
        $this->error = trim($msg). " @ $file:$line ";
    }
    
    protected function _sockPut(&$socket, $msg, $level = 1){
        if($this->debug >= $level) 
            echo $msg;
        fputs($socket, $msg);
    }

    protected function _getMxHosts($to){
        $response = array('mx' => array(), 'weight' => array());
        list($user, $host) = explode('@', $to);
        if(getmxrr ( $host, $response['mx'], $response['weight'] )){
            return $response;
        }
        return false;
    }

    protected function _parse($socket, $response, $line = __LINE__, $level = 1) 
    { 
	    while (@substr($server_response, 3, 1) != ' ') 
	    {
		    if (!($server_response = fgets($socket, 256))) 
		    { 
			    $this->_die("Couldn't get mail server response codes", $line, __FILE__); 
		    } 
            if($this->debug >= $level) 
                echo $server_response;
	    } 

	    if (!(substr($server_response, 0, 3) == $response)) 
	    { 
		    $this->_die("Ran into problems sending Mail. Response: $server_response", $line, __FILE__); 
	    }else{
	        return substr($server_response, 4);
	    } 
    }

    public function send($to, $subject, $message, $headers = '')
    {

	    $message = preg_replace("#(?<!\r)\n#si", "\r\n", $message);

	    if (trim($subject) == '')
	    {
		    $this->_die("No email Subject specified", __LINE__, __FILE__);
	    }

	    if (trim($message) == '')
	    {
		    $this->_die("Email message was blank", __LINE__, __FILE__);
	    }

	    $mxhosts = $this->_getMxHosts($to);
	    if(!$mxhosts){
	        $this->_die("No MX records could be identified for email $to", __LINE__, __FILE__);
	    }
	    $socket = false;
	    
        foreach($mxhosts['mx'] as $smtp_host){
	        if($socket = fsockopen($smtp_host, 25, $errno, $errstr, 20) )
	        {
		        break;
	        }
	    }
	    if(!$socket){
	        $this->_die("Could not connect to smtp host : $errno : $errstr", __LINE__, __FILE__);
	    }

	    if($this->error !== false)
	        return false;
	    // Wait for reply
	    $mxReady = $this->_parse($socket, "220", __LINE__, 5);
	    $mxHost = (!empty($mxReady))?substr($mxReady, 0, strpos($mxReady, ' ')):$mxhosts[0];

	    $this->_sockPut($socket, "HELO " . $mxHost . "\r\n", 1);
	    $this->_parse($socket, "250", __LINE__, 5);
	    
	    if($this->error !== false){
	        fclose($socket);
	        return false;
	    }    
	        
	    // Specify who the mail is from....
	    $this->_sockPut($socket, "MAIL FROM: <" . $this->fromEmail . ">\r\n", 2);
	    $this->_parse($socket, "250", __LINE__, 5);

	    if($this->error !== false){
	        fclose($socket);
	        return false;
	    }    
	    $this->_sockPut($socket, "RCPT TO: <" . $to . ">\r\n", 2);
	    $this->_parse($socket, "250", __LINE__, 5);

	    if($this->error !== false){
	        fclose($socket);
	        return false;
	    }    
	    // Ok now we tell the server we are ready to start sending data
	    $this->_sockPut($socket, "DATA\r\n", 5);

	    // This is the last response code we look for until the end of the message.
	    $this->_parse($socket, "354", __LINE__, 5);

	    if($this->error !== false){
	        fclose($socket);
	        return false;
	    }    
	    // Send the Subject Line...
	    $this->_sockPut($socket, "Subject: $subject\r\n", 8);

        if(!empty($headers)){
	       // Now any custom headers....
	       $this->_sockPut($socket, "$headers\r\n", 8);
	    }
	    
	    $this->_sockPut($socket, "\r\n", 8);

	    // Ok now we are ready for the message...
	    $this->_sockPut($socket, "$message\r\n", 9);

	    // Ok the all the ingredients are mixed in let's cook this puppy...
	    $this->_sockPut($socket, ".\r\n", 9);

        $this->_parse($socket, "250", __LINE__, 9);

	    if($this->error !== false){
	        fclose($socket);
	        return false;
	    }    
	    // Now tell the server we are done and close the socket...
	    $this->_sockPut($socket, "QUIT\r\n", 8);
	    fclose($socket);

	    return TRUE;
    }
}
?>
