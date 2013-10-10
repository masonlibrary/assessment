                <div id="courseSelect" class="item">
                    <h2 id="courseSelectHeader">Course ID-Selection </h2>
                        <div id="makeCopiesDiv"><input type="checkbox" name="makeCopies" id="makeCopies" /> <span class="askCopy">Create</span>
                            <div id="numberOfCopiesDiv">
                            <select id="numberOfCopies" class="hidden" name="numberOfCopies">
                                <option value="1" selected="selected">1</option>
                                <option value="2" >2</option>
                                <option value="3" >3</option>
                                <option value="4" >4</option>
                                <option value="5" >5</option>
                            </select>
                            </div> <span class="askCopy"> additional section&lpar;s&rpar;: </span>
                        
                        </div>
			<div class="coursePrefixColumn">
				<h4>Course Prefix</h4>
				<div id="selectBox">
                                <div id="coursePrefixSelectContainer" class="floatLeft">
                                    <select id="coursePrefixID" name="coursePrefixID" class="mustHave" title="You must select a course prefix.">
                                            <option value="" selected="selected">&nbsp;</option>
                                            <?php

                                            include("control/connection.php");
                                            $query = "select crspID as ID, crspName as Name from courseprefix";
                                            $result = mysqli_query($dbc, $query) or die('Mr. Christian!- query issues.'.mysqli_error($dbc));
                                            if(!$result){echo "this is an outrage: ".mysqli_error($dbc)."\n";}

                                            while ( $row = mysqli_fetch_assoc( $result) )
                                            {
                                                $id=$row['ID'];
                                                $Name =  trim($row['Name']);
                                                echo '<option id="crsp'.$id.'" value="'.$id.'">'.$Name.'</option>';
                                            }

                                            mysqli_free_result($result);
//                                            mysqli_close($dbc);
                                            ?>
                                    </select>
                            </div>
			</div>
			</div>
			<div class="courseNumberColumn">
				<h4>Number</h4> <div id="courseNumberContainer"><input id="courseNumber" name="courseNumber" type="text" size="15" class="mustHave" title="You must have a course number." /></div>
			</div>
			<div class="courseSectionColumn">
				<h4>Section</h4> <div id="courseSectionContainer"><input id="courseSection" name="courseSection" type="text" size="10" class="mustHave" title="You must provide a section number." /></div>
			</div>
			<div class="courseTitleColumn">
				<h4>Title</h4> <div id="courseTitleContainer"><input id="courseTitle" name="courseTitle" type="text" size="15" class="mustHave" title="You must have a course title." /></div>
			</div>
                        <div class="courseSessionColumn">
                            <h4>Session #</h4> 
                            <div id="sessionNumberContainer">
                            <select  name="sessionNumber" id="sessionNumber" class="mustHave" title="You must select a session #." >
                                <option selected="selected" value="I">I</option><option value="II">II</option><option value="III">III</option><option value="Visit">Visit</option><option value="Other">Other</option></select>
                            </div>
                        </div>
			<br />
		</div>
