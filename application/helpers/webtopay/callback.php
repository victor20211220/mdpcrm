<?php
 
require_once('WebToPay.php');
 
try {
	//$projectId = 0;
	$projectId = 1;
    $response = WebToPay::checkResponse($_GET, array(
        'projectid'     => $projectId,
        'sign_password' => 'd41d8cd98f00b204e9800998ecf8427e',
    ));
 
    if ($response['test'] !== '0') {
        throw new Exception('Testing, real payment was not made');
    }
    if ($response['type'] !== 'macro') {
        throw new Exception('Only macro payment callbacks are accepted');
    }
 
    $orderId = $response['orderid'];
    $amount = $response['amount'];
    $currency = $response['currency'];
    //@todo: patikrinti, ar užsakymas su $orderId dar nepatvirtintas (callback gali būti pakartotas kelis kartus)
    //@todo: patikrinti, ar užsakymo suma ir valiuta atitinka $amount ir $currency
    //@todo: patvirtinti užsakymą
	
	// @ Todo: check order with $ orderid not yet confirmed (callback can be repeated several times)
    // @ Todo: check whether the amount of the order and in line with the currency and $ AMOUNT $ currency
    // @ Todo: confirm the order
 
    echo 'OK';
} catch (Exception $e) {
    echo get_class($e) . ': ' . $e->getMessage();
}