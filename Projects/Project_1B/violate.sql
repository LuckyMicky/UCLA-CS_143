/* Daxuan Shu
   2*******1
   CS 143
   Project 1B */





-- Movie Constraints:

	INSERT INTO Movie VALUES (6, "foo", 2018, "R", "Daxuan Entertainment");
		-- Duplicate primary key.

	INSERT INTO Movie VALUES (10, "foo", 6666, "R", "Daxuan Entertainment");
		-- Violate Check year, release year is not reasonable.

-- Actor Constraints:

	INSERT INTO Actor VALUES (1, "B", "LOL", "Male", 19961120, \N);
		-- Duplicate primary key.

	INSERT INTO Actor VALUES (2, "B", "LOL", "Male", 30001120, \N);
		-- Violate Check year, year of birth is not reasonable.

	INSERT INTO Actor VALUES (1, "B", "LOL", "Male", 19968820, \N);
		-- Violate Check month, month of birth is not reasonable.

-- Director Constraints:

	INSERT INTO Director VALUES (16, "Fewwea", "TEGW", 19970103, \N);
		-- Duplicate primary key.

-- MovieGenre Constraints:

	INSERT INTO MovieGenre VALUES (2, "Drama");
		-- Duplicate primary key.

-- MovieDirector Constraints:

	INSERT INTO MovieDirector VALUES (3, 112);
		-- Duplicate primary key.

-- MovieActor Constraints:
	INSERT INTO MovieActor VALUES (2, 162, "Teemo");
		-- Duplicate primary key.

-- Review Constraints:
	INSERT INTO Review VALUES ("name", 20180422, 6, 5, "WestWorld is the best!");
	INSERT INTO Review VALUES ("name", 20180422, 6, 5, "WestWorld is the best!");
		-- Duplicate primary key.

	INSERT INTO Review VALUES ("name", 20180422, 6, 100, "WestWorld is the best!");
		-- rating out of range.

-- MaxPersonID Constraints:
	INSERT INTO MaxPersonID VALUES (6666);
	INSERT INTO MaxPersonID VALUES (6666);
		-- Duplicate primary key.

-- MaxMovieID Constraints:
	INSERT INTO MaxMovieID VALUES (6666);
	INSERT INTO MaxMovieID VALUES (6666);
		-- Duplicate primary key.














