<?php
use PHPUnit\Framework\TestCase;
use function PHPUnit\Framework\assertEquals;
require __DIR__ .'/../bashScripts/Send.php';

    class test1Test extends PHPUnit_Framework_TestCase
    {
        public function testBruh() {
            $this -> assertEquals(True, True);
        }
        public function testSend()
        {

            if (strcmp(substr(SendtoMoss(0), 0, 4), "http")) {

                $result = true;
            } else {
                $result = false;
            }
            $this -> assertEquals(True, $result);
        }

    }
?>  
