<?php
date_default_timezone_set('America/Los_Angeles');

include_once './libs/24pay.class.php';

// ID USER IN APP
$idUser = 1;
$price = 15.55;
$debug = true;

$pData = array(
    'email' => 'roman@huliak.eu',
    'amount' => $price,
    'MsTxnId' => mt_rand(0, 99999999),
    'requestType' => 'payRequest',
    'clientid' => 'roman@huliak.eu',
    'firstName' => 'Roman',
    'familyName' => 'Huliak'
);

$p24 = new pay24($pData);
$pubData = $p24->get_payment_data();
?>

<a href="https://github.com/roman-huliak/24PAY-API" target="_blank">https://github.com/roman-huliak/24PAY-API</a>
<br />
<a href="https://www.24-pay.sk/" target="_blank">https://www.24-pay.sk/</a>

<form id="payS" method="post" action="<?php echo $pubData['URL'] ?>">
    <?php
    unset($pubData['URL']);
    foreach ($pubData as $name => $value):
        ?>
        <?php echo $name ?><input size="100" type="text" readonly="readonly" name="<?php echo $name ?>" value="<?php echo $value ?>" /> <br />
    <?php endforeach; ?>

        <?php if($debug): ?>
        Debug<input type="text" readonly="readonly" name="Debug" value="true" /> <br />
        <?php endif; ?>
    <input type="submit" name="pay" value="Pay">
</form>