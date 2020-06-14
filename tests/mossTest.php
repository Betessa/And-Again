<?php
use PHPUnit\Framework\TestCase;
use function PHPUnit\Framework\assertEquals;
require __DIR__ ."../../moss/moss.php";
require __DIR__ ."/ToTest.php";

    class mossTest extends PHPUnit_Framework_TestCase
    {

        public function testSetLanguage(){
            $moss = new MOSS(370143826);
            $value = $moss->setLanguage("java");
            $this-> assertEquals(True, $value);
        }

        public function testSetLanguageWrong(){
                $this->setExpectedException("Exception",[],1);
                $moss = new MOSS(370143826);
                $value = $moss->setLanguage("This is not a programming languase");

        }

        public function testGetAllowedLanguages(){
            $moss = new MOSS(370143826);
            $array = $moss ->getAllowedLanguages();
            $this->assertCount(25, $array);
        }

        public function testSetDirectorymMode(){
            $moss = new MOSS(370143826);
            $value = $moss->setDirectoryMode(true);
            $this->assertTrue($value);
        }
        public function testSetDirectorymModeWrong(){
                $this->setExpectedException("Exception",["DirectoryMode must be a boolean"], 2);
                $moss = new MOSS(370143826);
                $value = $moss->setDirectoryMode("Hi");
        }

        public function testSetIgnoreLimit(){
            $moss =  new MOSS(370143826);
            $value = $moss->setIgnoreLimit(20);
            $this->assertTrue($value);
        }
        public function testSetIgnoreLimitWrong1(){
            $this->setExpectedException("Exception", ["The limit needs to be greater than 1"], 4);
            $moss = new MOSS(370143826);
            $value = $moss->setIgnoreLimit(1);
        }
        public function testSetIgnoreLimitWrongNotInt(){
                $this->setExpectedException("Exception", ["The limit needs to be greater than 1"], 4);
                $moss = new MOSS(370143826);
                $value = $moss->setIgnoreLimit("This is not an int");

        }
        public function testAddBaseFile(){
                $this->setExpectedException("Exception",[],3);
                $moss = new MOSS(370143826);
                $value = $moss->addBaseFile("This is not a file");
        }

        public function testSetResultLimit()
        {
            $moss = new MOSS(370143826);
            $value = $moss->setResultLimit(20);
            $this->assertTrue($value);
        }
        public function testSetResultLimitWrong1(){
            $this->setExpectedException("Exception", [], 5);
            $moss = new MOSS(370143826);
            $value = $moss->setResultLimit(1);
        }
        public function testSetResultLimitWrongNotInt()
        {
            $this->setExpectedException("Exception", [], 5);
            $moss = new MOSS(370143826);
            $value = $moss->setResultLimit("This is not an int");
        }

        public function testAddFileWrong(){
            $this->setExpectedException("Exception",[],7);
            $moss = new MOSS(370143826);
            $value = $moss->addFile("This is not a file");
        }

        public function testSetComment(){
            $moss = new MOSS(370143826);
            $value = $moss->setCommentString("Hello");
            $this->assertTrue($value);
        }

        public function testExperimentalServer(){
            $moss = new MOSS(370143826);
            $value = $moss->setExperimentalServer(True);
            $this->assertTrue($value);
        }
        public function testExperimentalServerWrong(){
            $this->setExpectedException("Exception",[],6);
            $moss = new MOSS(370143826);
            $value= $moss->setExperimentalServer("This isn't a boolean");
        }
        public function testSend(){
            $moss = new MOSS(370143826);
            $read= $moss->send();
            $this->assertNotEmpty($read);
        }
        public function testAddFile(){
            $moss = new MOSS(370143826);
            $value = $moss->addFile("ToTest.php");
            $this->assertTrue($value);
        }
        public function testAddBase(){
            $moss = new MOSS(370143826);
            $value = $moss->addBaseFile("ToTest.php");
            $this->assertTrue($value);
        }
    }
?>  
