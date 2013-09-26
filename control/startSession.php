<?php

//pick up (or begin) session
session_start();

  // If the session vars aren't set, try to set them with a cookie
  if (!isset($_SESSION['userId'])) {
    if (isset($_COOKIE['userId']) && isset($_COOKIE['userName']) &&isset($_COOKIE['roleID'])   &&isset($_COOKIE['roleName'])) {
      $_SESSION['userId'] = $_COOKIE['userId'];
      $_SESSION['userName'] = $_COOKIE['userName'];
      $_SESSION['roleId'] = $_COOKIE['roleId'];
      $_SESSION['roleName'] = $_COOKIE['roleName'];
      //if not already extant then create the User-object
        if (!isset($_SESSION['thisUser']))
                   { 
                   $_SESSION['thisUser']= new User($_SESSION['userID'], $_SESSION['userName'], $_SESSION['roleName']);
                   
                   }
      
    }
  }
  
  

                
?>
