<?php 
/*
select p.ppleID as ID, p.ppleLName as LName, p.ppleFName as FName 
  from people p, librarianMap l 
 where p.ppleID=l.libmppleID and l.libmStatus='active';
 * 
 * $("#librarian option[value='Jennifer Ditkoff']").remove();
 */


?>
<div  id="librarianSelect" class="item ui-corner-all">
		<h2 id="librarianHeader">Librarian: <span id="librarianComment"></span></h2>
			<div class="selectBox">
                            <div class="floatLeft">
                                <select id="librarianID" name="librarianID" class="mustHave" title="You must select a librarian." >
                                        <option value="" selected="selected"> &nbsp; &nbsp;Please select:</option>
                                        <?php
                                        
                                        include("control/connection.php");
                                        
                                        
                                           
                                                $query = "select p.ppleID as ID, p.ppleFName as FName, p.ppleLName as LName from people p, librarianmap l ".
                                                        "where p.ppleID=l.libmppleID and l.libmStatus='active'";
                                                $result = mysqli_query($dbc, $query) or die('dammit- query issues.');
                                                if(!$result){echo "this is an outrage: ".mysqli_error()."\n";}

                                                while ( $row = mysqli_fetch_assoc( $result) )
                                                {
                                                    $id= $row['ID'];
                                                    $librarianName = $row['FName'].' '.$row['LName'];
                                                    echo '<option id="libm'.$librarianName.'" value="'.$id.'">'.$librarianName.'</option>';
                                                }
                                            

                                        mysqli_free_result($result);
                                        mysqli_close($dbc);
                                        ?>
                                </select>
                            </div>
			</div>
			<!--<div id="librarianComment" class="floatLeft commentDiv"></div>-->
			
                 </div>
