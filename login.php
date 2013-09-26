<?php
  require_once('control/connectionVars.php');
    // Start the session
    session_start();
 
    // Clear the error message
    $error_msg = "";
    // Clear dialog message
    $_SESSION['dialogText']='';
    $_SESSION['dialogTitle']='';

  // If the user isn't logged in, try to log them in
  if (!isset($_SESSION['userID'])) {
    if (isset($_POST['submit'])) {
      // Connect to the database
      $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

      // get form log-in data
      $user_username = mysqli_real_escape_string($dbc, trim($_POST['username']));
      $user_password = mysqli_real_escape_string($dbc, trim($_POST['password']));

      //if both fields are filled in...
      if (!empty($user_username) && !empty($user_password)) {
        // Look up the username and password in the database
        $query = "SELECT ".
                "u.userID as userID, ".
                "u.userName as userName, ".
                "r.roleID as roleID, ".
                "r.roleName as roleName, ".
                "ur.roleroleID as roleroleID, ".
                "ur.roleUserID as roleUserID ".
            "FROM users u, roles r, userroles ur ".
                "WHERE  u.userName = '$user_username' AND u.userPass = SHA('$user_password') ".
                "and ur.roleUserID=u.userID ".
                "and r.roleID=ur.roleroleID";
        
      //echo $query;
        $data = mysqli_query($dbc, $query);
        
        if (mysqli_num_rows($data) == 1) {
          // The log-in is OK so set the user ID and username session vars (and cookies), and redirect to the home page
          $row = mysqli_fetch_array($data);
          $_SESSION['userID'] = $row['userID'];
          $_SESSION['userName'] = $row['userName'];
          $_SESSION['roleID'] = $row['roleID'];
          $_SESSION['roleName']=$row['roleName'];
          setcookie('userID', $row['userID'], time() + (60 * 60 * 24 * 30));    // expires in 30 days
          setcookie('userName', $row['userName'], time() + (60 * 60 * 24 * 30));  // expires in 30 days
          setcookie('roleID', $row['roleID'], time() + (60 * 60 * 24 * 30));    // expires in 30 days
          setcookie('roleName', $row['roleName'], time() + (60 * 60 * 24 * 30));  // expires in 30 days
          //
          
          //$home_url = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/index.php';
         
        
          
        }
        else {
          // The username/password are incorrect so set an error message
          $error_msg = 'Sorry, you must enter a valid username and password to log in. <br />'; //.$query;
        }
      }
      else {
        // The username/password weren't entered so set an error message
        $error_msg = 'Sorry, you must enter your username and password to log in. <br />';//.$query;
      }
    }
  }

  // Insert the page header
 // $page_title = 'Log In';
 // require_once('includes/loginHeader.php');

  // If the session var is empty, show any error message and the log-in form; otherwise confirm the log-in
  if (empty($_SESSION['userID'])) {
    echo  $error_msg ;
?>
    <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <title>Mason Library Assessment::Login</title>
    <link rel="icon" type="image/png" href="images/favicon.png" /> 
    <link rel="stylesheet" href="css/login.css" type="text/css" />

    <meta name="robots" content="noindex" />
    <meta http-equiv="cache-control" content="no-cache" />
    <meta http-equiv="pragma" content="no-cache" />
    </head>
    <body id="loginBody">
    <div id="loginBox">
            <h1 id="logo"><a href="index.php">Assessment Database</a></h1>
            <h3 id="loginText">Assessment Database</h3>
            <h1>Authentication Required</h1>
            <br />
            <form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
        <table  >
            <tr><td width="100" align="right"><b>Username</b>:</td><td><input type="text" name="username" id="name" value="<?php if (!empty($user_username)) echo $user_username; ?>" /></td></tr>
            <tr><td align="right"><b>Password</b>:</td><td><input type="password" name="password" id="pass" /></td></tr>
            <tr><td>&nbsp;</td><td>&nbsp;&nbsp;<input class="submit" type="submit" name="submit" value="Login" /></td></tr>
        </table>
    </form>
    </div>
    <div id="copyRights">Copyright &copy; notice goes here</div>
    </body>
    </html>

   

<?php
  }
  else {
    // Confirm the successful log-in
    header('Location: index.php');
    echo('<p class="login">You are logged in as ' . $_SESSION['userName'] . '.</p>');
  }
?>

<?php
  // Insert the page footer
 // require_once('includes/footer.php');
?>