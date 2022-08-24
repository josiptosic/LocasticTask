<?php session_start(); ?>
<!doctype html>
<html>
	<head>
		<meta charset="utf-8">
		<title> Edit form </title>
	</head>
	
	<?php 	
			$_SESSION['editPlacement'] = $_POST['editPlacement'];
			$_SESSION['editRaceObj'] = $_POST['editRaceObj'];
			$_SESSION['editDistance'] = $_POST['editDistance'];
			$procPlacement = $_SESSION['editPlacement']; 
	?>
	
	<body>
		<form action = "ResultsView.php" method = "post">
			<p>
				<hr>Full name: <input type = "text" name = "editFullName">
				Finish time: <input type = "text" name = "editRaceTime">
				<input type = "submit">
				<hr>
			</p>	
		</form>
	
	</body>
</html>