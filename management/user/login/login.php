<html>
<head>
	<title>Login Account</title>
</head>
<body>
	<a href="#cs">login account</a>
	<form method="post" action="/user/login/">
		<input name="name">Name</input>
		<input name="password">Password</input>
		<input type="submit" value="login!"></input>
	</form>

	<br>
	<a href="#cs">make account</a>
	<form method="post" action="/user/create/">
		<input name="actual">Your Real Name</input>
		<input name="email">Your Email Plz</input>
		<input name="name">Name</input>
		<input name="password">Password</input>
		<input type="submit" value="create!"></input>
	</form>	
</body>
</html>