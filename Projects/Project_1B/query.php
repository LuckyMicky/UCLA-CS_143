/* Daxuan Shu 2*******1
   CS 143
   Project 1B_query.php */

<!DOCTYPE html>
<html>

	<head>
			<title>CS143 Project1B_query.php</title>
	<style>
	table, th, td{
		border: 1px solid black;
	}
	@media screen and (min-width: 480px){
		body{
			background-color: lightgreen;
		}
	}
	</style>
	</head>

	<body>

	<h1>Web Query Interface</h1>

	<p> Type an SQL query in the following box: </p>
	<p> Example: SELECT * FROM Actor WHERE id = 10; </p>
	<p>
	<form method = "GET" action = "<?php echo $_SERVER['PHP_SELF'];?>">
	<textarea name="query" cols="80" rows="10"><?php if(isset($_GET['query'])) { echo htmlentities ($_GET['query']); }?></textarea><br>
 	<input type="submit" value="Submit">
	</form>
	</p>
	<p><small>Note: tables and fields are case sensitive. All tables in Project 1B are availale.</small>
	</p>


<?php

function check_null_print($x){
	if (is_null($x))
    	print "<td>N/A</td>";
    else
    	print "<td>".$x."</td>";
}
// Collect value of input field
	$query = $_GET['query'];
	$n = "N/A";
// Establishing a Connection
	$db = new mysqli('localhost', 'cs143', '', 'CS143');
	if($db->connect_errno > 0){
	    die('Unable to connect to database [' . $db->connect_error . ']');
	}
	if (!($rs = $db->query($query))){ 
	    $errmsg = $db->error;
	    print "Query failed: $errmsg <br />";
	    exit(1);
    }elseif (! sizeof(rs)) {
    	print "<h2>Results from MySQL:</h2>";
    	print "Empty set";
    }else { //Retrieving Results
    		print "<h2>Results from MySQL:</h2>";
    	    $row = $rs->fetch_assoc();
    		print "<table><tr>"; // create the table
    		foreach ($row as $key => $value) { // build table header
    			if (is_null($key))
    				print "<th>". $n ."</th>";
    			else
    				print "<th>".$key."</th>";
    		}
    		print "</tr>";
    		print "<tr>";
    		foreach ($row as $key => $value) { // build first content row
                    check_null_print($value);
    		} 
    		print "</tr>";
    		while($row = $rs->fetch_assoc()){ // build the following contents row
    			print "<tr>";
    			foreach ($row as $key => $value) {
                    check_null_print($value);
    			} // end foreach
    			print "</tr>";
    		  } // end while
    		print "</table>";
           } // end else
	
// Number of Returned Rows
	print 'Total results: ' . $rs->num_rows . '<br>';

// Free Result
	$rs->free();

// Number of Affected Rows
	print 'Total rows updated: ' . $db->affected_rows . '<br>';

// Close connection
	$db->close();

?>

</body>
</html>


















