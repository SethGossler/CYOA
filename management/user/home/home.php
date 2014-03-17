<!DOCTYPE html>
<html>
	<head>
		<title>Find An Adventure</title>
		<link rel="shortcut icon" href="/cyoa.ico">	

		<!--google fonts-->
		<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,300' rel='stylesheet' type='text/css'>	

		<!-- Bootstrap Framework -->
		<link rel="stylesheet" type="text/css" href="../viewer/bootstrap.min.css">
		<link rel="stylesheet" type="text/css" href="../viewer/bootstrap-responsive.min.css">
		<link rel="stylesheet" type="text/css" href="../common/CSS/viewer.css"></link>

		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
		<!--loading data-->
		<script>

			<?php echo "var books = ".json_encode($publicBooks); ?>

		</script>
	</head>
	<body>
		<div class="container library">
			<center><h1>Pick An Adventure!</h1></center>
			<?php foreach ($publicBooks as $key => $book) {?>
				<a href="../readID/<?php echo $book['ID'];?>" class="bookListing">
					<span><?php echo $book['title']; ?></span>
				</a>
			<?php } ?>
		</div>
	</body>
</html>