<?php
/*
 MyDBDiff class
 written by Ivan Cachicatari
 Date: sat dic 25 08:08:24 EST 2010
 
 Please send your comments to ivancp@latindevelopers.com

 This is a pre beta version, use under your own risk.
 Version History:
 
 2010-12-25  0.0.1 - First pre-beta version released.
 2010-12-27  0.0.2 - Fix connection issues.

 */
$version = "0.0.2 pre-beta";

class MyDBDiff
{
	var $o_conn; //source connection
	var $m_conn; //compare to connection
	var $db1; //connection to information_database
	var $db2; //connection to information_database
	var $db1conn; //connection to database1
	var $db2conn; //connection to database2
	function MyDBDiff()
	{
	
	}
	function getPostParams()
	{
		$this->o_conn = array();
		$this->m_conn = array();
		
		$this->o_conn["host"] = $_POST['ohost'];
		$this->o_conn["user"] = $_POST['ouser'];
		$this->o_conn["password"] = $_POST['opassword'];
		$this->o_conn["database"] = $_POST['odatabase'];
	
		$this->m_conn["host"] = $_POST['mhost'];
		$this->m_conn["user"] = $_POST['muser'];
		$this->m_conn["password"] = $_POST['mpassword'];
		$this->m_conn["database"] = $_POST['mdatabase'];

		$this->params2Cookie();
	}
	function  params2Cookie()
	{
		session_start();
		$_SESSION["config"]["o_conn"] = $this->o_conn;
		$_SESSION["config"]["m_conn"] = $this->m_conn;
	}
	function loadCookieParams()
	{
		session_start();
		if(isset($_SESSION["config"]["o_conn"]))
			$this->o_conn = $_SESSION["config"]["o_conn"];
		if(isset($_SESSION["config"]["m_conn"]))
			$this->m_conn = $_SESSION["config"]["m_conn"];

	}
	function getConfig($conf_name)
	{
		$opt = substr($conf_name,1,strlen($conf_name)-1);
		if(substr($conf_name,0,1) == "o")
			return $this->o_conn[$opt];
		if(substr($conf_name,0,1) == "m")
			return $this->m_conn[$opt];
	}
	function testSource()
	{
		$ret = false;
		$link = mysql_connect($this->o_conn["host"]
				,$this->o_conn["user"]
				,$this->o_conn["password"]);
		if($link)
		{
			if(mysql_select_db($this->o_conn["database"],$link))
			{
				$ret = true;
			}
			mysql_close($link);
		}
		return $ret;
	}
	function testDest()
	{
		$ret = false;
		$link = mysql_connect($this->m_conn["host"]
				,$this->m_conn["user"]
				,$this->m_conn["password"]);
		if($link)
		{
			if(mysql_select_db($this->m_conn["database"],$link))
			{
				$ret = true;
			}
			mysql_close($link);
		}
		return $ret;
	}
	function connect()
	{
		$this->db1 = mysql_connect($this->o_conn["host"]
				,$this->o_conn["user"]
				,$this->o_conn["password"],true);
	
		mysql_select_db("information_schema",$this->db1);
		

		$this->db2 = mysql_connect($this->m_conn["host"]
				,$this->m_conn["user"]
				,$this->m_conn["password"],true);

		mysql_select_db("information_schema",$this->db2);

		$this->db1conn = mysql_connect($this->o_conn["host"]
				,$this->o_conn["user"]
				,$this->o_conn["password"],true);
	
		mysql_select_db($this->o_conn["database"],$this->db1conn);

		$this->db2conn = mysql_connect($this->m_conn["host"]
				,$this->m_conn["user"]
				,$this->m_conn["password"],true);

		mysql_select_db($this->m_conn["database"],$this->db2conn);
	}
	function close()
	{
		mysql_close($this->db1);
		mysql_close($this->db2);
		mysql_close($this->db1conn);
		mysql_close($this->db2conn);
	}

	function getFieldSpec(&$spec)
	{
		return "<td>".$spec['COLUMN_NAME']." ".$spec['COLUMN_TYPE']."</td><td>".
			$spec['IS_NULLABLE']."</td><td>".$spec['COLUMN_DEFAULT']."</td><td>".$spec['COLLATION_NAME']."</td>";
	}
	function getFieldName(&$spec)
	{
		return $spec['COLUMN_NAME']." ".$spec['COLUMN_TYPE'];
	}
	function getValueFromQuery($sql,&$val,$link)
	{
		$val = "";
		$res = mysql_query($sql,$link);
		if(!$res)
		{ 
			$val = mysql_error($link);
			
			return false;
		}
		$rows = mysql_affected_rows($link);

		if($rows == 0)
		{ 
			$val = "[empty]";
			return false;
		}

		if($rows == 1)
		{
			if($row = mysql_fetch_row($res))
			{
				$val = $row[0];
			}
			mysql_free_result($res);
			return true;
		}
		else
		{
			$val = array();
			$i=0;
			while($row = mysql_fetch_row($res))
			{
				$val[$i] = $row[0];
			}
			mysql_free_result($res);
			return true;
		
		}
	}
	function diffTable($t1,$t2,&$res)
	{
		$ret = "";
		$ok =  true;

		$sql1 = "SELECT * from COLUMNS WHERE TABLE_NAME ='$t1' AND TABLE_SCHEMA='{$this->o_conn["database"]}' ORDER BY COLUMN_NAME ";
		$sql2 = "SELECT * from COLUMNS WHERE TABLE_NAME ='$t2' AND TABLE_SCHEMA='{$this->m_conn["database"]}' ORDER BY COLUMN_NAME ";

		$fields1 = array();
		$fields2 = array();
		$specs1 = array();
		$specs2 = array();

		$res = mysql_query($sql1,$this->db1);
		$i = 0;
		while($row = mysql_fetch_array($res))
		{
			$fields1[$i] = $row["COLUMN_NAME"];
			$specs1[$i++] = $row;
		}
		mysql_free_result($res);

		$res = mysql_query($sql2,$this->db2);
		$j = 0;
		while($row = mysql_fetch_array($res))
		{
			$fields2[$j] = $row["COLUMN_NAME"];
			$specs2[$j++] = $row;
		}
		mysql_free_result($res);

		$ret.= "<table border=0 cellspacing=0 class=\"diff_table\">
				<tr ><th>Field</th><th>`{$this->o_conn["database"]}`.`$t1`</th><th>Null</th><th>Default</th> <th>Collation</th> 
				<th></th><th>`{$this->m_conn["database"]}`.`$t2`</th><th>Null</th><th>Default</th><th>Collation</th>  </tr>";
		$pos = 1;
		$k = 0;
		$h = 0;
		while($k < $i && $h < $j)
		{
			$ret.= "<tr><td>$pos</td>";

			if($fields1[$k] == $fields2[$h])
			{
				$ret.= $this->getFieldSpec($specs1[$k])."<td> </td>".
						$this->getFieldSpec($specs2[$h]);
			}
			elseif(strcmp($fields1[$k], $fields2[$h]) > 0)
			{
				$ret.= "<td  colspan=4></td><td>--&gt;X</td><td class=\"del\" colspan=4>`".$fields2[$h]."` <b>to be deleted</b></td>";
				$k--;
				$ok = false;
			}
			elseif(strcmp($fields1[$k], $fields2[$h]) < 0)
			{
				$ret.= $this->getFieldSpec($specs1[$k])."<td>--&gt;</td><td class=\"add\" colspan=4><b>create</b> ".$this->getFieldName($specs1[$k])."</td>";
				$h--;
				$ok = false;
			}

			$pos++;
			$k++;
			$h++;
			$ret.= "</tr> ";
		}
		while($h < $j)
		{
				$ret.= "<tr ><td>$pos</td><td colspan=4></td><td>--&gt;X</td><td class=\"del\" colspan=4>`"
					.$fields2[$h]."` <b>to be deleted</b></td></tr>";
				$h++;
				$pos++;
				$ok =  false;
		}
		while($k < $i)
		{
				$ret.= "<tr><td>$pos</td><td></td><td>--&gt;</td><td class=\"add\"><b>create</b> ".$this->getFieldSpec($specs1[$k])."</td></tr>";
				$k++;
				$ok = false;
		}
		$ret.= '</table>';
		$res = $ret;
		
		return $ok;
	}

	function diffTables()
	{
		$sql1 = "SELECT TABLE_NAME FROM TABLES WHERE TABLE_SCHEMA='{$this->o_conn["database"]}' ORDER BY TABLE_NAME";
		$sql2 = "SELECT TABLE_NAME FROM TABLES WHERE TABLE_SCHEMA='{$this->m_conn["database"]}' ORDER BY TABLE_NAME";
		$tables1 = array();
		$tables2 = array();

		$res = mysql_query($sql1,$this->db1);
		if(!$res) echo mysql_error($this->db1);
		$i = 0;

		while($row = mysql_fetch_array($res))
		{
			$tables1[$i++] = $row["TABLE_NAME"];
		}
		mysql_free_result($res);

		$res = mysql_query($sql2,$this->db2);
		if(!$res) echo mysql_error($this->db2);
		$j = 0;
		while($row = mysql_fetch_array($res))
		{
			$tables2[$j++] = $row["TABLE_NAME"];
		}
		mysql_free_result($res);

		echo '<table border=0 cellspacing=0 class="diff_table">
				<tr ><th>Table</th><th>'.$this->o_conn["database"].'</th><th>Records</th> <th></th><th>'.
				$this->m_conn["database"].'</th><th>Records</th></tr>';
		$pos = 1;
		$k = 0;
		$h = 0;
		while($k < $i && $h < $j)
		{
			echo "<tr><td>$pos</td>";
			$diff_table = "";
			$count1 = 0;
			$count2 = 0;

			if($tables1[$k] == $tables2[$h])
			{
				$this->getValueFromQuery("SELECT count(*) from `{$tables1[$k]}`",$count1,$this->db1conn);
				$this->getValueFromQuery("SELECT count(*) from `{$tables2[$h]}`",$count2,$this->db2conn);
				if($this->diffTable($tables1[$k],$tables2[$h],$diff_table))
				{
					echo "<td>".$tables1[$k]."</td><td>$count1</td><td>=</td><td>".$tables2[$h]."</td><td>$count2</td>";
					$diff_table = "";
				}
				else
				{
					echo "<td class=\"diff\">".$tables1[$k]."</td><td>$count1</td><td class=\"diff\"> != </td><td class=\"diff\">".
						$tables2[$h]."</td><td>$count2</td>";
				}
			}
			elseif(strcmp($tables1[$k], $tables2[$h]) > 0)
			{
				echo "<td colspan=2></td><td>--&gt;X</td><td class=\"del\" colspan=2>`".$tables2[$h]."` <b>to be deleted</b></td>";
				$k--;
			}
			elseif(strcmp($tables1[$k], $tables2[$h]) < 0)
			{
				echo "<td colspan=2>".$tables1[$k]."</td><td>--&gt;</td><td class=\"add\" colspan=2><b>create</b> `".$tables1[$k]."`</td>";
				$h--;
			}

			if(strlen($diff_table) > 0)
				$hidden_row = "<tr ><td></td><td colspan=5>$diff_table</td><tr>";
			else
				$hidden_row="";
			$pos++;
			$k++;
			$h++;
			echo "</tr> $hidden_row";
		}
		while($h < $j)
		{
				echo "<tr ><td>$pos</td><td > not found</td><td>--&gt;X</td><td class=\"del\">`"
					.$tables2[$h]."` <b>to be deleted</b></td></tr>";
				$h++;
				$pos++;
		}
		while($k < $i)
		{
				echo "<tr><td>$pos</td><td>".$tables1[$k]."</td><td>--&gt;</td><td class=\"add\"><b>create</b> `".$tables1[$k]."`</td></tr>";
				$k++;
		}
		echo '</table>';
		echo "$i tables found in {$this->o_conn["database"]} database <br>\n";
		echo "$j tables found in {$this->m_conn["database"]} database <br>\n";
	}
}
?>
