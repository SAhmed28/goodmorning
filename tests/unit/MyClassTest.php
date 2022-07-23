<?php
// namespace Tests\Unit;
// use App\MyClass;
use PHPUnit\Framework\TestCase;
Class MyClassTest extends TestCase{
    public function testMean(){
        $myClass = new App\MyClass;
        $uri1 = array(
            0=> array(
                'uriName' => "uri1",
                'value' => 15
            ),
        );
        $myClass->processStart($uri1[0]['uriName'],$uri1[0]['value']);
        $mean = $myClass->getIndividualMean();

        $result = array(
            "uri1"=> 15
        );

        $this->assertEquals(15,$mean);
    }

}


?>