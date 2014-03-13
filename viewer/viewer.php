<!DOCTYPE html>
<html>
	<head>
		<title>Make An Adventure</title>
		<link rel="shortcut icon" href="/cyoa.ico">	

		<!--google fonts-->
		<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,300' rel='stylesheet' type='text/css'>	

		<!-- Bootstrap Framework -->
		<link rel="stylesheet" type="text/css" href="../../viewer/bootstrap.min.css">
		<link rel="stylesheet" type="text/css" href="../../viewer/bootstrap-responsive.min.css">
		<link rel="stylesheet" type="text/css" href="/common/CSS/viewer.css"></link>

		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
		<script src="/common/JS/underscore-min.js"></script>
		<script src="/common/JS/backbone-min.js"></script>
		<script src="/common/JS/views.js"></script>
		<script src="/common/JS/models.js"></script>
		<script src="../../viewer/viewer.js"></script>
		<!--loading data-->
		<script>
			<?php
				echo "var loadedBook = ".json_encode($properBook);
			?>
		</script>
	</head>
	<body>
		<div class="container">
			<div id="subPageHeader">
				<a id="backAPage" class="back_btn">&lt;&nbsp;previous page</a>
				<h1 class="subPageTitle">Choose Your Own Adventure</h1> 
			</div>
			<h1 id="bookTitle" class="title">Choose Your Own Adventure</h1>
			<div class="content span8 offset2">

				<div id="writingArea">
					<script type="text/template" id="page">
						<!--<h1 class="title"><%- title %></h1>-->
						<p>
							<%= content %>
						</p>
						<!--<div class="pageNumber">
							<%- id%>
						</div>-->
					</script>
				</div>

				<div id="nextChoices">
					<div class="innerBox">
						<script type="text/template" id="choiceTemplate">
							<div class="index option">
								<%- choiceDialog %>
							</div>
						</script>
					</div>
				</div>	
			</div>
		</div>
	</body>
</html>