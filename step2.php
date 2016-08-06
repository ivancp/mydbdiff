<?php
require "mydbdiff.php";

$diff = new MyDBDiff();

$diff->loadCookieParams();
$diff->connect();
$diff->diffTables();
$diff->close();
