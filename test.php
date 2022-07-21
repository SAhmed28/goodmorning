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

    function getBinWidth($arr, $maxNoOfBins){
        $min =  min($arr);
        $max = max($arr);
        $diiference = $max - $min;

        return array(ceil($diiference/$maxNoOfBins),$min);

        
    }

    //Histogram data (frequency of response time occurance)
    function getHistogram($maxNoOfBins){
        $timeValues = $this->getOnlyValues();
        // $differenceMaxMin = $this->getBinWidth($timeValues)[0];
        $binWidth = $this->getBinWidth($timeValues, $maxNoOfBins)[0];
        $min = $this->getBinWidth($timeValues, $maxNoOfBins)[1];
        // $noOfBins = ceil($differenceMaxMin/$binWidth);
        $noOfBins = $maxNoOfBins;

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
        
        // initializing $frequency
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
                    $countContinue = 0;  
                }
                else{ 
                    if($countContinue == 0){
                        if($ranges[$k][0] <= $sortTimeValues[$i]  &&  $ranges[$k][1] >= $sortTimeValues[$i]){
                            echo "<br> Value (ElseIF): ".$sortTimeValues[$i];
                            $frequency[$j] = $frequency[$j]+1;
                            $countContinue = 0;  
                        }
                    }
                    $countContinue += 1;
                }
            }
            $j+=1;
            $frequency[$j] = 0;
            echo "<br>";
            echo "Frequency: ".$frequency[$k]."<br><br>";
        }


        // getting all range values
        for($i=0;$i<$noOfBins;$i++){
            echo $ranges[$i][0]." --- ".$ranges[$i][1]." : ".$frequency[$i]."<br>";
        }
        echo "<br><br>";


        // Deleting the extra 0 at the end of $frequency, got from the loop
        unset($frequency[array_key_last($frequency)]);


        //If last frequency count is 0, delete the range and frequency
        $loopCount = 0;
        while($loopCount<count($frequency)){
            if(end($frequency)== 0){
                unset($ranges[array_key_last($ranges)]);
                unset($frequency[array_key_last($frequency)]);
                echo "Deleting<br>";
            }
            $loopCount +=1;
        }


        // building 2-d associative array for returning the range start time(0), range end time(1) and frequency(2)
        for($i=0;$i<count($frequency);$i++){
            $histogramData[$i][0] = $ranges[$i][0];
            $histogramData[$i][1] = $ranges[$i][1];
            $histogramData[$i][2] = $frequency[$i];

            echo $histogramData[$i][0]."    ";
            echo $histogramData[$i][1]."    ";
            echo $histogramData[$i][2]."<br>";
        }

        // $histogramData = [position][start,end,frequency]
        return $histogramData;

    }

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
$myClass->getHistogram(5);

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
