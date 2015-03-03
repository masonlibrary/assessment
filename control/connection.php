<?php
require_once('connectionVars.php');
$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME)
                    or die('Error connecting to the stupid database');

mysqli_query($dbc, 'set @@session.time_zone = "America/New_York"') or die('Failed to set SQL session timezone: ' . mysqli_error($dbc));

?>