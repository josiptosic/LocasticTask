<?php
	session_start();
?>
<!doctype html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Import form</title>
	</head>
	<body>
		<form action = "ResultsView.php" method = "post">
			<p>
				<hr>Race name: <input type= "text" name="raceName">
				Race date: <input type= "text" name="raceDate">
				CSV file: <input type= "file" name = "fileName" >
				<input type = "submit">
				<hr>
			</p>	
		</form>
	</body>
</html>