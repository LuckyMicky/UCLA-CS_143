

/* Give me the names of all the actors in the movie 'Die Another Day'. 
   Please also make sure actor names are in this format:  <firstname> <lastname>   
   (separated by single space, **very important**). */

select 
	upper(concat(first, " ", last)) AS Name
from 
	Movie Mov,
	MovieActor MoA,
	Actor Act
where
	Mov.id = MoA.mid 
	AND MoA.aid = Act.id
	AND Mov.title = 'Die Another Day';


/* Method II 

select
	upper(concat(first, " ", last))
from Movie
inner join MovieActor on
	Movie.id = MovieActor.mid
inner join Actor on
	MovieActor.aid = Actor.id
where title = 'Die Another Day';

/* Test Sequence 
select
	upper(concat(first, " ", last))
from MovieActor
inner join Actor on
	MovieActor.aid = Actor.id
inner join Movie on
	Movie.id = MovieActor.mid
where title = 'Die Another Day';
*/

/*Give me the count of all the actors who acted in multiple movies.*/
select count(*) 
from(
	select 
		aid, 
		count(*) AS c
	from MovieActor 
	group by aid 
	Having c > 1
)sub;


/* Give me the info of movies that id < 20*/
select * from Movie where id < 20;







