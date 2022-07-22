<?php

include "request.php";

class MyClass extends Request{
    public $uri;
    public $responseTime;
    public $startTime;
    public $end;
    public $counter=0;
    public $uriList = array(
        // 0=> array(
        //     'uriName' => 'uri1',
        //     'value' => 10
        // ),
    );

    protected function start(string $uri ): void{
       $this->startTime = microtime(true);
    }
    protected function finish (): void{
        $this->end = microtime(true);
    }

    // get all generated uriName and response times for each uri 
    public function getData(){
        return $this->uriList;
    }


    //passing random UriName (uri1 to uri4) and random UriValue
    // Callig process() to get response data
    public function getResponseData($uriName, $uriValue){
        $this->process($uriValue);
        
        $this->counter = $this->counter+1;  //counting process
        $this->responseTime = round($this->end - $this->startTime,3)*1000;   //1 sec = 1000 miliseconds

        // insert new data (uriName and response times) to uriList
        if (count($this->uriList) <= $this->counter-1){
            $this->uriList[count($this->uriList)] = array(
                'uriName' => $uriName,
                'value' => $this->responseTime
            );

        }
    }

    // Stores only response time values in an array. 
    protected function getOnlyValues(){
        for ($i=0; $i<count($this->uriList); $i++){
            $valuesArray[$i] = $this->uriList[$i]['value'];     //All values stored in this array
        }
        return $valuesArray;
    }

    protected function getOnlyNames(){
        for ($i=0; $i<count($this->uriList); $i++){
            $namesArray[$i] = $this->uriList[$i]['uriName'];     //All values stored in this array
        }
        return $namesArray;
    }


    public function getIndividualMean(){
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
        $meanIndividualArray = [];
        foreach($meanIndividual as $key => $value){
            $meanIndividual[$key] = array_sum($value)/count($value);
            $meanIndividualArray[$key] = $meanIndividual[$key];
        }
        return $meanIndividualArray;
    }

    // total std formula
    protected function Std($arr)
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



    // STD
    public function getIndividualStd(){
        $stdIndividual = [];
        $allNames = $this->getOnlyNames();
        $allValues = $this->getOnlyValues();

        for($i=0; $i<count($allNames); $i++){
            if(array_key_exists($allNames[$i],$stdIndividual)){
                $temp = $stdIndividual[$allNames[$i]];
                array_push($temp, $allValues[$i]);
                $stdIndividual[$allNames[$i]] = $temp;
            }
            else{
                $stdIndividual[$allNames[$i]] = array($allValues[$i]);
            }
        }
        
        // storing the standard deviation values and keys
        $stdIndividualArray = [];
        foreach($stdIndividual as $key => $value){
            $stdIndividual[$key] = $this->Std($value);
            // echo $key." ".$stdIndividual[$key]."<br>";
            $stdIndividualArray[$key] = $stdIndividual[$key];
        }

        return $stdIndividualArray;
    }

    public function getIndividualHistogram($maxNoOfBins){
        $uriResponseTimes = [];
        $allNames = $this->getOnlyNames();
        $allValues = $this->getOnlyValues();

        //arrange uri's with the response time, uri1 => [12, 15, 53] / uri2 => [112, 15, 53]
        for($i=0; $i<count($allNames); $i++){
            if(array_key_exists($allNames[$i],$uriResponseTimes)){
                $temp = $uriResponseTimes[$allNames[$i]];
                array_push($temp, $allValues[$i]);
                $uriResponseTimes[$allNames[$i]] = $temp;
            }
            else{
                $uriResponseTimes[$allNames[$i]] = array($allValues[$i]);
            }
        }
        $allHistograms = [];
        foreach($uriResponseTimes as $key => $value){
            // echo "This is for: ".$key."<br>";
            $allHistograms[$key] = $this->getHistogram($maxNoOfBins,$value);
        }

        return $allHistograms;

    }

    protected function getBinWidth($arr, $maxNoOfBins){
        $min =  min($arr);
        $max = max($arr);
        $diiference = $max - $min;

        return array(ceil($diiference/$maxNoOfBins),$min);
    }

    //Histogram data (frequency of response time occurance)
    protected function getHistogram($maxNoOfBins, $individualArr){
        $timeValues = $individualArr;
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
            $ranges[$i] = array($rangeStart, $rangeStart+$binWidth);
            $rangeStart += $binWidth+1;
        }
        // echo "<br><br>";
        
        // initializing $frequency
        $frequency[0] = 0;

        $sortTimeValues = $timeValues;
        sort($sortTimeValues);


        // j represents the index of $frequency
        $j=0; 
        $countContinue=0;
        // getting frequency for each bin 
        for($k=0; $k<count($ranges); $k++){
            for($i=0; $i<count($sortTimeValues); $i++){
                if($ranges[$k][0] <= $sortTimeValues[$i]  &&  $ranges[$k][1] >= $sortTimeValues[$i]){
                    $frequency[$j] = $frequency[$j]+1;
                    $countContinue = 0;  
                }
                else{ 
                    if($countContinue == 0){
                        if($ranges[$k][0] <= $sortTimeValues[$i]  &&  $ranges[$k][1] >= $sortTimeValues[$i]){
                            $frequency[$j] = $frequency[$j]+1;
                            $countContinue = 0;  
                        }
                    }
                    $countContinue += 1;
                }
            }
            $j+=1;
            $frequency[$j] = 0;
        }


        // getting all range values
        $sumFrequency = array_sum($frequency);
        for($i=0;$i<$noOfBins;$i++){
            $frequency[$i] = round($frequency[$i] / $sumFrequency,2);  //normalizing
        }


        // Deleting the extra 0 at the end of $frequency, got from the loop
        unset($frequency[array_key_last($frequency)]);


        //If last frequency count is 0, delete the range and frequency
        $loopCount = 0;
        while(end($frequency)== 0){
            unset($ranges[array_key_last($ranges)]);
            unset($frequency[array_key_last($frequency)]);
            $loopCount +=1;
        }


        // building 2-d associative array for returning the range start time(0), range end time(1) and frequency(2)
        for($i=0;$i<count($frequency);$i++){
            $histogramData[$i][0] = $ranges[$i][0];
            $histogramData[$i][1] = $ranges[$i][1];
            $histogramData[$i][2] = $frequency[$i];
        }

        // $histogramData = [position][start,end,frequency]
        return $histogramData;

    }

}




// Initialization of child class
$myClass = new MyClass();
echo "<br><br><br>";

/**
 * How many process do you want, put the number in the $noOfProcess variable
 * Setting 10 process as default
 */
$noOfProcess = 10;


/**
 * Let's assume we have 4 unique URI's and values of those are between 10k to 30k
 * For each unique uriName and uriValue, calling "getResponseData" method to generate data
 */
for ($i=0; $i<$noOfProcess; $i++){
    $uri1 = array(
        0=> array(
            'uriName' => "uri".rand(1,4),
            'value' => rand(10000,30000)
        ),
    );
    $myClass->getResponseData($uri1[0]['uriName'],$uri1[0]['value']);
}



// echo $myClass->getIndividualStd()."<br>";

/**
 * If you want to see all the generated data (URI and Response time), run this on your browser
 */
$myData = $myClass->getData();
for($i=0; $i<count($myData);$i++){
    echo "Entry No.: ".$i+1,"<br>URI Name: ".$myData[$i]["uriName"]." and ";
    echo "Reposnse Time: ".$myData[$i]["value"]."<br>";
}
echo "<br>";

//Mean of All
$mean = $myClass->getIndividualMean();
foreach($mean as $key => $value){
    echo "Mean for: ".$key." is: ".$value."<br>";
}
echo "<br>";

//Standard Deviation of All
$std = $myClass->getIndividualMean();
foreach($std as $key => $value){
    echo "Standard Deviation for: ".$key." is: ".$value."<br>";
}
echo "<br>";

/**
 * If you want to see the normalized histogram with Actual no. of Bins
 * Inside "getIndividualHistogram()" method, pass Max. no. of Bins 
 */
$hist = $myClass->getIndividualHistogram(5);
foreach($hist as $x => $x_value) {
    echo "Key=" . $x ;
    for($i=0; $i<count($x_value);$i++){
        echo "<br> " . $x_value[$i][0]." --- ".$x_value[$i][1]. " : ".$x_value[$i][2];
    }
    echo "<br><br>";
  }





?>
