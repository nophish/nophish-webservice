<?php
require_once 'jsonRPCServer.php';
require 'phishingServer.php';

$myPhishingServer = new phishingServer();
jsonRPCServer::handle($myPhishingServer)
    or print 'no request';