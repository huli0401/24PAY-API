<?php

/**
 * pay24
 *
 * @author Roman Huliak <roman@huliak.eu>
 * @category Payments
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @version 0.1
 * @final
 *
 * */
final class pay24
{

    /**
     * 24pay - MID
     *
     * @access private
     * @var type
     * @static
     */
    private static $mid = '24-PAY-MID';

    /**
     * 24pay - Eshop ID
     *
     * @access private
     * @var type
     * @static
     */
    private static $eshopId = '1111111';

    /**
     * 24pay - SIGN HEX KEY
     *
     * @access private
     * @var type
     * @static
     */
    private static $hexKey = '';

    /**
     * 24pay - KEY
     *
     * @access private
     * @var string
     * @static
     */
    private static $key = '123456790123456790123456790123456790123456790123456790123456790';

    /**
     * Production URL

      private static $url = 'https://admin.24-pay.eu/pay_gate/paygt';

     *
     */

    /**
     * 24pay GATE URL - TESTING
     *
     * @access private
     * @var string
     * @static
     */
    private static $url = 'https://admin.24-pay.eu/pay_gate/paygt';
    private static $data;

    /**
     * IV for sign
     *
     * @access private
     * @var string
     * @static
     */
    private static $iv = 'nTLhLOBSSBOLhLTn';
    private static $sign;

    /**
     * User return URL
     *
     * @access private
     * @var string
     * @static
     */
    private static $rurl = 'http://24p.loc/user-after-pay.php';

    /**
     * API response URL
     *
     * @access private
     * @var string
     * @static
     */
    private static $nurl = 'http://24pay-api.loc/gate-payment-info.php';

    private static $msg = '';

    /**
     * Other settings
     *
     * @access private
     * @static
     */
    private static $amount = '';
    private static $cac = 'EUR';
    private static $MsTxnId = '';
    private static $PspTxnId = '';
    private static $firstName = '';
    private static $familyName = '';
    private static $email = '';
    private static $clientid = '23';
    private static $country = 'SVK';
    private static $lang = 'SK';
    private static $timeStamp = '';
    private static $Result = '';
    private static $Target = '';
    private static $requestType = '';

    function __construct(array $data)
    {
        if (isset($data['rurl']))
        {
            self::$rurl = $data['rurl'];
        }

        if (isset($data['nurl']))
        {
            self::$nurl = $data['nurl'];
        }

        if (isset($data['cac']))
        {
            self::$cac = $data['cac'];
        }
        if (isset($data['clientid']))
        {
            self::$clientid = $data['clientid'];
        }

        if (isset($data['email']))
        {
            self::$email = $data['email'];
        }

        if (isset($data['amount']))
        {
            self::$amount = number_format($data['amount'], 2, '.', '');
        }

        if (isset($data['MsTxnId']))
        {
            self::$MsTxnId = $data['MsTxnId'];
        }

        if (isset($data['PspTxnId']))
        {
            self::$PspTxnId = $data['PspTxnId'];
        }

        if (isset($data['firstName']))
        {
            self::$firstName = $data['firstName'];
        }

        if (isset($data['familyName']))
        {
            self::$familyName = $data['familyName'];
        }

        if (isset($data['Result']))
        {
            self::$Result = $data['Result'];
        }

        if (isset($data['Target']))
        {
            self::$Target = $data['Target'];
        }

        if (isset($data['requestType']))
        {
            self::$requestType = $data['requestType'];
        }

        if (isset($data['timeStamp']))
        {
            self::$timeStamp = $data['timeStamp'];
        }
        else
        {
            self::$timeStamp = date('Y-m-d h:i:s');
        }

        self::get_message();
        self::getHexKey();
        self::get_sign();
    }

    /**
     * Return payment data
     *
     * @access public
     * @return array payment data
     */
    public function get_payment_data()
    {
        return array(
            'URL' => self::$url,
            'RURL' => self::$rurl,
            'NURL' => self::$nurl,
            'Amount' => self::$amount,
            'CurrAlphaCode' => self::$cac,
            'MsTxnId' => self::$MsTxnId,
            'FirstName' => self::$firstName,
            'FamilyName' => self::$familyName,
            'Email' => self::$email,
            'Country' => self::$country,
            'LangCode' => self::$lang,
            'ClientId' => self::$clientid,
            'Timestamp' => self::$timeStamp,
            'Sign' => self::$sign,
            'Mid' => self::$mid,
            'EshopId' => self::$eshopId
        );
    }

    /**
     * Create SIGN
     *
     * @access private
     * @return void
     * @static
     */
    private static function get_sign()
    {
        $pad = 8 - (strlen(self::$msg) % 8);
        self::$msg .= str_repeat(chr($pad), $pad);

        $result = openssl_encrypt(
            self::$msg, 'aes-256-cbc', self::$hexKey, OPENSSL_RAW_DATA, self::$iv
        );
        self::$sign = strtoupper(substr(bin2hex($result), 0, 32));
        unset($result);
    }

    /**
     * Create HEX key
     *
     * @access private
     * @return void
     * @static
     */
    private static function getHexKey()
    {
        $hKey = "";
        for ($i = 0; $i < strlen(self::$key); $i = $i + 2) {
            $hKey .= hex2bin(self::$key[$i] . self::$key[$i + 1]);
        }
        self::$hexKey = $hKey;
    }

    /**
     * Set message for 24pay gate
     *
     * @access private
     * @return void
     * @static
     */
     private function get_message()
     {
         switch (self::$requestType) {
             case 'payRequest':
                 self::$msg = sha1(self::$mid . self::$amount . self::$cac . self::$MsTxnId . self::$firstName . self::$familyName . self::$timeStamp, true);
                 break;
             case 'payNote':
                 self::$msg = sha1(self::$mid . self::$amount . self::$cac . self::$PspTxnId . self::$MsTxnId . self::$timeStamp . self::$Result, true);
                 break;
         }
     }

}
