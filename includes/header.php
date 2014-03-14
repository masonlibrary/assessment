<?php
	// For all pages, make sure the user is logged in before going any further.
	if (!isset($_SESSION['userID'])) { header("Location: login.php"); exit('Not logged in'); }

	// Initialize for inclusion of JavaScript snippets, will be included in
	// footer after loading of all JS libraries
	$jsOutput = '';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<?php
  echo '<title>' . $page_title . ' - Assessment</title>';
?>
  <link rel="icon" type="image/png" href="images/favicon.png" />

  <link rel="stylesheet" type="text/css" href="css/jquery-ui.css" />
  <link rel="stylesheet" type="text/css" href="css/tabzilla.css" />
    <link rel="stylesheet" type="text/css" href="css/assessment.css" />
    <link rel="stylesheet" type="text/css" href="css/dataTables.css" />
    <link rel="stylesheet" type="text/css" href="css/TableTools.css" />
    <!--<link rel="stylesheet" type="text/css" href="css/dataTables_themeroller.css" />-->
	<script type="text/javascript" src="js/jquery.js"></script>
</head>
<body>

    <div id="header">
        <div class="headerContainer">
       <div class="logo-block">
		<a href="http://www.keene.edu"><img src="images/KSC-wordmark-150px.png" alt="Keene State College" /></a>
		<div id="logoTitleDiv">
			<h2 id="djcWEMlib" ><a href="http://keene.edu/academics/library/">Wallace E. Mason Library</a></h2>
			<?php
				// Check to see if we're local or the path to this script starts with this:
				// Same check is used in control/connectionVars.php
				$practiceprefix='/assessment/practice/assessment';
				if ($_SERVER['HTTP_HOST'] == 'localhost' || (strncmp($_SERVER['SCRIPT_NAME'], $practiceprefix, strlen($practiceprefix)) == 0)) {
					echo '<h2 id="assessmentTitle" ><a style="color:#2C2;" href="index.php">** Development Copy **</a></h2>';
				} else {
					echo '<h2 id="assessmentTitle" ><a href="index.php">Information Literacy Assessment</a></h2>';
				}
			?>
		</div></div> </div>
<?php
 // echo '<div id="pageTitleDiv"><h3>Assessment - ' . $page_title . '</h3></div>';
  echo '<div id="loginDiv">';
  /*IF Logged in then tell the user and offer logout */ ?>
        <h3 id="title" title="Current Location:"><?php  echo $page_title; ?></h3>
        <?PHP
   if (isset($_SESSION['userID']))
       {
       echo '<div id="loginLine" class="login">You are logged in as <strong>' . $_SESSION['userName'] . '</strong>.</div>';
       echo '<div id="userlinks"><a href="userEdit.php">Account</a> &bull; <a href="logout.php">Logout</a></div>';
       }
?>
    <br />
    </div><!--loginDiv-->
    <!-- <a href="http://www.mozilla.org/" id="tabzilla">mozilla</a> -->

    <?php
       // include('topNavigation.php');
       // if($page_title!='Home'){include('assessmentMenu.php');} ?>


        <div id="slider" class="closed"><?PHP include('includes/tabzillaMenu1.php') ?></div> <a href="#" id="tabzilla">mozilla</a>
    </div><!-- header-->
    <div id="content">

		<?php
			if (isset($_SESSION['dialogTitle']) || isset($_SESSION['dialogText'])) {
				// Ensure we don't have an undefined var
				$_SESSION['dialogTitle'] .= '';
				$_SESSION['dialogText'] .= '';
				echo '<div id="messagebox" class=""><strong>'.$_SESSION['dialogTitle'].'</strong><br/>'.$_SESSION['dialogText'].'</div>';
				unset($_SESSION['dialogTitle']);
				unset($_SESSION['dialogText']);
			}
		?>
