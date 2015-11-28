<?php

include_once './libs/24pay.class.php';

function getPaymentArray($data)
{
    $payData = array(
        'cac' => $data['Transaction']['Presentation']['Currency'],
        'amount' => $data['Transaction']['Presentation']['Amount'],
        'MsTxnId' => $data['Transaction']['Identification']['MsTxnId'],
        'PspTxnId' => $data['Transaction']['Identification']['PspTxnId'],
        'timeStamp' => $data['Transaction']['Processing']['Timestamp'],
        'Result' => $data['Transaction']['Processing']['Result'],
        'requestType' => 'payNote'
    );
    $pay = new pay24($payData);
    $d = $pay->get_payment_data();

    return $d['Sign'];
}

$string = str_replace('<?xml version"1.0" encoding="UTF-8"?>', '', $_REQUEST['params']);
$xml = new DOMDocument();
$xml->loadXML($string);
$xml = simplexml_load_string($xml->saveXML());
$params = json_decode(json_encode($xml), true);
$sign = getPaymentArray($params);

if ($sign == $params['@attributes']['sign'])
{
    switch ($params['Transaction']['Processing']['Result'])
    {
        case 'OK':
            // ALL IS OK
            // UPDATE STATUS IN DATABASE
            break;
        case 'PENDING':
            // OK BUT PENDING
            // UPDATE STATUS IN DATABASE
            // SELECTED BANK TRANSFER OR BANK STILL NOT CONFIRMED TRANSFER, WAIT FEW SECONDS
            break;
        default:
        case 'FAIL':
            /**
             * JUST FAIL
             * UPDATE STATUS IN DATABASE
             *
             * NO MONEY
             * SMALL LIMIT ON CARD
             * WRONG CARD DATA
             * NOT PAID BAND TRANSFER (AFTER 3-4 DAYS)
             * 24PAY ERROR/MAINTENANCE
             *
             */
            break;
    }
}
?>