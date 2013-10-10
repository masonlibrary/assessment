<?php 
/*
select p.ppleID as ID, p.ppleLName as LName, p.ppleFName as FName 
  from people p, librarianMap l 
 where p.ppleID=l.libmppleID and l.libmStatus='active';
 * 
 * $("#librarian option[value='Jennifer Ditkoff']").remove();
 */
include("control/connection.php");
    $userID=$_SESSION['userID'];
  $query = "select p.ppleID as ID, p.ppleFName as FName, p.ppleLName as LName from people p, librarianmap l, users u ".
              "where p.ppleID=l.libmppleID and l.libmStatus='active' and $userID=l.libmuserID";
   $result = mysqli_query($dbc, $query) or die('dammit- query issues.<br />'.$query);
        if(!$result){echo "this is an outrage: ".mysqli_error($dbc)."\n";}

        while ( $row = mysqli_fetch_assoc( $result) )
        {
            $id= $row['ID'];
            $librarianName = $row['FName'].' '.$row['LName'];
            $_SESSION['librarianID']=$id;
        }


mysqli_free_result($result);
mysqli_close($dbc);

    
?>
<div  id="librarianSelect" class="item complete">
		<h2>Librarian: <span id="librarianComment"> <?php echo $librarianName ?></span></h2>
			<div class="selectBox">
                            <div class="floatLeft">
                                <input id="librarianID" class="mustHave" title="You must have a librarian" type="hidden" name="librarianID" value="<?php echo $id ?>" />
                            </div>
                            
                        </div>
</div>
