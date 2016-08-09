<?php
/**********************************************************************************************************************
Author: Ryan Sloan
This process will read a F657 GL .txt file from LCDN and sort the data and analyze the credits and debits and whether or not
they balance and output the total and whether or not the balance to a web page
ryan@paydayinc.com
 *********************************************************************************************************************/
/**
Data Example:

Index: 0            1        2          3        4   5-num   6   7    8-DB   9-CR      10-name
PR061015 WK# 24, 99982473, ER WCA,   6/11/2015, 6508, 200,  20,  60,  2.3,       0, AGNES L OLSEN
PR061015 WK# 24, 99982473, NM-SUI,   6/11/2015, 6510, 200,  20,  60, 5.47,       0, AGNES L OLSEN
PR061015 WK# 24, 99982499, NETPAY,   6/11/2015, 1030, 100,   0,   0,    0,  483.07, AMANDA  JARAMILLO
PR061015 WK# 24, 99982499, OASDI,    6/11/2015, 2210, 100,   0,   0,    0,   44.27, AMANDA  JARAMILLO
PR061015 WK# 24, 99982499, ER OASDI, 6/11/2015, 2210, 100,   0,   0,    0,   44.27, AMANDA  JARAMILLO
PR061015 WK# 24, 99982499, MEDICARE, 6/11/2015, 2220, 100,   0,   0,    0,   10.35, AMANDA  JARAMILLO

 *
 **/

session_start();
if(isset($_FILES)) { //Check to see if a file is uploaded
    try {
        if (($log = fopen("log.txt", "w")) === false) { //open a log file
            //if unable to open throw exception
            throw new RuntimeException("Log File Did Not Open.");
        }

        $today = new DateTime('now'); //create a date for now
        fwrite($log, $today->format("Y-m-d H:i:s") . PHP_EOL); //post the date to the log
        fwrite($log, "--------------------------------------------------------------------------------" . PHP_EOL); //post to log

        $name = $_FILES['file']['name']; //get file name
        fwrite($log, "FileName: $name" . PHP_EOL); //write to log
        $type = $_FILES["file"]["type"];//get file type
        fwrite($log, "FileType: $type" . PHP_EOL); //write to log
        $tmp_name = $_FILES['file']['tmp_name']; //get file temp name
        fwrite($log, "File TempName: $tmp_name" . PHP_EOL); //write to log
        $tempArr = explode(".", $_FILES['file']['name']); //set file name into an array
        $extension = end($tempArr); //get file extension
        fwrite($log, "Extension: $extension" . PHP_EOL); //write to log

        //If any errors throw an exception
        if (!isset($_FILES['file']['error']) || is_array($_FILES['file']['error'])) {
            fwrite($log, "Invalid Parameters - No File Uploaded." . PHP_EOL);
            throw new RuntimeException("Invalid Parameters - No File Uploaded.");
        }

        //switch statement to determine action in relationship to reported error
        switch ($_FILES['file']['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_NO_FILE:
                fwrite($log, "No File Sent." . PHP_EOL);
                throw new RuntimeException("No File Sent.");
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                fwrite($log, "Exceeded Filesize Limit." . PHP_EOL);
                throw new RuntimeException("Exceeded Filesize Limit.");
            default:
                fwrite($log, "Unknown Errors." . PHP_EOL);
                throw new RuntimeException("Unknown Errors.");

        }

        //check file size
        if ($_FILES['file']['size'] > 2000000) {
            fwrite($log, "Exceeded Filesize Limit." . PHP_EOL);
            throw new RuntimeException('Exceeded Filesize Limit.');
        }

        //define accepted extensions and types
        $goodExts = array("txt", "csv");
        $goodTypes = array("text/plain", "text/csv", "application/vnd.ms-excel");

        //test to ensure that uploaded file extension and type are acceptable - if not throw exception
        if (in_array($extension, $goodExts) === false || in_array($type, $goodTypes) === false) {
            fwrite($log, "This page only accepts .txt and .csv files, please upload the correct format." . PHP_EOL);
            throw new Exception("This page only accepts .txt and .csv files, please upload the correct format.");
        }

        //move the file from temp location to the server - if fail throw exception
        $directory = "/var/www/html/ACB/ACBFiles";
        if (move_uploaded_file($tmp_name, "$directory/$name")) {
            fwrite($log, "File Successfully Uploaded." . PHP_EOL);
            //echo "<p>File Successfully Uploaded.</p>";
        } else {
            fwrite($log, "Unable to Move File to /ACBFiles." . PHP_EOL);
            throw new RuntimeException("Unable to Move File to /ACBFiles.");
        }

        //rename the file using todays date and time
        $month = $today->format("m");
        $day = $today->format('d');
        $year = $today->format('Y');
        $time = $today->format('H-i-s');

        $newName = "$directory/lcdnData-$month-$day-$year-$time.$extension";
        if ((rename("$directory/$name", $newName))) {
            fwrite($log, "File Renamed to: $newName" . PHP_EOL);
            //echo "<p>File Renamed to: $newName </p>";
        } else {
            fwrite($log, "Unable to Rename File: $name" . PHP_EOL);
            throw new RuntimeException("Unable to Rename File: $name");
        }

        //open the stream for file reading
        $handle = fopen($newName, "r");
        if ($handle === false) {
            fwrite($log, "Unable to Open Stream." . PHP_EOL);
            throw new RuntimeException("Unable to Open Stream.");
        } else {
            fwrite($log, "Stream Opened Successfully." . PHP_EOL);
            //echo "<p>Stream Opened Successfully.</p>";
        }

        //echo "<hr>";

        $fileData = array();


        //remove the header
        fgets($handle);

        //read the data in line by line
        while (!feof($handle)) {
            $line_of_data = fgets($handle); //gets data from file one line at a time
            $line_of_data = trim($line_of_data); //trims the data
            $fileData[] = explode(",", $line_of_data); //breaks the line up into pieces that the array can store
        }

        //close file reading stream
        fclose($handle);

        //var_dump($fileData);

        //remove empty lines
        $data = $fileData;
        foreach($data as $key => $line){

            if(count($line) < 7){
               var_dump($key);
               array_splice($data,$key,1);
            }
        }

        var_dump("DATA", $data, "DATAEND");

        //sort into credits and debits
        $credits = $debits = array();


        foreach($data as $line){
            if($line[1] !== ''){

                $credits[] = $line;
            }else{

                $debits[] = $line;
            }

        }

        var_dump("CREDITS", $credits, "DEBITS", $debits);

        //sort arrays in decending order
        $creditArr = array();

        foreach($credits as $value ){
            $creditArr[] = $value[1];
        }
        array_multisort($creditArr, SORT_DESC, $credits);

        $debitArr = array();

        foreach($debits as $value ){
            $debitArr[] = $value[2];
        }

        array_multisort($debitArr, SORT_DESC, $debits);

        var_dump("CREDITS1", $credits, "DEBITS1", $debits);

        //replace the codes
        foreach($credits as $key => $line){
            if($line[3] === '142'){
                $credits[$key][3] = '165';
            }
        }

        foreach($debits as $key => $line){
            if($line[3] === '451'){
                $debits[$key][3] = '455';
            }
        }

        //set 2ndReference column
        foreach($credits as $key => $line){
            if($line[4] === ''){
                $credits[$key][4] = '000000000000';
            }else{
                $credits[$key][4] = '000000005133';
            }
        }

        foreach($debits as $key => $line){
            if($line[4] === ''){
                $debits[$key][4] = '000000000000';
            }else{
                $debits[$key][4] = '000000005133';
            }
        }

        var_dump("CREDITS2", $credits, "DEBITS2", $debits);

        //cut out the client number
        foreach($credits as $key => $line){
            $var = explode(' ', $line[5]);
            $credits[$key][5] = str_pad('', 19) . $var[0];
        }

        foreach($debits as $key => $line){
            $var = explode(' ', $line[5]);
            $debits[$key][5] = str_pad('', 19) . $var[0];
        }

        var_dump("CREDITS3", $credits, "DEBITS3", $debits);


        //set string length of values
        foreach($credits as $key => $line){
            $credits[$key][1] = str_pad($line[1], 19);
        }

        foreach($debits as $key => $line){
            $debits[$key][2] = str_pad($line[2], 19);
        }

        var_dump("CREDITS4", $credits, "DEBITS4", $debits);


        $creditFileLines = $debitFileLines = array();

        foreach($credits as $key => $line){
            $creditFileLines[] = array('"'.$year.$month.$day.'"','""','""','""','""','"'.$line[3].'"','""','"'.$line[1].'"','""','"'.$line[4].'"','"'.$line[5].'"','""');
        }
        var_dump("CREDITFILELINES", $creditFileLines);
        //return to index.php
        header("Location: index.php");

        //close log
        fwrite($log, "Close Log --------------------------------------------------------------------------------" . PHP_EOL . PHP_EOL);
        fclose($log);


    } catch (Exception $e) {
        echo $e->getMessage();
        //header('Location: index.php?');
    }
}else{
    header('Location: index.php?output=<p>No File Was Selected</p>');
}
?>