<?php
    require __DIR__ .'/../vendor/autoload.php';

    class Test extends PHPUnit_Framework_TestCase
    {
        public function testSend()
        {
            if(strcmp(substr(Send::SendtoMoss(0), 1, 4), "http")){
                
            $result = true;
            }
        }
    }
?>  
