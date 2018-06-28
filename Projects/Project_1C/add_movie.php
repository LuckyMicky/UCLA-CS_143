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
			<h2>Add new Movie</h2>
			 <form name = "add_movie" method = "GET" action="<?php echo $_SERVER['PHP_SELF'];?>" onsubmit = "checksubmit()">
			 	 <label for="title">Title:</label><br>
			 	 <input type="text" placeholder="Text input" name="title" size="70" ><br>
			 	 <label for="company">Company</label><br>
			 	 <input type="text"  placeholder="Text input" name="company" size="70"><br>
			 	 <label for="year">Year</label><br>
			 	 <input type="text"  placeholder="Text input" name="year" size="70"><br>
			 	 <div class="form-group">
                    <label for="rating">MPAA Rating</label>
                    <select   class="form-control" name="rate">
                        <option value="G">G</option>
                        <option value="NC-17">NC-17</option>
                        <option value="PG">PG</option>
                        <option value="PG-13">PG-13</option>
                        <option value="R">R</option>
                        <option value="surrendere">surrendere</option>
                    </select>
                </div>
                <div class="form-group">
                    <label >Genre:</label>
                    <input type="checkbox" name="genre[]" value="Action">Action</input>
                    <input type="checkbox" name="genre[]" value="Adult">Adult</input>
                    <input type="checkbox" name="genre[]" value="Adventure">Adventure</input>
                    <input type="checkbox" name="genre[]" value="Animation">Animation</input>
                    <input type="checkbox" name="genre[]" value="Comedy">Comedy</input>
                    <input type="checkbox" name="genre[]" value="Crime">Crime</input>
                    <input type="checkbox" name="genre[]" value="Documentary">Documentary</input>
                    <input type="checkbox" name="genre[]" value="Drama">Drama</input>
                    <input type="checkbox" name="genre[]" value="Family">Family</input>
                    <input type="checkbox" name="genre[]" value="Fantasy">Fantasy</input>
                    <input type="checkbox" name="genre[]" value="Horror">Horror</input>
                    <input type="checkbox" name="genre[]" value="Musical">Musical</input>
                    <input type="checkbox" name="genre[]" value="Mystery">Mystery</input>
                    <input type="checkbox" name="genre[]" value="Romance">Romance</input>
                    <input type="checkbox" name="genre[]" value="Sci-Fi">Sci-Fi</input>
                    <input type="checkbox" name="genre[]" value="Short">Short</input>
                    <input type="checkbox" name="genre[]" value="Thriller">Thriller</input>
                    <input type="checkbox" name="genre[]" value="War">War</input>
                    <input type="checkbox" name="genre[]" value="Western">Western</input>
                </div>
                <button type="submit" class="btn btn-default">Add!</button>
			 </form>
		</div>
	</div>


<?php
	// trim function removes the whitespace on both sides of the string.
	$title = trim($_GET['title']);
	$company = trim($_GET['company']);
	$year = $_GET['year'];
	$rating = $_GET["rate"];
	$genre = $_GET["genre"];

	// Establishing a Connection
	$db = new mysqli('localhost', 'cs143', '', 'CS143');
	if($db->connect_errno > 0){
	    die('Unable to connect to database [' . $db->connect_error . ']');
	}

	// increment MaxMovieID
	$Incre_MMid = "UPDATE MaxMovieID SET id = id + 1;";
	if (!($rs = $db->query($Incre_MMid))){ 
	    $errmsg = $db->error;
	    print("<div class = 'col-sm-9 col-sm-offset-6 col-md-8 col-md-offset-3'>");
	    print "Query failed: $errmsg <br />";
	    print("</div>");
	    exit(1);
	}

	//Get new id
	$Select_MMid = "SELECT id FROM MaxMovieID;";
	if (!($rs = $db->query($Select_MMid))){ 
	    $errmsg = $db->error;
	    print("<div class = 'col-sm-9 col-sm-offset-6 col-md-8 col-md-offset-3'>");
	    print "Query failed: $errmsg <br />";
	    print("</div>");
	    exit(1);
	}
	$row = $rs->fetch_assoc();
	foreach ($row as $key => $new_id){
	// 	print("<div class = 'col-sm-9 col-sm-offset-6 col-md-8 col-md-offset-3'>");
 // 	print("$new_id<br>");
	// print("</div>");
	}

	//Insert new movie

	if ($company == "")
		$query = "INSERT INTO Movie VALUES($new_id, '$title', $year, '$rating', NULL);";
	else
		$query = "INSERT INTO Movie VALUES($new_id, '$title', $year, '$rating', '$company');";

	if (!($rs = $db->query($query))){ 
	    $errmsg = $db->error;
	    print("<div class = 'col-sm-9 col-sm-offset-6 col-md-8 col-md-offset-3'>");
	    print "Query failed: $errmsg <br />";
	    print("</div>");
	    exit(1);
	}
	else {
		print("<div class = 'col-sm-9 col-sm-offset-6 col-md-8 col-md-offset-3'>");
		print "<p>Add success!</p>";
		print "<p style='font-size:15px'> New_id = $new_id, Title = $title, Year = $year, Rating = $rating, Company = $company";
		print("</div>");

	}

	//Insert genre to MovieGenre table
	for($i = 0; $i < count($genre); $i++){
		$query = "INSERT INTO MovieGenre VALUES('$new_id', '$genre[$i]')";
		if (!($rs = $db->query($query))){ 
	    $errmsg = $db->error;
	    print("<div class = 'col-sm-9 col-sm-offset-6 col-md-8 col-md-offset-3'>");
	    print "Query failed: $errmsg <br />";
	    print("</div>");
	    exit(1);
		}
		else{
		print("<div class = 'col-sm-9 col-sm-offset-6 col-md-8 col-md-offset-3'>");
		print "<p>Insert success to MovieGenre!</p>";
		print("</div>");
		}	
	}

	$rs->free();
	// Close connection
	$db->close();
?>

</body>
</html>
