<!DOCTYPE html>
<html>
	<head>
		<title>Make An Adventure</title>
		<link href='http://fonts.googleapis.com/css?family=Open+Sans+Condensed:300' rel='stylesheet' type='text/css'>

		<!-- Bootstrap Framework -->
		<link rel="stylesheet" type="text/css" href="../../viewer/bootstrap.min.css">
		<link rel="stylesheet" type="text/css" href="../../viewer/bootstrap-responsive.min.css">
		<link rel="stylesheet" type="text/css" href="/common/CSS/viewer.css"></link>

		
		<!--google fonts-->
		<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,300' rel='stylesheet' type='text/css'>	

		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
		<script src="/common/JS/underscore-min.js"></script>
		<script src="/common/JS/backbone-min.js"></script>
		<script src="/common/JS/models.js"></script>
		<script src="/createbook/creator.js"></script>
	</head>
	<body>
		<div class="container">
			<div id="subPageHeader">
				<a id="backAPage" class="back_btn">&lt;&nbsp;previous page</a>
				<div style="height:2em; text-align:center" id="readLink">
					(this book hasnt been saved yet -- no read link yet)
				</div>	
				<h1 class="subPageTitle">Choose Your Own Adventure</h1> 
			</div>

			<div class="content span8 offset2">
				<div id="writingArea">
					
					<script type="text/template" id="page">
					<input id="bookTitle" class="title" value="<%- title %>"></input>
							<textarea class="text storyText"><%- content %></textarea>
							<div style="display:none;" class="pageNumber">
								<%- id%>
							</div>
					</script>
				</div>

				<div id="nextChoices">
					<div class="innerBox">
						<script type="text/template" id="choiceTemplate">
							<input class="dialog" value="<%- choiceDialog %>"></input>
							<div class="create index"></div>
						</script>
					</div>
				</div>	
				<div id="controls">
					<div id="addChoice">Create New Chapter Choice Path</div>
					<!--<div id="removeChoice">-</div>-->
				</div>
			</div>	
		</div>
	</body>
</html>