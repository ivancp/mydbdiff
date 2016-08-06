<?php
require "mydbdiff.php";

$diff = new MyDBDiff();

$diff->getPostParams();
$db1 = $diff->testSource();
$db2 = $diff->testDest();

$result = array();

$message = '';
if($db1) 
	$result['source'] ="ok";
else{
	$message .= "Can't connect to source database\n";	
	$result['source'] = "fail";
}

if($db2) 
	$result['dest'] = "ok";
else{
	$message .= "Can't connect to destination database\n";	
	$result['dest'] = "fail";
}


if($db1 && $db2)
{
	$result['result'] = 'ok';
}else{
	$result['result'] = 'fail';
}

$result['message'] = $message;

echo json_encode($result);