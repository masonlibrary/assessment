            
<?php
/* select locaName as location from location */
?>

                <div id="locationSelect" class="item ui-corner-all">
                    <h2 >Location <span id="locationComment" class="commentDiv" ></span></h2> <!-- classroom -->
                    <div  class="copyOptions hidden">
                        Same for all sections:<input type="checkbox" checked="checked" name="sameLocations" id="sameLocations" />     
                    </div>
                    <div class="selectBox">
                            <div id="locationSelectContainer" class="floatLeft">
                                <span class="courseInfo xxx location"></span><span class="xxx courseSection location"></span>
                                <select id="locationID" name="locationID" class="xxx location mustHave" title="You must select a location." >
                                        
                                        <option class="xxx location" value="" selected="selected"> &nbsp; &nbsp;Please select:</option>
                                        
                                        <?php
                                        echo "<p>before include</p>";
                                        include("control/connection.php");
                                        echo "<p>after include</p>";
                                        $query = "select locaID as ID, locaname as Name from location";
                                        echo "<p>$query</p>";
                                        $result = mysqli_query($dbc, $query) or die('crappy crustacean!- query issues.'.mysqli_error($dbc));
                                        if(!$result){echo "this is an outrage: ".mysqli_error($dbc)."\n";}

                                        while ( $row = mysqli_fetch_assoc( $result) )
                                        {
                                            $id=$row['ID'];
                                            $Name = $row['Name'];
                                            echo '<option class="xxx location" value="'.$id.'" >'.$Name.'</option>';
                                        }
                                        echo "<h3>$query</h3>";
                                        mysqli_free_result($result);
                                      //  mysqli_close($dbc);
                                        ?>
                                </select>
                            </div>
			</div>
                    
		</div>
