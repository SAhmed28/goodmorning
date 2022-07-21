<?php

include "request.php";

class MyClass extends Request{
    public $uri;
    public $responseTime;
    public $startTime;
    public $end;
    public $counter=0;
    public $uriList = array(
        0=> array(
            'uriName' => 'uri1',
            'value' => 10
        ),
    );
    // public function __construct(){

    // }

    protected function start(string $uri ): void{
       $this->startTime = microtime(true);
    }
    protected function finish (): void{
        $this->end = microtime(true);
    }


    //Since we don't have any specific uri, let's generate random uri. Here I am using a range of uri1 to uri4.
    function generateUri(){
        return "uri".rand(1,4);
    }

    //make a count of process for updating array
    function getResponseData($uri){
        //calling process
        $this->process($uri);
        

        $this->counter = $this->counter+1;  //counting process

        $this->responseTime = round($this->end-$this->startTime,3)*1000;   //1 sec = 1000 miliseconds

        // echo $this->counter;
        // echo "Length of Array".count($this->uriList);
        // echo "Counter: ".$this->counter-1;
        echo "<br>";

        // if we have less data (uriList) than no. of process calling, insert new data
        if (count($this->uriList) <= $this->counter-1){
            $this->uriList[count($this->uriList)] = array(
                'uriName' => $this->generateUri(),
                'value' => $this->responseTime
            );

            // $this->uriList[count($this->uriList)]['uriName'] = $uri;
            // $this->uriList[count($this->uriList)]['value'] = $this->responseTime;


            //Checking new data insertion
            // echo "<br>";
            // echo "New URI for position-".($this->counter-1).": ".$this->uriList[count($this->uriList)-1]['uriName'];
            // echo "<br>";
            // echo "New Value: ".$this->uriList[count($this->uriList)-1]['value'];

        }

        // Cheking all data and new data insertion in $uriList array
        for ($i=0; $i<count($this->uriList); $i++){
            echo "<br>";
            echo "URI position-".$i.", uriName: ".$this->uriList[$i]['uriName'];
            echo "<br>";
            echo "New Value: ".$this->uriList[$i]['value'];
        }
    }

    // Stores only response time values in an array. Gives Support to getAllMean() and getAllStd() function
    function getOnlyValues(){
        for ($i=0; $i<count($this->uriList); $i++){
            $valuesArray[$i] = $this->uriList[$i]['value'];     //All values stored in this array
        }
        return $valuesArray;
    }

    function getOnlyNames(){
        for ($i=0; $i<count($this->uriList); $i++){
            $namesArray[$i] = $this->uriList[$i]['uriName'];     //All values stored in this array
        }
        return $namesArray;
    }

    function getAllMean(){
        $values = $this->getOnlyValues();
        $meanAll = array_sum($values) / count($values);
        echo "All value Mean: ".$meanAll;
    }

    function getIndividualMean(){
        $meanIndividual = [];
        $allNames = $this->getOnlyNames();
        $allValues = $this->getOnlyValues();

        for($i=0; $i<count($allNames); $i++){
            if(array_key_exists($allNames[$i],$meanIndividual)){
                $temp = $meanIndividual[$allNames[$i]];
                array_push($temp, $allValues[$i]);
                $meanIndividual[$allNames[$i]] = $temp;
            }
            else{
                $meanIndividual[$allNames[$i]] = array($allValues[$i]);
            }
        }
        foreach($meanIndividual as $key => $value){
            $meanIndividual[$key] = array_sum($value)/count($value);
            echo $key." ".$meanIndividual[$key]."<br>";
        }

    }

    // total std formula
    function Std($arr)
    {
        $variance = 0.0;
                // calculating mean using array_sum() method
        $average = array_sum($arr)/count($arr);
          
        foreach($arr as $i)
        {
            // sum of squares of differences between 
                        // all numbers and means.
            $variance += pow(($i - $average), 2);
        } 
        return (float)sqrt($variance/count($arr));
    }

    function getAllStd(){
        $values = $this->getOnlyValues();
        return $this->Std($values);
    }



    // STD
    function getIndividualStd(){
        $meanIndividual = [];
        $allNames = $this->getOnlyNames();
        $allValues = $this->getOnlyValues();

        for($i=0; $i<count($allNames); $i++){
            if(array_key_exists($allNames[$i],$meanIndividual)){
                $temp = $meanIndividual[$allNames[$i]];
                array_push($temp, $allValues[$i]);
                $meanIndividual[$allNames[$i]] = $temp;
            }
            else{
                $meanIndividual[$allNames[$i]] = array($allValues[$i]);
            }
        }

        foreach($meanIndividual as $key => $value){
            $meanIndividual[$key] = $this->Std($value);
            echo $key." ".$meanIndividual[$key]."<br>";
        }
    }

    function getBinWidth($arr){
        $min =  min($arr);
        $max = max($arr);
        $diiference = $max - $min;
        $mean = array_sum($arr)/count($arr);
        $distance = [];

        for($i=0;$i<count($arr);$i++){
            $distance[$i] = abs($mean - $arr[$i]);
        }
        $binWidth = array_sum($distance)/count($distance);

        return array($diiference, ceil($binWidth), $min);
    }

    //Histogram data (frequency of response time occurance)
    function getHistogram(){
        $timeValues = $this->getOnlyValues();
        $differenceMaxMin = $this->getBinWidth($timeValues)[0];
        $binWidth = $this->getBinWidth($timeValues)[1];
        $min = $this->getBinWidth($timeValues)[2];
        $noOfBins = ceil($differenceMaxMin/$binWidth);

        $ranges = [];
        $rangeLimit = 0;
        $rangeStart = $min;

        // getting all range values
        for($i=0;$i<$noOfBins;$i++){
            echo $rangeStart."---".$rangeStart+$binWidth."<br>";
            $ranges[$i] = array($rangeStart, $rangeStart+$binWidth);
            $rangeStart += $binWidth+1;
        }
        echo "<br><br>";
        
        // echo $ranges[0][0]."<br>";
        // echo $binWidth."<br>";
        // echo $ranges[0][1]."<br><br>";
        $frequency[0] = 0;

        $sortTimeValues = $timeValues;
        sort($sortTimeValues);
        
        // print sorted array
        $arrlength = count($sortTimeValues);
        for($x = 0; $x < $arrlength; $x++) {
          echo $sortTimeValues[$x];
          echo "<br>";
        }
        echo "<br>";
        $j=0; 
        $countContinue=0;
        // getting frequency for each bin 
        for($k=0; $k<count($ranges); $k++){
            for($i=0; $i<count($sortTimeValues); $i++){
                if($ranges[$k][0] <= $sortTimeValues[$i]  &&  $ranges[$k][1] >= $sortTimeValues[$i]){
                    echo "Value (IF): ".$sortTimeValues[$i]."<br>";
                    $frequency[$j] = $frequency[$j]+1;
                    // echo "Frequency: ".$frequency[$j];
                    $countContinue = 0;  
                }
                else{ 
                    if($countContinue == 0){
                        if($ranges[$k][0] <= $sortTimeValues[$i]  &&  $ranges[$k][1] >= $sortTimeValues[$i]){
                            echo "<br> Value (ElseIF): ".$sortTimeValues[$i];
                            $frequency[$j] = $frequency[$j]+1;
                            // echo "Frequency: ".$frequency[$j];
                            $countContinue = 0;  
                        }
                    }
                    
                    $countContinue += 1;
                    continue;
                }
            }
            $j+=1;
            $frequency[$j] = 0;
            echo "<br>";
            echo "Frequency: ".$frequency[$k]."<br><br>";
        }
        echo "<br><br>";
        echo $frequency[0]."<br>";
        echo $frequency[1]."<br>";
        echo $frequency[2]."<br><br>";



        // getting all range values
        for($i=0;$i<$noOfBins;$i++){
            echo $rangeStart." --- ".$rangeStart+$binWidth.": ".$frequency[$i]."<br>";
            $ranges[$i] = array($rangeStart, $rangeStart+$binWidth);
            $rangeStart += $binWidth+1;
        }
        echo "<br><br>";
        


    }

    // No. of Bins(k) = ceil((max(x)-min(x))/bin-width)
    // $resTimes = [10,2,5,7,5,20]
    // binWidth = 5
    // 2 - 7 : 4
    // 8 - 13 : 1
    // 14 -19 : 0
    // 20 - 25: 1


}

//Let's assume
$uri1 = array(
    0=> array(
        'uriName' => "uri".rand(1,4),
        'value' => rand(10000,30000)
    ),
);




$myClass = new MyClass();
$myClass->getResponseData($uri1[0]['value']);
$myClass->getResponseData($uri1[0]['value']);
$myClass->getResponseData($uri1[0]['value']);
$myClass->getResponseData($uri1[0]['value']);
$myClass->getResponseData($uri1[0]['value']);
$myClass->getResponseData($uri1[0]['value']);
$myClass->getResponseData($uri1[0]['value']);
$myClass->getResponseData($uri1[0]['value']);
$myClass->getResponseData($uri1[0]['value']);
echo "<br><br><br>";
// $myClass->getOnlyNames();
// $myClass->getIndividualStd();
$myClass->getHistogram();

// echo count($uri);

// echo $uri[0]['value']."<br>";

// $uri1 = 10000;

// $requestResponseTime = new MyClass();
// $stratTime = $requestResponseTime->start();



// echo $myClass->getResponseTime()."miliseconds";
// for($x=0;$x<10000000;$x++){
//     //
// }
// echo $requestResponseTime->finish($stratTime);



// $start = microtime(true);

// for($x=0;$x<10000000;$x++){
//     //
// }

// $end = microtime(true);

// echo ($end-$start)."mseconds";

// $array = [
//     "foo" => "bar",
//     "bar" => "foo",
// ]
// $array["foo"];

//mean
//array_sum($values) / count($values);


// function to calculate the standard deviation
    // of array elements
    // function Stand_Deviation($arr)
    // {
    //     $num_of_elements = count($arr);
          
    //     $variance = 0.0;
          
    //             // calculating mean using array_sum() method
    //     $average = array_sum($arr)/$num_of_elements;
          
    //     foreach($arr as $i)
    //     {
    //         // sum of squares of differences between 
    //                     // all numbers and means.
    //         $variance += pow(($i - $average), 2);
    //     }
          
    //     return (float)sqrt($variance/$num_of_elements);
    // }
      
    // // Input array
    // $arr = array(2, 3, 5, 6, 7);
      
    // print_r(Stand_Deviation($arr));


?>
