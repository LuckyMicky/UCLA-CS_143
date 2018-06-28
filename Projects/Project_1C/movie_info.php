<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="author" content="Daxuan Shu">
		<meta name="keywords" content="CS143, UCLA, Project 1C, Movie, DataBase">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>CS143 Project_1C index.php</title>
		<!-- Latest compiled and minified CSS -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
		<style>
			.top-bar{
				background-color: #000000;
				border:  10px solid black;
			}
			.top-title{
				color: #bfbfbf;
				text-decoration: none;
				font-size: 40px;
				padding-left: 40px;
			}
			a.top-title:hover{
				color: #ffffff;
			} 
			.sidebar{
				background-color: #e6e6e6;
    			width: 350px;
    			position: fixed;
    			top: 80px;
    			left: 0;
    			overflow-x: hidden;
    			overflow-y: auto;
			}
			.navbar{

			}
			p{
				color: #000000;
				font-size: 26px		
    		}
			a{
				color: #3366cc;
				text-decoration: none;
			}
			table, th, td{
			border: 1px solid black;
			}
			.side-links-contents{
				font-size: 24px;
				list-style-type: none;
				line-height: 180%;
			}
			.main {
 				 padding: 20px;
			}
			@media (min-width: 768px) {
	  			.main {
	    			padding-right: 40px;
	   				padding-left: 40px;
	  			}
			}
.main .page-header {
  margin-top: 0;
}

		</style>
	</head>
<body>
		<div class = "top-bar">
			<a class="top-title" href="index.php">CS143 Movie DataBase System (Home Page)</a>
		</div>	

	<div class = "row">
		<nav class = "sidebar">
			<ul class = "side-links-contents">
				<p>Add new content</p>
				<li><a href="add_a_d.php">Add Actor/Director</a></li>
				<li><a href="add_movie.php">Add Movie Information</a></li>
				<li><a href="add_comments.php">Add Comments to Movies</a></li>
				<li><a href="add_a2m.php">Add Actor/Movie Relation</a></li>
				<li><a href="add_d2m.php">Add Director/Movie Relation</a></li>
			</ul>
			<ul class = "side-links-contents">
				<p>Browsering infomation</p>
				<li><a href="show_a.php">Show Actor Information</a></li>
				<li><a href="show_m.php">Show Movie Information</a></li>
			</ul>
			<ul class = "side-links-contents">
				<p>Search interface</p>
				<li><a href="search.php">Search Actor/Movie</a></li>
			</ul>
		</nav>
		<div class = "col-sm-9 col-sm-offset-6 col-md-8 col-md-offset-3 main">
			<h2>Movie Information Page :</h2>
			 <form method = "GET" action="<?php echo $_SERVER['PHP_SELF'];?>">
			 	<input type="text"  placeholder="Search..." name="text">
				<br>
                <button type="submit">Search O.O</button>
			 </form>
		</div>
	</div>

<?php

    // Establishing a Connection
	$db = new mysqli('localhost', 'cs143', '', 'CS143');
	if($db->connect_errno > 0){
	    die('Unable to connect to database [' . $db->connect_error . ']');
	}

	if (isset($_GET['mid'])){
		$mid = $_GET['mid'];
	}
	
	$query = "select title, year, rating, company from Movie where id = $mid";
	if (!($rs = $db->query($query))){ 
	    $errmsg = $db->error;
	    print("<div class = 'col-sm-9 col-sm-offset-6 col-md-8 col-md-offset-3'>");  
	    print "Query failed: $errmsg <br />";
	    print("</div>");
	    exit(1);
	 }
	 else{
	 	print("<div class = 'col-sm-9 col-sm-offset-6 col-md-8 col-md-offset-3'>");  
	 	print "<p>Movie Information is :</p>";
	 	print "<table><tr>"; // create the table
		print "<th> Title </th>";
		print "<th> Year </th>";
		print "<th> Rating </th>";
		print "<th> Company </th>";
		print "</tr>";
		while($row = $rs->fetch_assoc()){
							print "<tr>";
							foreach ($row as $key => $value) {
                    			if (is_null($value))
    							print "<td>N/A</td>";
    							else
    							print "<td>".$value."</td>";
                    			
    						} // end foreach	
							print "</tr>";	
						}
		print "<br>";

		echo "<b>average score: </b>";
		$query = "select AVG(rating) from Review where mid = $mid group by mid";
		if (!($rs = $db->query($query))){ 
	    $errmsg = $db->error;
	    print("<div class = 'col-sm-9 col-sm-offset-6 col-md-8 col-md-offset-3'>");  
	    print "Query failed: $errmsg <br />";
	    print("</div>");
	    exit(1);
	 } else{
	 	$row = $rs->fetch_assoc();
	 	print("<div class = 'col-sm-9 col-sm-offset-6 col-md-8 col-md-offset-3'>");  
	    foreach ($row as $key => $value) {
	    	print "average score = :$value";
	    }
	    print("</div>");
	 } 
// 		print "<p>Director in this Movie:</p>";
		
// 		$query ='SELECT Director.id, CONCAT(Director.first, " ", Director.last) as Name
// 	FROM Director
// 	JOIN MovieDirector ON MovieDirector.did = Director.id
// 	WHERE MovieDirector.mid = $mid
// ';

// 		if (!($rs = $db->query($query))){ 
// 		    $errmsg = $db->error;
// 		    print("<div class = 'col-sm-9 col-sm-offset-6 col-md-8 col-md-offset-3'>");  
// 		    print "Query failed: $errmsg <br />";
// 		    print("</div>");
// 	    exit(1);
// 	 	}
// 	 	else{
// 	 		 print("<div class = 'col-sm-9 col-sm-offset-6 col-md-8 col-md-offset-3'>");  
// 		    while($row = $rs->fetch_assoc()){
// 		    	print "$row[Name]<br />";
// 		    }
// 		    print("</div>");
// 	 	}
// 		print("</div>");

// 		print "<br>";
	// 	print "<p>Actors in this Movie:</p>";

	// 	$query = "SELECT Actor.id, CONCAT(Actor.first, " ", Actor.last) as Name, MovieActor.role as Role
	// 			  FROM Actor
	// 			  JOIN MovieActor ON MovieActor.aid = Actor.id
	// 			WHERE MovieActor.mid = $mid";
	// if (!($rs = $db->query($query))){ 
	// 	    $errmsg = $db->error;
	// 	    print("<div class = 'col-sm-9 col-sm-offset-6 col-md-8 col-md-offset-3'>");  
	// 	    print "Query failed: $errmsg <br />";
	// 	    print("</div>");
	//     exit(1);
	//  	}
	//  	else{
	//  		 print("<div class = 'col-sm-9 col-sm-offset-6 col-md-8 col-md-offset-3'>"); 
	//  		 print "<table><tr>"; // create the table
	// 		print "<th>  Name </th>";
	// 		print "<th>Role </th>"; 
	// 	    while($row = $rs->fetch_assoc()){
	// 	    	print "<tr>";
	// 	    	foreach ($row as $key => $value) {
 //                    			print "<td>";
 //                    			print "$value";
 //                    			print "</td>";
 //    						} // end foreach
 //    			print "</tr>";	
	// 	    }
	// 	    print "</table>";
	// 		print("</div>");
	 // 	}
		// print("</div>");
	  }
	


	 $rs->free();
	// Close connection
	$db->close();
?>


</body>
</html>