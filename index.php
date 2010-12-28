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
?>
<html>
<head>
<title>Mydbdiff tool <?=$version?></title>
</head>
<body>
<h1>Mydbdiff tool v0.1</h1>
This is another MySQL diff tool.<br>
This is a pre beta version, this is not ready but you can use it to test under your own risk.<br>
Please send your comments to ivancp@latindevelopers.com

<h2>Original DB</h2>

<form action="step1.php" method="post">
<table>
<tr> <td>Host</td> <td> <input type="text" name="ohost" value="<?=$diff->getConfig("ohost")?>"> </td></tr>
<tr> <td>User</td> <td> <input type="text" name="ouser" value="<?=$diff->getConfig("ouser")?>" >  </td></tr>
<tr> <td>Password</td> <td><input type="text" name="opassword" value="<?=$diff->getConfig("opassword")?>"> </td></tr>
<tr> <td>Database</td> <td><input type="text" name="odatabase" value="<?=$diff->getConfig("odatabase")?>"> </td></tr>
</table>

<h2>My DB</h2>

<table>
<tr> <td>Host</td> <td> <input type="text" name="mhost" value="<?=$diff->getConfig("mhost")?>"> </td></tr>
<tr> <td>User</td> <td> <input type="text" name="muser" value="<?=$diff->getConfig("muser")?>">  </td></tr>
<tr> <td>Password</td> <td><input type="text" name="mpassword" value="<?=$diff->getConfig("mpassword")?>"> </td></tr>
<tr> <td>Database</td> <td><input type="text" name="mdatabase" value="<?=$diff->getConfig("mdatabase")?>"> </td></tr>
</table>
<input type=submit value="Go diff">
</form>
</body>
</html>
