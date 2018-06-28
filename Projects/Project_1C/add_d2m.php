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
			<h2>Add new actor or director</h2>
			 <form method = "GET" action="<?php echo $_SERVER['PHP_SELF'];?>">
			 	<label for="Movie_Title">Movie_Title:</label><br>
			 	 	<select name="mid">
			 			<?php
							// Establishing a Connection
							$db = new mysqli('localhost', 'cs143', '', 'CS143');
							if($db->connect_errno > 0){
	    						die('Unable to connect to database [' . $db->connect_error . ']');
							}

							$query = "select * from Movie";
							if (!($rs = $db->query($query))){ 
		    					$errmsg = $db->error;  					
							    print "Query failed: $errmsg <br />";							    
	    						exit(1);
							}

							while($row = $rs->fetch_assoc()){
										print "<option value='$row[id]'>$row[title]</option>";
							}

							$rs->free();
							$db->close();
						?>
					</select></br>
					<label for="Director_Name">Director Name:</label><br>
				 	 	<select name="did">
					 	 	<?php
					 	 			// Establishing a Connection
									$db = new mysqli('localhost', 'cs143', '', 'CS143');
									if($db->connect_errno > 0){
	    								die('Unable to connect to database [' . $db->connect_error . ']');
							}
									//$query = "SELECT id, first, last, dob FROM Actor ORDER BY first, last, id";
									$query = "SELECT id, first, last, dob FROM Director ORDER BY id";
									if (!($rs = $db->query($query))){ 
				    					$errmsg = $db->error;  					
									    print "Query failed: $errmsg <br />";							    
			    						exit(1);
									}

									while($row = $rs->fetch_assoc()){
										print "<option value='$row[id]'>$row[first] $row[last] $row[dob] </option>";
									}
							$rs->free();
							$db->close();
							?>
				</select></br>
				<br>
                <button type="submit">Add!</button>
			 </form>
		</div>
	</div>

<?php

	$mid = $_GET['mid'];
	$did = $_GET['did'];

	// Establishing a Connection
	$db = new mysqli('localhost', 'cs143', '', 'CS143');
	if($db->connect_errno > 0){
	   	die('Unable to connect to database [' . $db->connect_error . ']');
	}
	
	if($did != NULL && $mid != NULL)
			{
				//insert tuple into MovieActor
				$query = "INSERT INTO MovieDirector VALUES($mid, $did);";
				if (!($rs = $db->query($query))){ 
				    $errmsg = $db->error;  
				    print("<div class = 'col-sm-9 col-sm-offset-6 col-md-8 col-md-offset-3'>");  					
					print "Query failed: $errmsg <br />";
					print("</div>");							    
			    	exit(1);
				}
				else{
					print("<div class = 'col-sm-9 col-sm-offset-6 col-md-8 col-md-offset-3'>");  					
					print "Adding Success! <br />";
					print "mid = $mid<br>";
					print "did = $did<br>";	
					print("</div>");
				}

			}

	$rs->free();
	// Close connection
	$db->close();	

?>


</body>
</html>
