<?php
class CcHelper extends MvcHelper 
{
    //check if valid Visa, MasterCard or American Express credit card
    function validate_cc_number($cc_number) 
    {
        $cc_number = preg_replace("[^0-9]", "", $cc_number);
        $false = false;
        $card_type = "";
        $card_regexes = array(
            "/^4\d{12}(\d\d\d){0,1}$/"     => "visa",
            "/^5[12345]\d{14}$/"           => "mastercard",
            "/^3[47]\d{13}$/"              => "amex",
        );
     
        foreach ($card_regexes as $regex => $type) 
        {
            if (preg_match($regex, $cc_number)) 
            {
                $card_type = $type;
                break;
            }
        }
     
        if(!$card_type) 
        {
            return $false;
        }

        return $card_type;
     
        /* mod 10 checksum algorithm */
        /*$revcode = strrev($cc_number);
        $checksum = 0;
     
        for ($i = 0; $i < strlen($revcode); $i++) 
        {
            $current_num = intval($revcode[$i]);
            if($i & 1) 
            {// Odd position 
                $current_num *= 2;
            }
            // Split digits and add. 
            $checksum += $current_num % 10;
            if($current_num > 9) 
            {
                $checksum += 1;
            }
        }
     
        return ($checksum % 10 == 0) ? $card_type : $false;*/
    }

}