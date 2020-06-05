<?php
    require __DIR__ .'bashScripts/Send.php';

    class test1 extends PHPUnit_Framework_TestCase
    {
        public function testSend()
        {
            if (strcmp(substr(Send::SendtoMoss(0), 1, 4), "http")) {

                $result = true;
            } else {
                $result = false;
            }
        }
    }
?>  
