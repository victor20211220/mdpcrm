<?php
//http://localhost/webtopay/redirect.php
 
require_once('WebToPay.php');
 
function get_self_url() {
    $s = substr(strtolower($_SERVER['SERVER_PROTOCOL']), 0,
                strpos($_SERVER['SERVER_PROTOCOL'], '/'));
 
    if (!empty($_SERVER["HTTPS"])) {
        $s .= ($_SERVER["HTTPS"] == "on") ? "s" : "";
    }
 
    $s .= '://'.$_SERVER['HTTP_HOST'];
 
    if (!empty($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] != '80') {
        $s .= ':'.$_SERVER['SERVER_PORT'];
    }
 
    $s .= dirname($_SERVER['SCRIPT_NAME']);
 
    return $s;
}
 
try {
    $self_url = get_self_url();
	
	//$projectId = 0;
	$projectId = 93171;
	
	//$test = 0;
	$test = 1;
	
	///$sign_password = 'd41d8cd98f00b204e9800998ecf8427e';
	$sign_password = '2a8a812400df8963b2e2ac0ed01b07b8';
	
	$requestUrl = WebToPay::getRequestUrl(array(
        'projectid'     => $projectId,
        'sign_password' => $sign_password,
        'orderid'       => 0,
        'amount'        => 1000,
        'currency'      => 'EUR',
        'country'       => 'LT',
        'accepturl'     => $self_url.'/accept.php',
        'cancelurl'     => $self_url.'/cancel.php',
        'callbackurl'   => $self_url.'/callback.php',
        'test'          => $test,
    ));
	
	echo '<a href="' . $requestUrl . '" >Pay</a>';
	/*exit;
 
    $request = WebToPay::redirectToPayment(array(
        'projectid'     => $projectId,
        'sign_password' => $sign_password,
        'orderid'       => 0,
        'amount'        => 1000,
        'currency'      => 'EUR',
        'country'       => 'LT',
        'accepturl'     => $self_url.'/accept.php',
        'cancelurl'     => $self_url.'/cancel.php',
        'callbackurl'   => $self_url.'/callback.php',
        'test'          => $test,
    ));
	*/
} catch (WebToPayException $e) {
    // handle exception
} 