<!DOCTYPE html>
<html>
	<head>
		<title>CYOA: Make An Adventure</title>
		<link href='http://fonts.googleapis.com/css?family=Open+Sans+Condensed:300' rel='stylesheet' type='text/css'>

		<!-- Bootstrap Framework -->
		<link rel="stylesheet" type="text/css" href="viewer/bootstrap.min.css">
		<link rel="stylesheet" type="text/css" href="viewer/bootstrap-responsive.min.css">
		<link rel="stylesheet" type="text/css" href="common/CSS/viewer.css"></link>

		<!--google fonts-->
		<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,300' rel='stylesheet' type='text/css'>	

		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
		<script src="common/JS/underscore-min.js"></script>
		<script src="common/JS/backbone-min.js"></script>
	</head>
	<body>
		<div class="container">
			<script type="text/template" id="page_tpl">
				<div id="subPageHeader">
					<a id="backAPage" target="_blank" class="back_btn">&lt;&nbsp;previous page</a>
					<div style="height:2em; text-align:center" id="readLink">
						<% if(id){ %><a href="readID/<%= id %>">Link!</a><% } %>
					</div>	
					<input id="bookTitle" class="value subPageTitle" value="<%= title %>"></input> 
				</div>

				<div class="content span8 offset2">
					<div id="writingArea">
						
						<input id="pageTitle" class="title" value="<%= pageTitle %>"></input>
						<textarea class="text storyText"><%= content %></textarea>

					</div>

					<div id="nextChoices">

						<% _.each(choices, function(choice){ %>
							<div class="innerBox">
								<input data-id="<%= choice.id %>" class="dialog" value="<%= choice.title %>"></input>
								<div data-id="<%= choice.id %>" class="create index"></div>	
							</div>
						<% }); %>

					</div>	
					<div id="controls">
						<div id="addChoice">Create New Chapter Choice Path</div>
					</div>
				</div>	
			</script>
		</div>
		<script src="createbook/creator.js"></script>
	</body>
</html>