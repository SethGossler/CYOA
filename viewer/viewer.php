<!DOCTYPE html>
<html>
	<head>
		<title>CYOA: Choose Your Adventure</title>
		<link rel="shortcut icon" href="/cyoa.ico">	

		<!--google fonts-->
		<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,300' rel='stylesheet' type='text/css'>	

		<!-- Bootstrap Framework -->
		<link rel="stylesheet" type="text/css" href="../viewer/bootstrap.min.css">
		<link rel="stylesheet" type="text/css" href="../viewer/bootstrap-responsive.min.css">
		<link rel="stylesheet" type="text/css" href="../common/CSS/viewer.css"></link>

		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
		<script src="../common/JS/underscore-min.js"></script>
		<script src="../common/JS/backbone-min.js"></script>
		<script src="../common/JS/views.js"></script>
		<script src="../common/JS/models.js"></script>
		<script src="../viewer/viewer.js"></script>
		<!--loading data-->
		<script>
			<?php echo "var loadedBook = ".json_encode($properBook); ?>

			<?php echo "var loadedPages = ".json_encode($properBook["pages"]); ?>
		</script>
	</head>
	<body>
		<div class="container">

			<script type="text/template" id="viewer_tpl">
				<div id="subPageHeader">
					<a id="backAPage" class="back_btn">&lt;&nbsp;previous page</a>
					<h1 class="subPageTitle"><%= title  %></h1> 
				</div>
				<h1 id="bookTitle" class="title"><%= pageTitle  %></h1>
				<div class="content span8 offset2">

					<div id="writingArea">
						<p>
							<%= content %>
						</p>
						<!--<div class="pageNumber">
						</div>-->
					</div>

					<div id="nextChoices">
						<% _.each(choices, function(choice){ %>

							<div data-id="<%= choice.id %>" class="innerBox">
								<div data-id="<%= choice.id %>" class="index option">
									<%- choice.title %>
								</div>
							</div>

						<% }); %>
					</div>	
				</div>
			</script>

		</div>
	</body>
</html>