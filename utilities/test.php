<?php
/**
 * Created by PhpStorm.
 * User: Gregory
 * Date: 3/31/2019
 * Time: 2:28 PM
 */

if(PHP_SAPI != "cli"){
    exit;
}

// DEFINE DATABASE VARIABLES
define("HOST", "127.0.0.1");               // The host you want to connect to.
define("PORT", "3306");                    // Port to connect
define("DATABASE", "lottery_lottery");
define("USER", "lottery_admin");    // The database username.
define("PASSWORD", "!Sochi28lot");  // The database password.
$cb = array("black" => "\e[1;30m", "red" => "\e[1;31m", "green" => "\e[1;32m", "yellow" => "\e[1;33m", "blue" => "\e[1;34m", "purple" => "\e[1;35m", "cyan" => "\e[1;36m", "white" => "\e[1;37m", "reset" => "\e[0m");
$cn = array("black" => "\e[0;30m", "red" => "\e[0;31m", "green" => "\e[0;32m", "yellow" => "\e[0;33m", "blue" => "\e[0;34m", "purple" => "\e[0;35m", "cyan" => "\e[0;36m", "white" => "\e[0;37m", "reset" => "\e[0m");


$number_array = array(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24);

$request = 12;
$picked = 0;
$seed = $argv[1];

echo "Fetch ".$request." numbers.";
if(isset($argv[1])){
    echo "Seed with ".$argv[1];
}
echo "\n";

$numbers = $number_array;

$result = array();

$test = new analyzeDataClass();

for($i = 0; $i < 1; $i++){
    $result = $test->RandomNumberPicker($numbers);
    //print_r($result[0]);

    // print_r($result[1]);
   /* foreach($result[1] AS $key => $val){
        echo $key."\n";
    }*/

   print_r($result[0]);
    // sweet spots:
    // $this->UnderOver, $result[1][0];
    // print_r($result[1][0]);
    if($result[1][0]["5/7"] === 1 || $result[1][0]["6/6"] === 1 || $result[1][0]["7/5"] === 1){
        echo $cb["yellow"]." OK TO ACCEPT ".$cb["white"]."UnderOver".$cb["yellow"]." PICK!\n".$cb["reset"];
        // $test->resetVariable
    }else{
        echo "failed on Even/Odd mix.\n";
        $i -= 1;
    }

    // $this->EvenOddMix, $result[1][1];
    print_r($result[1][1]);
    if($result[1][1]["5/7"] === 1 || $result[1][1]["6/6"] === 1 || $result[1][1]["7/5"] === 1){
        echo $cb["yellow"]." OK TO ACCEPT ".$cb["white"]."Even/Odd".$cb["yellow"]." PICK!\n".$cb["reset"];
    }else{
        echo "failed on Under/Over mix.\n";
        $i -= 1;
    }

    // $this->ConsecNumbers, $result[1][2];
    echo "ConsecNumbers Array:";
    print_r($result[1][2]);
/*    if($result[1][2][4] === 1 || $result[1][2][5] === 1 || $result[1][2][6] === 1){
        echo $cb["yellow"]." OK TO ACCEPT ".$cb["white"]."Consecutive Values".$cb["yellow"]." PICK!\n".$cb["reset"];
    }else{
        echo "failed on Under/Over mix.\n";
        $i -= 1;
    }*/

    print_r($result[0]);
    print_r($result);
    unset($result);
}

unset($test);

exit;
echo "Check past winners...\n";
$test = new analyzeDataClass();

$test->AnalyzeWinningNumbers();

echo "\n\n";
exit;

class analyzeDataClass {

    public $cb = array("black" => "\e[1;30m", "red" => "\e[1;31m", "green" => "\e[1;32m", "yellow" => "\e[1;33m", "blue" => "\e[1;34m", "purple" => "\e[1;35m", "cyan" => "\e[1;36m", "white" => "\e[1;37m", "reset" => "\e[0m");
    public $cn = array("black" => "\e[0;30m", "red" => "\e[0;31m", "green" => "\e[0;32m", "yellow" => "\e[0;33m", "blue" => "\e[0;34m", "purple" => "\e[0;35m", "cyan" => "\e[0;36m", "white" => "\e[0;37m", "reset" => "\e[0m");
    public $UnderOver = array("0/12" => 0, "1/11" => 0, "2/10" => 0, "3/9" => 0, "4/8" => 0, "5/7" => 0, "6/6" => 0, "7/5" => 0, "8/4" => 0, "9/3" => 0, "10/2" => 0, "11/1" => 0, "12/0" => 0);
    public $EvenOddMix = array("0/12" => 0, "1/11" => 0, "2/10" => 0, "3/9" => 0, "4/8" => 0, "5/7" => 0, "6/6" => 0, "7/5" => 0, "8/4" => 0, "9/3" => 0, "10/2" => 0, "11/1" => 0, "12/0" => 0);
    public $ConsecNumbers = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
    public $NonConsecNumbers = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
    public $PickedNumbers = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);

    public function __construct()
    {

    }

    public function AnalyzeWinningNumbers(){
        echo "starting DB connection...\n";
        $mysqli = new mysqli(HOST, USER, PASSWORD, DATABASE, PORT);
        $prep_stmt = "SELECT drawing_date, n_1, n_2, n_3, n_4, n_5, n_6, n_7, n_8, n_9, n_10, n_11, n_12, won FROM winners;";

        $stmt = $mysqli->prepare($prep_stmt);
        if($stmt){
            $stmt->execute();
            $stmt->bind_result($date, $n1, $n2, $n3, $n4, $n5, $n6, $n7, $n8, $n9, $n10, $n11, $n12, $won);
            while($stmt->fetch()){
                $result = array($n1, $n2, $n3, $n4, $n5, $n6, $n7, $n8, $n9, $n10, $n11, $n12);
                $this->IdentifyPatterns($result);
            }
        }else{
            echo "No connection.\n";
            die();
        }
    }

    public function RandomNumberPicker($numbers)
    {
        $picked = 0;
        $result = array();

        while($picked < 12){
            $picked++;
            $rand_key = array_rand($numbers, 1);
            $number = $numbers[$rand_key];
            // echo $picked." number is : ".$number."\n";
            $result[] = $number;
            unset($numbers[$rand_key]);
        }

        $data = $this->IdentifyPatterns($result);
        return array($result, $data);
    }

    public function IdentifyEvenOddPatterns($result)
    {
        $even = $odd = 0;
        foreach($result AS $key => $val) {
            if ($val % 2 === 0) {
                $even++;
            } else {
                $odd++;
            }
        }
        $this->EvenOddMix[$even."/".$odd] += 1;
    }

    public function IdentifyUnderOverPatterns($result)
    {
        $under = $over = 0;
        foreach($result AS $key => $val){
            if($val <= 12) {
                $under++;
            } else {
                $over++;
            }
        }
        $this->UnderOver[$under."/".$over] += 1;
    }

    public function IdentifyConsecutiveNumberPatterns($result)
    {
        asort($result);

        $i = 0;
        $c = 1;
        $max = 1;
        $chk = 0;
        foreach($result AS $key => $val){
            if ($i == 0) {
                $chk = $val;
                $i++;
            } else {
                $i++;

                if($val - $chk == 1){
                    $c++;
                }else{
                    if($c > $max) $max = $c;
                    $c = 1;
                }
                $chk = $val;
            }
        }
        if($c > $max) $max = $c;
        $this->ConsecNumbers[$max] += 1;
    }

    public function IdentifyPickedNumbersPatterns($result)
    {
        foreach($result AS $key => $val) {
            $this->PickedNumbers[$val] += 1;
        }
    }

    public function IdentifyPatterns ($result)
    {
        // Even and Odd, Under and Over mix test
        $even = $odd = 0;
        $under = $over = 0;
        // echo "Validate Odd\Even\n";
        foreach($result AS $key => $val){
            $this->PickedNumbers[$val] += 1;
            if($val % 2 === 0) {
                $even++;
            } else {
                $odd++;
            }

            if($val <= 12) {
                $under++;
            } else {
                $over++;
            }
        }

        // Consecutive Number analyzer
        asort($result);
        // print_r($result);

        $i = 0;
        $c = 1;
        $max = 1;
        $chk = 0;
        foreach($result AS $key => $val){
            if ($i == 0) {
                $chk = $val;
                $i++;
            } else {
                $i++;
                if($val - $chk == 1){
                    $c++;
                }else{
                    if($c > $max) $max = $c;
                    $c = 1;
                }
                $chk = $val;
            }
        }
        if($c > $max) $max = $c;

        $this->UnderOver[$under."/".$over] += 1;
        $this->EvenOddMix[$even."/".$odd] += 1;
        $this->ConsecNumbers[$max] += 1;
        // $this->NonConsecNumbers[$max] += 1;
        return array($this->UnderOver, $this->EvenOddMix, $this->ConsecNumbers); // , $this->PickedNumbers Mix of Picked Numbers is fully random since any number has equal chance to get picked first.
    }

    public function __destruct()
    {
        echo "\n\nRESULTS EVEN/ODD MIX: \n\n";
        $total = array_sum($this->EvenOddMix);
        foreach($this->EvenOddMix AS $key => $val){
            $this->output($key, $val, $total, "Even/Odd Mix");
        }

        echo "\n\nRESULTS Under/Over MIX: \n\n";
        $total = array_sum($this->UnderOver);
        foreach($this->UnderOver AS $key => $val){
            $this->output($key, $val, $total, "Under/Over 12 Mix");
        }

        echo "\n\nConsecutive Numbers: \n\n";
        $total = array_sum($this->ConsecNumbers);
        // echo spc(20, $total);
        foreach($this->ConsecNumbers AS $key => $val){
            if($key !== 0){
                if($key == 1) $key = "No";
                $this->output($key, $val, $total, "Consec Numbers");
            }
        }

        echo "\n\n";
    }

    public function output($key, $val, $total, $message){
        $percent = $val / $total;
        $percent = number_format($percent * 100, 2, '.', ',');

        if($percent < 1) {
            $dilute = $this->cn["white"];
            $per = $this->cn["red"];
        }elseif($percent < 10){
            $dilute = $this->cn["white"];
            $per = $this->cn["yellow"];
        }else{
            $dilute = $this->cb["white"];
            $per = $this->cb["green"];
        }

        echo $key;
        echo spc(5, $key);

        echo $dilute." ".$message." ";
        echo spc(20, " ".$message." ");
        echo spc(7, $percent." %");
        echo $per;
        echo $percent." %\n";
        echo $this->cb["reset"];
    }
}

function microtime_float() {
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}

function spc($count, $string) {
    $spaces = strlen($string);
    $count = $count - $spaces;
    $i = 0;
    while ($i < $count) {
        echo " ";
        $i++;
    }
    return false;
}

function exitScript($full_start){
    global $cb;
    $full_end = microtime_float();
    $full = $full_end - $full_start;
    echo "\n".$cb["white"]."****************************************************************************************************\n".$cb["reset"];
    echo "  EXECUTION TIME: ".$cb["white"].$full.$cb["reset"]." seconds\n  ";
    echo echo_memory_usage()."\n";
    echo $cb["white"]."****************************************************************************************************\n".$cb["reset"]."\n";
    exit;
}

function echo_memory_usage(){
    global $cb;
    $mem_usage = memory_get_usage(true);
    echo $cb["reset"]."MEMORY USED...: ".$cb["white"];
    if($mem_usage < 1024)
        echo $mem_usage." bytes";
    elseif($mem_usage < 1048576)
        echo round($mem_usage/1024,2).$cb["reset"]." Kb";
    else
        echo round($mem_usage/1048576,2).$cb["reset"]." Mb";
    echo $cb["reset"];
    return;
}