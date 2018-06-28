

-- PART1(a)
SELECT 
    highway,
	area
FROM (SELECT 
          highway,
		  area,
		  COUNT(*)
	  FROM
	      caltrans
	  WHERE
		   (text LIKE '%CLOSED%') AND (text LIKE '%DUE TO SNOW%' OR text LIKE '%FOR THE WINTER%')
	  GROUP BY highway,area
	  )highway_closed
ORDER BY highway,area DESC
LIMIT 20;





-- PART1(b)

-- METHOD 1

SELECT 
    highway,
	area,
	COUNT(*)/365 AS pct_closed
FROM (SELECT
          highway,
		  area,
		  DATE(reported) as date,
		  COUNT(DATE(reported)) AS dt
	  FROM
	      caltrans
	  WHERE
	      (text LIKE '%CLOSED%') AND ((text LIKE '%DUE TO SNOW%') OR (text LIKE '%FOR THE WINTER%'))
	  GROUP BY highway,area,date
	 )date_closed
GROUP BY highway,area
ORDER BY pct_closed DESC
LIMIT 5;



-- METHOD 2








-- part2
-- One of the mistakes: There should be a intersection of inner-join and outer-join, which is self-join.







-- part3(a)

SELECT 
    l.trip_id AS trip_id,
	l.user_id AS user_id,
	IF(r.time IS NOT NULL,(TOSECOND(TIMEDIFF(r.time,l.time)),24*60*60) AS tripLength_in_second
FROM trip_starts l
INNER JOIN trip_ends r ON 
    l.trip_id = r.trip_id
LIMIT 5;



-- part3(b)

SELECT
    trip_id,
	user_id,
	0.15*CEILING(tripLength_in_second/60)+1 AS trip_charge
FROM (SELECT 
          l.trip_id AS trip_id,
		  l.user_id AS user_id,
		  IF(r.time IS NOT NULL,(TOSECOND(TIMEDIFF(r.time,l.time)),24*60*60) AS tripLength_in_second
      FROM trip_starts l
      LEFT JOIN trip_ends r ON 
          l.trip_id = r.trip_id)trip_length
LIMIT 5;



-- part3(c)

SELECT
    user_id,
	SUM(trip_charge) AS month_charge
FROM(SELECT
	    user_id,
	    0.15*CEILING(tripLength_in_second/60)+1 AS trip_charge
    FROM (SELECT
              user_id,
    		  tripLength_in_second
    	  FROM (SELECT
                    l.trip_id AS trip_id,
	    	        l.user_id AS user_id,
	    	        MONTH(l.time) AS month,
	    	        IF(r.time IS NOT NULL,(TOSECOND(TIMEDIFF(r.time,l.time)),24*60*60) AS tripLength_in_second
                FROM trip_starts l
                LEFT JOIN trip_ends r ON 
                    l.trip_id = r.trip_id)trip_length
          WHERE
              month=3)March_trip
		      )charge
GROUP BY user_id
LIMIT 5;


-- How much does user_id = 2 owe?
SELECT
    user_id,
	SUM(trip_charge) AS month_charge
FROM(SELECT
	    user_id,
	    0.15*CEILING(tripLength_in_second/60)+1 AS trip_charge
    FROM (SELECT
              user_id,
    		  tripLength_in_second
    	  FROM (SELECT
                    l.trip_id AS trip_id,
	    	        l.user_id AS user_id,
	    	        MONTH(l.time) AS month,
	    	        IF(r.time IS NOT NULL,(TOSECOND(TIMEDIFF(r.time,l.time)),24*60*60) AS tripLength_in_second
                FROM trip_starts l
                LEFT JOIN trip_ends r ON 
                    l.trip_id = r.trip_id)trip_length
          WHERE
              month=3)March_trip
		      )charge
GROUP BY user_id
HAVING user_id = 2;




-- part3(d)
    --    self-join

	


















