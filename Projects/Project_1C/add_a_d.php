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
			 	
                 <label for="first_name">First Name</label>
                  <input type="text"  placeholder="Actor or Director"  name="identity"/>
                <div >
                  <label for="first_name">First Name</label>
                  <input type="text" class="form-control" placeholder="Text input"  name="fname"/>
                </div>
                <div >
                  <label for="last_name">Last Name</label>
                  <input type="text" class="form-control" placeholder="Text input" name="lname"/>
                </div>
                <label >
                    <input type="radio" name="sex" checked="checked" value="male">Male
                </label>
                <label >
                    <input type="radio" name="sex" value="female">Female
                </label>
                <div >
                  <label for="DOB">Date of Birth</label>
                  <input type="text" class="form-control" placeholder="Text input" name="dob">ie: 1997-05-05<br>
                </div>
                <div >
                  <label for="DOD">Date of Death</label>
                  <input type="text" placeholder="Text input" name="dod">(leave blank if alive now)<br>
                </div>
                <button type="submit">Add!</button>
			 </form>
		</div>
	</div>

<?php
 
// print("<div class = 'col-sm-9 col-sm-offset-6 col-md-8 col-md-offset-3'>");
// print("Rose are red.");
// print("</div>");

	$Person_type = $_GET['identity'];
	$fname = $_GET['fname'];
	$lname = $_GET['lname'];
	$sex = $_GET['sex'];
	$dob = $_GET['dob'];
	$dod = $_GET['dod'];

	if($fname != "" && $lname != "" && $dob !=""){
	// Establishing a Connection
	$db = new mysqli('localhost', 'cs143', '', 'CS143');
	if($db->connect_errno > 0){
	    die('Unable to connect to database [' . $db->connect_error . ']');
	}

	// increment MaxMovieID
	$Incre_MPid = "UPDATE MaxPersonID SET id = id + 1;";
	if (!($rs = $db->query($Incre_MPid))){ 
	    $errmsg = $db->error;
	    print "Query failed: $errmsg <br />";
	    exit(1);
	}
	

	// Get new id
	$Select_MPid = "SELECT id FROM MaxPersonID;";
	if (!($rs = $db->query($Select_MPid))){ 
	    $errmsg = $db->error;
	    print "Query failed: $errmsg <br />";
	    exit(1);
	}
	$row = $rs->fetch_assoc();
	foreach ($row as $key => $new_id){
		print("<div class = 'col-sm-9 col-sm-offset-6 col-md-8 col-md-offset-3'>");
 	print("$new_id<br>");
	print("</div>");
	}
	

	// Insert new Actor or Director
	if ($Person_type == 'Actor'){
		if ($dod == NULL)
			$query = "INSERT INTO Actor VALUES($new_id, '$lname', '$fname', '$sex', '$dob', NULL);";
		else
			$query = "INSERT INTO Actor VALUES($new_id, '$lname', '$fname', '$sex', '$dob', $dod);";
	}
	else{
		if ($dod == NULL)
			$query = "INSERT INTO Director VALUES($new_id, '$lname', '$fname',  '$dob', NULL);";
		else
			$query = "INSERT INTO Director VALUES($new_id, '$lname', '$fname',  '$dob', '$dod');";
	}

	} // endif

	if (!($rs = $db->query($query))){ 
	    $errmsg = $db->error;
	    print "Query failed: $errmsg <br />";
	    exit(1);
	}

	$rs->free();
	// Close connection
	$db->close();

?>



</body>
</html>













