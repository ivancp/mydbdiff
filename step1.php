<html>
<head>
<title>Mydbdiff tool <?=$version?></title>
</head>
<body>
<?php
require "mydbdiff.php";

$diff = new MyDBDiff();

$diff->getPostParams();
$db1 = $diff->testSource();
$db2 = $diff->testDest();
?>
<h1>Mydbdiff tool v0.1</h1>

<h2>Original DB</h2>
<?php
echo "Testing connection to original db ... ";
if($db1) 
	echo "OK";
else
	echo "FAIL";
?>


<h2>My  DB</h2>


<?php
echo "Testing connection to my db ... ";
if($db2) 
	echo "OK";
else
	echo "FAIL";


if($db1 && $db2)
{
echo '<br><br>
	<form action="step2.php">
	<input type="submit" value="Continue to step 2">
	</form>';
}
?>
</body>
</html>
