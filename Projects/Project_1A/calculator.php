<!DOCTYPE html>
<html>

<head>
<title>Calculator</title>
</head>

<body>

<h1>Calculator</h1>

<p> Type an expression in the following box (e.g. 5+7*0.5)</p>
<form method = "GET" action = "<?php echo $_SERVER['PHP_SELF'];?>">
	Expression : <input type = "text" name = "expr">
	<input type = "submit" value="Calculate">
</form>

<ul>
	<li> Only numbers and +, -, * and / operatirs are allowed in the expression.</li>	
	<li> The evaluation follows the standard operator precedence. </li>
	<li> The calculator does not support parentheses.</li>
	<li> The calculator handles invalid input "gracefully". It does not output PHP error messages.</li>
</ul>

<h2>Result</h2>


<?php

	if ($_SERVER["REQUEST_METHOD"] == "GET"){ // collect value of input field
		$expr = htmlspecialchars($_REQUEST['expr']);
	if (empty($expr)){
		echo "Expression is empty.";
	} else {
				$expr = str_replace(' ', '', $expr); // remove all spaces
				$expr = str_replace('--', '+', $expr); // negative negative -> positive
				$match_sig = preg_match('/((-?\d+\.?\d*)[\+\-\/\*])*(-?\d+\.?\d*)/', $expr, $matches); //true if can be parsed
				if($matches[0] != $expr)
				{
					$match_sig = 0;
				} 
				//$match_sig = pre_match('/（-?\d+\.?\d+[-+*\/])-?\d+\.?\d*/', $expr);
				$divZero_at_end_sig = preg_match('/\/0$/', $expr); // true if division zero
				$divZero_at_mid_sig = preg_match('/\/0[\+\-\/\*]+/', $expr); // true if division zero
				$LeadZero_before_num_sig = preg_match('/^0[0-9]|[+\-*\/]0[0-9]/', $expr); //true if have like 02+003
				if ($match_sig){
					if($divZero_at_mid_sig | $divZero_at_end_sig)
						echo "Division by zero error!";
				 	elseif ($LeadZero_before_num_sig) 
				 		echo "Invalid Expression! Please enter non-leading-zero operands.";
				 		 else{
				 		 	echo $expr." = ";
				 		 	eval("echo $expr;");
				 		 }

							
				 }else 
					echo "Invalid Expression!";
	  	 	}
	

	}

?>

</body>
</html>