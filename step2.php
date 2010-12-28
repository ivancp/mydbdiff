<?php
/*
 MyDBDiff 
 written by Ivan Cachicatari
 Date: sat dec 25 08:08:24 EST 2010a
 Last-modify: dec 25 14:12:05 PET 2010
 Version 0.1
 
 Please send your comments to ivancp@latindevelopers.com

 This is a pre beta version, use under your own risk.
 */
require "mydbdiff.php";

$diff = new MyDBDiff();

$diff->loadCookieParams();
$diff->connect();

?>
<html>
<head>
<title>Mydbdiff tool <?=$version?></title>
<style>
table.diff_table {
	border: 1px solid #CCB;
/*	margin-bottom: 2em;*/
	font-family:Arial,Helvetica;
	font-size:12px;
	/*width: 100%;*/
}
table.diff_table th {
	/*background: url('img/grain_dark.gif');*/
	border: 1px solid #CCB;
	/*color: #555;*/
	text-align: center;
	background-color:gray;
}
table.diff_table tr {border-bottom: 1px solid #DDD;}
table.diff_table td, th {padding: 3px;}
table.diff_table td {
	/*background: url('img/grain_light.gif');*/
	border: 1px solid #DDC;
}
.diff  { background-color:yellow; }
.del  { background-color:orange; }
.add  { background-color:cyan; }

</style>
</head>
<body>
<h1>Mydbdiff tool v0.1</h1>
Searching for databases diff's.<br><br>
<?php
$diff->diffTables();
$diff->close();
?>
