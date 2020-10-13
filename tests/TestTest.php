<?php
use PHPUnit\Framework\TestCase;
use function PHPUnit\Framework\assertEquals;
require __DIR__ ."../../moss/ToTestGraph.php";
require __DIR__ ."/ToTest.php";

class TestTest extends PHPUnit_Framework_TestCase
{
    public function testGraphAllTrue(){
        $Graph = new ToTestGraph("Hi");
        $text = $Graph->generateGraph(30,40,50, true,true,true, "Name1",1,"Name2",2);
        $this-> assertEquals('Name1_1->Name2_2 [dir=none, label="40/50 "penwidth= 2.25"];', $text);
    }
    public function testGraphAllFalse(){
        $Graph = new ToTestGraph("Hi");
        $text = $Graph->generateGraph(30,40,50, false,false,false, "Name1",1,"Name2",2);
        $this-> assertEquals('1->2 [dir=none, label="40/50 ""];', $text);
    }
    public function testGraphBelowThresh(){
        $Graph = new ToTestGraph("Hi");
        $text = $Graph->generateGraph(30,20,10, true,true,true, "Name1",1,"Name2",2);
        $this-> assertEmpty( $text);
    }
    public function testGraphOnlyId(){
        $Graph = new ToTestGraph("Hi");
        $text = $Graph->generateGraph(30,40,50, true,false,true, "Name1",1,"Name2",2);
        $this-> assertEquals('1->2 [dir=none, label="40/50 "penwidth= 2.25"];', $text);
    }

}
