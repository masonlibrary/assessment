<?php
  require_once('control/connectionVars.php');
    // Start the session
    session_start();
 
    // Clear the error message
    $error_msg = "";
    // Clear dialog message
    $_SESSION['dialogText']='';
    $_SESSION['dialogTitle']='';

	// If the user is already logged in, log them out (will redirect back here)
	if (isset($_SESSION['userID'])) {
		include('logout.php');
	}

  // If the user isn't logged in, try to log them in
	if (!isset($_SESSION['userID']) && $_POST) {
		// Connect to the database
		$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

		// get form log-in data
		$user_username = $_POST['username'];
		$user_password = sha1($_POST['password']);

		// Look up the username and password in the database
		$query = 'select
				u.userID as userID,
				u.userName as userName,
				r.roleID as roleID,
				r.roleName as roleName
			from users u, roles r, userroles ur
			where u.userName=? and
				u.userPass=? and
				ur.roleUserID=u.userID and
				r.roleID=ur.roleroleID';

		$row=array();
		$stmt = mysqli_prepare($dbc, $query);
                //original way is actually an alias
		//mysqli_bind_param($stmt, 'ss', $user_username, $user_password);
                //better way to test.... as of 09-14-2015
                mysqli_stmt_bind_param($stmt, 'ss', $user_username, $user_password);
		mysqli_stmt_execute($stmt) or die('Failed to look up user: ' . mysqli_error($dbc));
		mysqli_stmt_store_result($stmt);
		mysqli_stmt_bind_result($stmt, $row['userID'], $row['userName'], $row['roleID'], $row['roleName']);
		mysqli_stmt_fetch($stmt);

		if (mysqli_stmt_num_rows($stmt) == 1) {
			
			// The log-in is OK so set the user ID and username session vars (and cookies), and redirect to the home page
			$_SESSION['userID'] = $row['userID'];
			$_SESSION['userName'] = $row['userName'];
			$_SESSION['roleID'] = $row['roleID'];
			$_SESSION['roleName'] = $row['roleName'];
			$timeout = time() + (60*60*24*30); // expires in 30 days
			setcookie('userID', $row['userID'], $timeout);
			setcookie('userName', $row['userName'], $timeout);
			setcookie('roleID', $row['roleID'], $timeout);
			setcookie('roleName', $row['roleName'], $timeout);
			
			header('Location: index.php');
			
		} else {
			// The username/password were invalid so set an error message
			$error_msg = 'Sorry, you must enter a valid username and password to log in. <br />';//.$query;
		}
	}

?>
    <!DOCTYPE html>
    <head>
			<title>Mason Library Assessment::Login</title>
			<link rel="icon" type="image/png" href="images/favicon.png" /> 
			<link rel="stylesheet" href="css/login.css" type="text/css" />
			<meta http-equiv="content-type" content="text/html; charset=utf-8" />
			<meta name="robots" content="noindex" />
    </head>
    <body id="loginBody">
		<?php
		  // Show any error message
			if (isset($error_msg)) { echo "<div>$error_msg</div>"; }
		?>
    <div id="loginBox">
			<h1 id="logo"><a href="index.php">Assessment Database</a></h1>
			<h3 id="loginText">Assessment Database</h3>
			<h1>Authentication Required</h1>
			<br />
			<form method="post">
        <table>
					<tr>
						<th><label for="name">Username</label></th><td><input type="text" name="username" id="name" autofocus value="<?php if (isset($user_username)) echo $user_username; ?>" /></td>
					</tr>
					<tr>
						<th><label for="pass">Password</label></th><td><input type="password" name="password" id="pass" /></td>
					</tr>
        </table>
				<input class="submit" type="submit" name="submit" value="Login" />
			</form>
    </div>
    <div id="copyRights"><?php echo 'Copyright &copy; 2013-' . date('Y') . ' Keene State College'; ?></div>
    </body>
    </html>

<?php
  // Insert the page footer
 // require_once('includes/footer.php');
?>