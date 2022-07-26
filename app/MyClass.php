<?php
namespace App;

/**
* Resource request processing class
*
* Instantiations of this class do state based processing of resource requests .
* To use , instantiate an object and call process () on a URI to get the response
* data. Children of this class can augment functionality by overriding start ()
* and finish ().
*/
class Request
{
    /**
    * Means for simulated response latencies
    *
    * Let ’s pretend that this doesn ’t actually exist in this class. Unit are
    * microseconds .
    */
    private const MEANS = [
        'uri1 ' => 10000 ,
        'uri2 ' => 20000
    ];
    /**
    * The default mean latency in microseconds
    *
    * Let ’s pretend that this doesn ’t actually exist in this class.
    */
    private const DEFAULT_MEAN = 15000;
    /**
    * Standard deviations for simulated response latencies
    *
    * Let ’s pretend that this doesn ’t actually exist in this class. Unit are
    * microseconds .
    */
    private const STDDEVS = [
    'uri1 ' => 2500 ,
    'uri2' => 7500];
    /**
    * The default standard deviation for latencies in microseconds
    *
    * Let ’s pretend that this doesn ’t actually exist in this class.
    */
    private const DEFAULT_STDDEV = 5000;
    /**
    * Simulate a delay that is specific to the URI in question
    *
    * Let ’s pretend that this function doesn ’t actually exist in this class
    *
    * @param string $uri The URI of the request endpoint
    */
    private static function simulateLatency (string $uri ): void
    {
    // The following puts execution to sleep by a random amount of
    // microseconds . This amount is generated by transforming PHP ’s uniform
    // random number generation into Gaussian random number generation via
    // the Box -Muller transformation .
        $responseTime = round(
        sqrt( -2.0 * log(mt_rand(
        PHP_FLOAT_EPSILON *mt_getrandmax(),
        mt_getrandmax())/mt_getrandmax()))
        * (self :: STDDEVS[$uri] ?? self :: DEFAULT_STDDEV )
        * cos(2*pi()*mt_rand()/mt_getrandmax())
        + (self :: MEANS[$uri] ?? self :: DEFAULT_MEAN ));
        if($responseTime >= 1)
        usleep($responseTime );
    }
    /**
    * Start processing the request in the child class
    *
    * @param string $uri The URI of the request endpoint
    */
    protected function start(string $uri ): void
    {
        // Base class version does nothing
    }
    
    /** Finish processing the request in the child class */
    protected function finish (): void
    {
        // Base class version does nothing
    }
    /**
    * Process the request
    *
    * @param string $uri The URI of the request endpoint
    * @return string The response data
    */
    final public function process(string $uri ): string
    {
        $this ->start($uri );
        // Let ’s pretend the following line is doing something instead of just
        // simulating response latency
        self :: simulateLatency ($uri );
        $this ->finish ();
        return 'Sample response.';
    }
}





/**
 * Given Request class is in "request.php", Including it here
 * Instantiation of a child class (MyClass) which will inherit from the parent "Request class"
 * To use, first, instantiate an object and call processStart() to get the process started.
 * Call the processStart() method (a number of times) to generate (a number of times) random data
 * To get Mean, call getIndividualMean()
 * To get Standard Deviation, call getIndividualStd()
 */

class MyClass extends Request{
    public $uri;
    public $responseTime;
    public $startTime;
    public $end;
    public $counter=0;
    public $uriList = array(
        //----array structure----
        // 0=> array(
        //     'uriName' => 'uri1',
        //     'value' => 10
        // ),
    );

    /**
     * Overriding start() method of parent class. This will start the microtime() funtion
     */
    protected function start(string $uri ): void{
        $this->startTime = microtime(true);
    }


    /**
     * Overriding start() method of parent class. This will finish the microtime() funtion
     */
    protected function finish (): void{
        $this->end = microtime(true);
    }


    /**
     * This will return all generated uriName and response times for each uri. To use,change protected to public 
     */
    protected function getData(){
        return $this->uriList;
    }


    /**
     * passing random UriName (uri1 to uri4) and random UriValue
     * Callig process() to get response data
     */
    public function processStart($uriName, $uriValue){
        $this->process($uriValue);
        
        $this->counter = $this->counter+1;  //counting process
        $this->responseTime = round($this->end - $this->startTime,3)*1000;   //1 sec = 1000 milliseconds

        // insert new data (uriName and response times) to uriList
        if (count($this->uriList) <= $this->counter-1){
            $this->uriList[count($this->uriList)] = array(
                'uriName' => $uriName,
                'value' => $this->responseTime
            );
        }
    }

     
    /**
     * Returns only a list of response time values. It Helps executing other methods.
     */
    protected function getOnlyValues(){
        for ($i=0; $i<count($this->uriList); $i++){
            $valuesArray[$i] = $this->uriList[$i]['value'];     
        }
        return $valuesArray;
    }

    /**
     * Returns only a list of URI's. It Helps executing other methods.
     */
    protected function getOnlyNames(){
        for ($i=0; $i<count($this->uriList); $i++){
            $namesArray[$i] = $this->uriList[$i]['uriName'];     
        }
        return $namesArray;
    }

    /**
     * This will calculate and return the Mean for the response time of all request of each seperate URI
     * @return this will return an associative array with URI and Mean for each seperate uri
     */
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

    /**
     * Implementation of the formula of total Standard Deviation. Helps getIndividualStd() method
     */
    protected function Std($arr)
    {
        $variance = 0.0; 
        $average = array_sum($arr)/count($arr);
          
        foreach($arr as $i)
        {
            // sum of squares of differences between all numbers and means.
            $variance += pow(($i - $average), 2);
        } 
        return (float)sqrt($variance/count($arr));
    }


    /**
     * This will calculate and return the Standard Deviation for the response time of all request of each seperate URI
     * @return this will return an associative array with URI and Standard Deviation for each seperate URI
     */
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


    /**
     * This will calculate and return the normalized histogram for the response time of all request of each seperate URI
     * @param $maxNoOfBins This is the maximum number of bins
     * @return this will return an associative array with URI, frequency starting range, frequency ending range and normalized frequency for each Seperate URI
     */
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
            $allHistograms[$key] = $this->getHistogram($maxNoOfBins,$value);
        }

        return $allHistograms;

    }

    /**
     * This will calculate the approx. bin width. It helps the getHistogram() method
     */
    protected function getBinWidth($arr, $maxNoOfBins){
        $min =  min($arr);
        $max = max($arr);
        $diiference = $max - $min;

        return array(ceil($diiference/$maxNoOfBins),$min);
    }

    /**
     * This will calculate and return a single histogram to the getIndividualHistogram() method
     */
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


        // Deleting the extra 0 at the end of $frequency, inserted by the loop
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
// $myClass = new MyClass();
// echo "<br><br><br>";

/**
 * How many process do you want, put the number in the $noOfProcess variable
 * Setting 10 process as default
 */
// $noOfProcess = 10;


/**
 * Let's assume we have 4 unique URI's and values of those are between 10k to 30k
 * For each unique uriName and uriValue, calling "getResponseData" method to generate data
 */
// for ($i=0; $i<$noOfProcess; $i++){
//     $uri1 = array(
//         0=> array(
//             'uriName' => "uri".rand(1,4),
//             'value' => rand(10000,30000)
//         ),
//     );
//     $myClass->processStart($uri1[0]['uriName'],$uri1[0]['value']);
// }




/**
 * $mean will give us the Mean of all response time for each seperate URI
 * $std will give us the Standard Deviation of all response time for each seperate URI
 * $hist will give us the normalized Histogram of all response time for each seperate URI
 * Inside "getIndividualHistogram()" method, pass Max. no. of Bins 
 */
// $mean = $myClass->getIndividualMean();
// $std = $myClass->getIndividualStd();
// $hist = $myClass->getIndividualHistogram(5);


/**
 * If you want to see all the mean of all response time for each seperate URI, run this in your browser
 */
// foreach($mean as $key => $value){
//     echo "Mean for: ".$key." is: ".$value."<br>";
// }
// echo "<br>";



/**
 * If you want to see the Standard Deviation of all response time for each seperate URI, run this in your browser
 */
// foreach($std as $key => $value){
//     echo "Standard Deviation for: ".$key." is: ".$value."<br>";
// }
// echo "<br>";

/**
 * If you want to see the normalized histogram with Actual no. of Bins, run this in your browser
 */
// foreach($hist as $x => $x_value) {
//     echo "URI=" . $x ;
//     for($i=0; $i<count($x_value);$i++){
//         echo "<br> " . $x_value[$i][0]." --- ".$x_value[$i][1]. " : ".$x_value[$i][2];
//     }
//     echo "<br><br>";
//   }





?>
