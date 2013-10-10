                <div  id="resourcesSelect" class="item ui-corner-all">
                    <h2>Resources introduced<span id="resourcesComment" class="commentDiv"></span></h2>
                    <div  class="copyOptions hidden">
                        Same for all sections:<input type="checkbox" checked="checked" name="sameResources" id="sameResources" />    
                    </div>
                    <div class="selectBox">
                            <div id="resourcesSelectContainer" class="floatLeft">
                                <span class="courseInfo xxx resourcesBox"></span><span class="xxx courseSection resourcesBox"></span><br />
                                        <?php
                                        
                                        include("control/connection.php");
                                        $query = "select rsrpID as ID, rsrpName as Name from resourcepool order by Name asc";
                                        $result = mysqli_query($dbc, $query) or die('Shite!- query issues.'.mysqli_error($dbc));
                                        if(!$result){echo "this is an outrage: ".mysqli_error($dbc)."\n";}
                                        echo '<input class="xxx resourcesBox none mustHaveBox" title="You must choose at least 1 resource (or specify NONE) per session" type="checkbox" name="resourcesIntroduced" value="none"  /><span class="xxx resourcesbox">None</span><br class="xxx resourcesbox" />';  
                                        while ( $row = mysqli_fetch_assoc( $result) )
                                        {
                                           
                                            $id=$row['ID'];
                                            $Name = $row['Name'];
                                            echo '<input class="xxx resourcesBox mustHaveBox notNone" title="You must choose at least 1 resource per session" type="checkbox" name="resourcesIntroduced[]" value="'.$id.'"  /><span class="xxx resourcesbox">'.$Name.'</span><br class="xxx resourcesbox" />';
                                        }

                                        mysqli_free_result($result);
                                        mysqli_close($dbc);
                                        ?>
                               
                            </div>
			</div>
		</div>