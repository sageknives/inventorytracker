<?php
//conn.php
//Updated

function conn($access="",$improved = TRUE)
{
	$myHostName = "localhost";#provide default DB credentials here
    $myUserName = "sagegatz_ianon";
    $myPassword = "1n0nym0u5";
    $myDatabase = "sagegatz_inventory_tracker";
    
	if($access != "")
	{#only check access if overwritten in function
		switch(strtolower($access))
		{# Optionally overwrite access level via function
			case "admin":	
				$myUserName = "sagegatz_todoset";
    			$myPassword = "P@yg0456";	
				break;
			case "delete":	
				$myUserName = "mysqluser"; 
				$myPassword = "xxxxxx"; 
				break;	
			case "insert":	
				$myUserName = "sagegatz_paygo";
    			$myPassword = "P@yg0456";
				break;
			case "update":	
				$myUserName = "mysqluser"; 
				$myPassword = "xxxxxx"; 
				break;
			case "select":	
				$myUserName = "sagegatz_ianon"; 
				$myPassword = "1n0nym0u5"; 
				break;
			case "trigger":	
				$myUserName = "sagegatz_ianon"; 
				$myPassword = "1n0nym0u5"; 
				break;		
			
		}
	}
	if($improved)
	{//create mysqli improved connection
		$myConn = @mysqli_connect($myHostName, $myUserName, $myPassword, $myDatabase) or die(trigger_error(mysqli_connect_error(), E_USER_ERROR));
		$myConn->query("SET SQL_BIG_SELECTS=1");
		$myConn->set_charset("utf-8");
	}else{//create standard connection
		$myConn = @mysql_connect($myHostName,$myUserName,$myPassword) or die(trigger_error(mysql_error(), E_USER_ERROR));
		mysql_select_db($myDatabase, $myConn) or die(trigger_error(mysql_error(), E_USER_ERROR));
	}
	//return "username:" . $myUserName . ",password:" . $myPassword . ",hostname:" . $myHostName . ",database:" . $myDatabase;
	return $myConn;
}

class IDB 
{ 
	private static $instance = null; #stores a reference to this class

	private function __construct() 
	{#establishes a mysqli connection - private constructor prevents direct instance creation 
		$myHostName = "localhost";#provide default DB credentials here
        $myUserName = "sagegatz_ianon";
        $myPassword = "1n0nym0u5";
        $myDatabase = "sagegatz_inventory_tracker";
		#hostname, username, password, database
		$this->dbHandle = mysqli_connect($myHostName,$myUserName, $myPassword, $myDatabase) or die(trigger_error(mysqli_connect_error(), E_USER_ERROR)); 
	} 

	public static function conn() 
    {#Creates a single instance of the database connection 
      if(self::$instance == null){self::$instance = new self;}#only create instance if does not exist
      return self::$instance->dbHandle;
    }
}


?>