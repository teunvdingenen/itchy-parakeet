<?php

require_once dirname(__FILE__) . "/src/Mollie/API/Autoloader.php";

/*
 * Initialize the Mollie API library with your API key.
 *
 * See: https://www.mollie.com/beheer/account/profielen/
 */

$mollie = new Mollie_API_Client;
//$mollie->setApiKey("live_g62MJJGnR8Px5tPnkqCNmSGucywcDg");
$mollie->setApiKey("test_GBTjmHjfpkxbrzEaVnVgFuCenmRFvw");

