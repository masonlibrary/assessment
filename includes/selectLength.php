
                <div id="lengthSelect" class="item ui-corner-all">
                    <h2>Session Length<span id="lengthComment" class="commentDiv"></span></h2> 
                <div  class="copyOptions hidden">
                        Same for all sections:<input type="checkbox" checked="checked" name="sameLengths" id="sameLengths" />     
                    </div>
		<div class="selectBox">
                            <div id="lengthSelectContainer" class="floatLeft">
                               <span class="courseInfo xxx length"></span><span class="xxx courseSection length"></span>
                                <select id="lengthID" name="lengthID" class="mustHave xxx length" title="You must select a session length." >
                                        <option value="" selected="selected"> &nbsp; &nbsp;Please select:</option>
                                        <?php
                                        
                                        include("control/connection.php");
                                        $query = "select seslID as ID, seslName as Name from sesslength";
                                        $result = mysqli_query($dbc, $query) or die('Victoria! I know your secret!- query issues.'.mysqli_error($dbc));
                                        if(!$result){echo "this is an outrage: ".mysqli_error($dbc)."\n";}

                                        while ( $row = mysqli_fetch_assoc( $result) )
                                        {
                                            $id=$row['ID'];
                                            $Name = $row['Name'];
                                            echo '<option id="sesl'.$id.'" value="'.$id.'" >'.$Name.'</option>';
                                        }

                                        mysqli_free_result($result);
                                        mysqli_close($dbc);
                                        ?>
                                </select>
                            </div>
			</div>
		</div>
