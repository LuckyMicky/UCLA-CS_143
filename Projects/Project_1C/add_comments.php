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
			<h2>Add Reviews</h2>
			 <form name = "add_comments" method = "GET" action="<?php echo $_SERVER['PHP_SELF'];?>">
			 	 <label for="uname">Your name:</label><br>
			 	 	<input type="text" placeholder="Your name:" name="name" size="70" ><br>
			 	<label for="Movie_name">Movie Name:</label><br>
			 		<select name="movie_name">
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
								foreach ($row as $key => $value){
									if ($key == "title")
										print "<option value='$value'>$value</option>";
								}
							}

							// Close connection
							$db->close();
						?>
			 		</select></br>
			 	 <label for="rating">Rating: (x out of 10)</label>
                    <select   class="form-control" name="rate">
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                        <option value="6">6</option>
                        <option value="7">7</option>
                        <option value="8">8</option>
                        <option value="9">9</option>
                        <option value="10">10</option>
                    </select>
                 <label for="comments:">Comments:</label><br>
                     <textarea name="comment" cols="80" rows="8" maxlength="500"></textarea><br>

                <button type="submit" class="btn btn-default">Add!</button>
			 </form>
		</div>
	</div>



<?php
	$usr_name = $_GET['name'];
	$m_name =  $_GET['movie_name'];
	$rating =  $_GET['rate'];
	$comment = $_GET['comment'];
	$mid = NULL;
	$timestamp = NULL;
	// Establishing a Connection
	$db = new mysqli('localhost', 'cs143', '', 'CS143');
	if($db->connect_errno > 0){
	   	die('Unable to connect to database [' . $db->connect_error . ']');
	}
	// Get current timestramp
	$query = "select now()";
	if (!($rs = $db->query($query))){ 
		$errmsg = $db->error;  					
		print("<div class = 'col-sm-9 col-sm-offset-6 col-md-8 col-md-offset-3'>");  					
		print "Query failed: $errmsg <br />";	
		print("</div>");							    
	    exit(1);
	}
	$row = $rs->fetch_assoc();
	foreach ($row as $key => $time){
		$timestamp = $time;
	}
	// Get mid based on movie name
	$query = "select id from Movie where title = '$m_name'";
	if (!($rs = $db->query($query))){ 
		$errmsg = $db->error;  					
		print("<div class = 'col-sm-9 col-sm-offset-6 col-md-8 col-md-offset-3'>");  					
		print "Query failed: $errmsg <br />";	
		print("</div>");							    
	    exit(1);
	}
	$row = $rs->fetch_assoc();
	foreach ($row as $key => $id){
		$mid = $id;
	}
	//insert new review
		if ($usr_name == "") {
			if ($comment == "")
				$query = "INSERT INTO Review VALUES(NULL, '$timestamp', $mid, $rating, NULL);";
			else
				$query = "INSERT INTO Review VALUES(NULL, '$timestamp', $mid, $rating, '$comment');";
		}
		else {
			if ($comment == "")
				$query = "INSERT INTO Review VALUES('$usr_name', '$timestamp', $mid, $rating, NULL);";
			else
				$query = "INSERT INTO Review VALUES('$usr_name', '$timestamp', $mid, $rating, '$comment');";
		}
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
		print("</div>");	
	}

	$rs->free();
	// Close connection
	$db->close();						
?>

</body>
</html>