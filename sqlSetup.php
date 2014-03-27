<?php
// connect to the server
$conn = new mysqli('localhost', 'root', '3Bl1ndM1c3', 'cyoaapp');
if (mysqli_connect_errno()) {
	echo 'MYSQLI error';
}

$query = "SELECT *
		  FROM Books;"

//print_r(error_get_last());
//$conn->query($query);


?>