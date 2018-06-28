<html>
<body style = "background-color:powderblue;">

<h1>Actor Information</h1>
<br>
<br>
<br>
<p style = "font-size:200%;">Search:</p>
</body>
</html>


<form method="GET" action="<?php echo $_SERVER['PHP_SELF'];?>">
  <input type="text" name="Actor_Name" SIZE=50 MAXLENGTH=100>
  <br>
  <br>
  <input type="submit" value="Click me!">
</form>

<?php



function check_null_print($x){
	if (is_null($x))
    	print "<td>N/A</td>";
    else
    	print "<td>".$x."</td>"; 
}

	
	
	
    $Actor_Name = $_GET['Actor_Name'];
	
	// Create connection
	$db = new mysqli('localhost', 'cs143', '', 'CS143');
	if($db->connect_errno > 0){
	    die('Unable to connect to database [' . $db->connect_error . ']');
	}
	
	// SQL query to extract Actor's name and date of birth
	$query = "SELECT 
	              UPPER(CONCAT(first,' ',last,' ')) AS name,
				  dob AS Date of Birth
		      FROM
			      Actor
			  WHERE
			      last LIKE '%$Actor_Name'
				  OR 
				  first LIKE '%$Actor_Name'";   //SYNTAX ERROR HERE
				  
	if(!($db->query($query)===TRUE)) {
		$errmsg = $db->error;
		print "Query failed: $errmsg</br>";
	    exit(1);
	}
	else { //Retrieving Results
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
	
	

				  
	
	
	
	
	
 
 

