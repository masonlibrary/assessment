<?php 
/*
select p.ppleID as ID, p.ppleLName as LName, p.ppleFName as FName 
  from people p, librarianMap l 
 where p.ppleID=l.libmppleID and l.libmStatus='active';
 * 
 * $("#librarian option[value='Jennifer Ditkoff']").remove();
 */


?>

                                        <?php
                                   
                                        include("control/connection.php");
                                        
                                        
                                                $output='';
                                                $query = "select p.ppleID as ID, p.ppleFName as FName, p.ppleLName as LName, l.libmuserID as userID ".
                                                        "from people p, librarianmap l ".
                                                        "where p.ppleID=l.libmppleID and l.libmStatus='active'";
                                                $result = mysqli_query($dbc, $query) or die('dammit- query issues.');
                                                if(!$result){echo "this is an outrage: ".mysqli_error()."\n";}

                                                while ( $row = mysqli_fetch_assoc( $result) )
                                                {
                                                    $id= $row['ID'];
                                                    $librarianName = $row['FName'].' '.$row['LName'];
                                                    $resultUserID = $row['userID'];
                                                    if ($_SESSION['userID']==$resultUserID)
                                                            {
                                                            $selected=' selected="selected" ';
                                                            $powerName=$librarianName;
                                                            }
                                                    else{$selected='';}
                                                    $output.='<option '.$selected.' id="libm'.$id.'" value="'.$id.'">'.$librarianName.'</option>';
                                                    //echo '<option '.$selected.' id="libm'.$librarianName.'" value="'.$id.'">'.$librarianName.'</option>';
                                                }
                                            

                                        mysqli_free_result($result);
                                        mysqli_close($dbc);
                                        ?>

                <div  id="librarianSelect" class="item ui-corner-all complete">
                        <h2 id="librarianHeader">Librarian: <span id="librarianComment"><?php echo $powerName ?></span></h2>
			<div class="selectBox">
                            <div class="floatLeft">
                                <select id="librarianID" name="librarianID" class="mustHave" title="You must select a librarian." >
                                        <option value="" > &nbsp; &nbsp;Please select:</option>
                                        <?php echo $output; ?>
                                </select>
                            </div>
			</div>
			
			
                 </div>
