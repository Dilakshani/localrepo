<?php
//setting header to json
header('Content-Type: application/json');

//database
define('DB_HOST', '127.0.0.1');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'assignment');

//get connection
$mysqli = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

if(!$mysqli){
	die("Connection failed: " . $mysqli->error);
}

if(isset($_POST['selectyear'])){
    $selected_year = $_POST['selectyear'];
} else {
    $selected_year = 2016;
}

if(isset($_POST['selecttype'])){
    $selected_type = $_POST['selecttype'];
} else {
    $selected_type = 'asn';
}
                
//query to get data from the table
$query = sprintf("SELECT `cc`, COUNT(`value`) as value FROM `datastore` WHERE YEAR(`date`)= ".$selected_year." AND `type`='".$selected_type."' GROUP BY `cc`");


//execute query
$result = $mysqli->query($query);

//loop through the returned data
$data = array();
foreach ($result as $row) {
	$data[] = $row;
}

//free memory associated with result
$result->close();

//close connection
$mysqli->close();

//now print the data
print json_encode($data);