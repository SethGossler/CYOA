<html>
	<head>
		<title>CYOA - Create Account</title>
		<link rel="shortcut icon" href="/cyoa.ico">	

		<!--google fonts-->
		<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,300' rel='stylesheet' type='text/css'>	
		
		<!-- Bootstrap Framework -->
		<link rel="stylesheet" type="text/css" href="../viewer/bootstrap.min.css">
		<link rel="stylesheet" type="text/css" href="../viewer/bootstrap-responsive.min.css">
		<link rel="stylesheet" type="text/css" href="../common/CSS/viewer.css"></link>
		<link rel="stylesheet" type="text/css" href="../management/user/user.css"></link>

		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>

	</head>
	<body>
		<div class="container login">
			<div class="form">
				<h1>Create Account</h1>
				<form method="post" action="create">
					<label>Name (First and Last)</label>
					<input name="actual"/>
					<label>Email</label>
					<input name="email"/>
					<label>Pen Name</label>
					<input name="name"/>
					<label>Password</label>
					<input name="password"/>
					<input type="submit" value="create!"></input>
				</form>	
			</div>
		</div>
	</body>
</html>