/* Daxuan Shu
   204853061
   CS 143
   Project 1B */


CREATE TABLE Movie (
		id				INT ,
			-- Movie ID
		title			VARCHAR(100) ,
			-- Movie title
		year			INT ,
			-- Release year
		rating			VARCHAR(10) ,
			-- MPAA rating
		company			VARCHAR(50) ,
			-- Production company
		PRIMARY KEY (id), 
			-- Movie ID uniquely identifies a tuple apart from all other tuples and not NULL.
		CHECK (year >= 1500 AND year < 2100)
			-- check the release year is reasonalbe.
) ENGINE = INNODB;

CREATE TABLE Actor (
		id 				INT ,
			-- Actor ID
		last			VARCHAR(20) ,
			-- Last name
		first			VARCHAR(20) ,
			-- First name
		sex				VARCHAR(6) ,
			-- Sex of the actor
		dob				DATE ,
			-- Date of birth
		dod				DATE ,
			-- Date of death
		PRIMARY KEY (id) , 
			-- Actor ID uniquely identifies a tuple apart from all other tuples and not NULL.
		CHECK (year(dob) >= 1500 AND year(dob) < 2019) ,
			-- Check the year of birth is reasonable.
		CHECK (month(dob) >= 1 AND month(dob) <= 12)
			-- check the month of birth is reasonable.
) ENGINE = INNODB;

CREATE TABLE Director (
		id 				INT ,
			-- Director ID
		last 			VARCHAR(20) ,
			-- Last name
		first 			VARCHAR(20) ,
			-- First name
		dob 			DATE ,
			-- Date of birth
		dod 			DATE ,
			-- Date of death
		PRIMARY KEY (id) 
			-- Director ID uniquely identifies a tuple apart from all other tuples and not NULL.
) ENGINE = INNODB;

CREATE TABLE MovieGenre (
		mid				INT ,
			-- Movie ID
		genre 			VARCHAR(20) ,
			-- Movie genre
		PRIMARY KEY (mid, genre), 
			-- Movie ID and Movie genre as a composite uniquely identifies a tuple apart from all other tuples and not NULL.
		FOREIGN KEY (mid) REFERENCES Movie(id) 
			-- mid in MovieGenre does not uniquely identify a tuple, but does in relation Movie
) ENGINE = INNODB;

CREATE TABLE MovieDirector (
		mid				INT ,
			-- Movie ID
		did				INT ,
			-- Director ID
		PRIMARY KEY (mid, did), 
			-- Movie ID and Director ID as a composite uniquely identifies a tuple apart from all other tuples and not NULL.
		FOREIGN KEY (mid) REFERENCES Movie(id), 
			-- mid in MovieDirector does not uniquely identify a tuple, but does in relation Movie.
		FOREIGN KEY (did) REFERENCES Director(id) 
			-- did in MovieDirector does not uniquely identify a tuple, but does in relation Director.
) ENGINE = INNODB;


CREATE TABLE MovieActor (
		mid				INT ,
			-- Movie ID
		aid 		    INT ,
			-- Actor ID
		role			VARCHAR(50) ,
			-- Actor role in movie
		PRIMARY KEY (mid, aid) , 
			-- Movie ID and Actor ID as a composite uniquely identifies a tuple apart from all other tuples and not NULL.
		FOREIGN KEY (mid) REFERENCES Movie(id),  
			-- mid in MovieActor does not uniquely identify a tuple, but does in relation Movie.
		FOREIGN KEY (aid) REFERENCES Actor(id) 
			-- aid in MovieActor does not uniquely identify a tuple, but does in relation Actor.
) ENGINE = INNODB;


/* For Project 1C */

CREATE TABLE Review (
		name			VARCHAR(20) ,
			-- Reviewer name
		time			TIMESTAMP ,
			-- Review time
		mid				INT ,
			-- Movie ID
		rating			INT ,
			-- Review rating
		comment			VARCHAR(500) ,
			-- Reviewer comment		
		PRIMARY KEY (name, time, mid), 
			-- Reviewer name, Review time and Movie ID as a composite uniquely identifies a tuple apart from all other tuples and not NULL.
		FOREIGN KEY (mid) REFERENCES Movie(id), 
			-- mid in Review does not uniquely identify a tuple, but does in relation Movie.
		CHECK (rating >= 0 AND rating <= 5)
			-- the rating that the reviewer gave the movie should be in range of [0,5].
) ENGINE = INNODB;

CREATE TABLE MaxPersonID (
		id 				INT ,
			-- Max ID assigned to all persons	
		PRIMARY KEY (id) 
) ENGINE = INNODB;

CREATE TABLE MaxMovieID (
		id 				INT ,
			-- Max ID assigned to all movies	
		PRIMARY KEY (id) 
) ENGINE = INNODB;
























