<html>
	<head>
		<title>CYOA - Make An Adventure</title>
		<link rel="shortcut icon" href="/cyoa.ico">	

		<!--google fonts-->
		<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,300' rel='stylesheet' type='text/css'>	
		
		<!-- Bootstrap Framework -->
		<link rel="stylesheet" type="text/css" href="../../viewer/bootstrap.min.css">
		<link rel="stylesheet" type="text/css" href="../../viewer/bootstrap-responsive.min.css">
		<link rel="stylesheet" type="text/css" href="/common/CSS/viewer.css"></link>
		<link rel="stylesheet" type="text/css" href="../../frontpage/frontpage.css"></link>
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
		<!-- Not Ready For This
		<script src="../../frontpage/frontpage.js"></script> -->

	</head>
	<body>

		<!--FrontPage, does not need backbone to run (simple Javascript)-->

		<div class="container">
			<h1 id="bookTitle" class="title"><img src="frontpage/cyoa-icon.png" />Choose Your Own Adventure</h1>
			<div class="content span8 offset2">

				<div id="writingArea">
					
						<p>
							Bored and lonely, you find yourself surfing the internet. Site after site, image after image - you're just burning time, waiting for something to catch your attention ...
							<br><br>
							Suddenly, and I mean unexpectedly, you find yourself on, "www.ChooseYourOwnAdventure.in". A website that, despite it's simplistic looks, and noticeably warming charm, has something that most sites don't -- interactivity! Intrigued, and filled with an adventurous spirit, you feel like you should click on something ... 
							<br><br>
							What do you want to do on, "Choose Your Own Adventure"?
						</p>
					
				</div>

				<div id="library" style="display:none;">

					<center><h2>Available Adventures</h2></center>
					<a href="/read/23">
						<div class="book">
							<div class="image">
								<img src="../../frontpage/manwithorange.jpg"></img>
							</div>
							<div class="bookDetail">
								<h2><i>"Man With An Orange"</i></h2>
								<p>A story about a man, and his encounter with a simple, straight from the box, overly ordinary, nothing all that important - orange.</p>
							</div>
						</div>
					</a>
					<br><br>
					<a style="font-size:20px;" href="/create/">Psssst : This is the Alpha-Adventure Maker<br>(try it out before I change my mind)</a>
					<br>
				</div>
				<div id="nextChoices">
					<div class="innerBox">
						<div class="index option one">
							<a href="/frontpage/page2.html">Go on an adventure!</a>
						</div>
						<div class="index option two">
							<a href="/frontpage/page4.html">Sign up for Beta Access!</a>
						</div>
					</div>
				</div>	
			</div>
		</div>
	</body>
</html>