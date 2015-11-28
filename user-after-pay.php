<?php

$error = false;
if (!isset($_GET['MsTxnId']))
{
    $_GET['MsTxnId'] = 0;
    $error = true;
}
// GET TRANSACTION FROM DATABASE by $_GET['MsTxnId']

$transaction = array(
    'status' => 1
);
if ($transaction)
{
    // SHOW STATUS FOR USER


    // IF PENDING YOU CAN RELOAD PAGE AFTER 5 sec.
}
else
{
    $error = true;
}
