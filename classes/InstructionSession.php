<?php


class InstructionSession {

    //SessionDesc variables
    private $sessionID=0;
    private $user='';
    private $librarianID = 0;
    private $dateOfSession = '';

    private $lengthOfSessionID =0;
    private $numberOfStudents=0;
    private $coursePrefixID=0;
    private $courseNumber=0;
    private $courseSection=0;
    private $courseTitle='';
    private $sessionNumber='';
    private $faculty='';
    private $locationID = 0;
    private $sesdCopyID=0;
    private $outcomeDone='no';
    private $assessed='no';


    private $outcomesTaught = array();
    private $outcomesAssessed=array();
    //session data for other db entities
    private $resourcesIntroducedID = array();
    private $sessionNote='';

    //sessionDesc variable for later use
    private $facultyID=0;

    //variables for later use...
    private $librarianName='not set';
    private $locationName='not set';
    private $coursePrefix='not set';
    private $lengthOfSessionName='not set';
    private $resourcesIntroducedName=array();



             // Declare a public constructor

    public function setOutcomesTaught($inArray)
        {
         $this->outcomesTaught=$inArray;
        }
    public function insertOutcomesTaught()
        {
        $success='query not completed.';
        $query = $this->getOutcomesTaughtInsertQuery();

        $dbc=$this->getConnection();
        $result=mysqli_query($dbc, $query);
        if(!$result){$success.='outcomes taught insert failed: <br /> Error: '.mysqli_error($dbc).'<br />Query: -->'.$query.'<-- <br />';}
        else
            {
                $success='Session insert success! <br />';

                $query="update sessiondesc set sesdOutcomeDone='yes' where sesdID=".$this->sessionID;
                $this->outcomeDone='yes';
                $result=mysqli_query($dbc, $query);
                if(!$result){$success.='sesdOutcomeDone update failed: <br /> Error: '.mysqli_error($dbc).'<br />Query: '.$query.'<br />';}
                else {$success.='sesdOutcomeDone update success! <br />';}
             }


        $this->closeConnection($dbc);
        return $success;
        }

    public function getOutcomesTaughtInsertQuery()
        {
        $query ="insert into outcomestaught (otctsesdID, otctotcdID) values ";

        $x= count($this->outcomesTaught);

        for ($i=0; $i<$x; $i++)
            {
            $query.="(".$this->sessionID.", ".$this->outcomesTaught[$i]."),";
            }

        $query = rtrim($query, ',');


        return $query;
        }
    public function setAndInsertOutcomesTaught($inArray)
        {
            $success='';
            $this->setOutcomesTaught($inArray);
            $success=$this->insertOutcomesTaught();
            return $success;
        }

    public function setAndInsertOutcomesAssessed($inArray)
        {
            $this->outcomesAssessed=$inArray;
            $success="Complete and utter failure...";
            $query="insert into outcomesassessed (otcaotctID, otcaMet, otcaPartial, otcaNotMet, otcaNotAssessed) values ";


            foreach($inArray as $row)
                {
                if ($row['NotAssessed']=='0')
                    {
                    $otctID=$row['otctID'];
                    $Met=$row['Met'];
                    $Partial=$row['Partial'];
                    $NotMet=$row['NotMet'];
                    $NotAssessed=$row['NotAssessed'];
                    $query.="($otctID, $Met, $Partial, $NotMet, $NotAssessed),";
                    }
                 else
                     {
                      $otctID=$row['otctID'];
                    $Met='0';
                    $Partial='0';
                    $NotMet='0';
                    $NotAssessed=$row['NotAssessed'];
                    $query.="($otctID, $Met, $Partial, $NotMet, $NotAssessed),";
                     }
                }

                $query=trim($query, ",");
                $dbc=$this->getConnection();
        $result=mysqli_query($dbc, $query);
        if(!$result){$success.='outcomes assessed insert failed: <br /> Error: '.mysqli_error($dbc).'<br />Query: -->'.$query.'<-- <br />';}
        else
            {
                $success='Outcomes assessed. Success! <br />';

                $query="update sessiondesc set sesdAssessed='yes' where sesdID=".$this->sessionID;
                $this->assessed='yes';
                $result=mysqli_query($dbc, $query);
                if(!$result){$success.='sesdAssessed update failed: <br /> Error: '.mysqli_error($dbc).'<br />Query: '.$query.'<br />';}
                else {$success.='Session marked as assessed. Success! <br />';}
             }


        $this->closeConnection($dbc);
        return $success;


        }

    public function getOutcomesToAssess()
        {
            $query = "select ".
                    "ot.otctID as taughtID, ".
                    "oh.otchID as headingID, ".
                    "oh.otchName as headingName, ".
                    "otpm.otcmsubhName as subheadingName, ".
                    "ot.otctotcdID as outcomeID, ".
                    "od.otcdName as outcomeName ".
                    "from ".
                    "outcometoprefixmap otpm, ".
                    "outcomestaught ot, ".
                    "outcomeheading oh, ".
                    "outcomedetail od ".
                    "where ".
                    "ot.otctsesdID=".$this->sessionID." ".
                    "and otpm.otcmotchID=oh.otchID ".
                    "and otpm.otcmcrspID=$this->coursePrefixID ".
                    "and od.otcdotchID=oh.otchID ".
                    "and od.otcdID = ot.otctotcdID ".
                    //"group by headingName ".
                    "order by oh.otchID, od.otcdID";



                $currentOutcomeHeading='first';
                //$output='<div class="test">'.$query.'</div>';
                $output='';

                $dbc=$this->getConnection();
                $result = mysqli_query($dbc, $query) or die('Oh nonono! Whyyyy??- query issues. <br /><h4>'.$query.'</h4');
                        if(!$result){echo "this is an outrage: ".mysqli_error($dbc)."\n";}

                    $assessedCount=0;
                    $output.='<div class="assessmentDiv">';
                    $output.='<h4 id="courseIdent">'.$this->coursePrefix.' '.$this->courseNumber.'-'.$this->courseSection.' '.$this->courseTitle.'</h4>';
                    $output.='<div id="courseSummary" class="hidden">'.$this->toString().'</div>';
                    $output.= '<form action="submitAssessment.php" method="post">';
                    while ( $row = mysqli_fetch_assoc( $result) )
                    {
                        $assessedCount++;
                        $taughtID=$row['taughtID'];
                        $headingID= $row['headingID'];
                        $headingName=$row['headingName'];
                        $subheadingName=$row['subheadingName'];
                        $outcomeID = $row['outcomeID'];
                        $outcomeName = $row['outcomeName'];

                        if ($headingName!=$currentOutcomeHeading)
                            {
                                if($currentOutcomeHeading!='first'){$output.='</table>';}
                                $currentOutcomeHeading = $headingName;
                                $output.= '<h4 class="xxx assessmentHeading">'.$headingName.'</h4>';
                                if ($subheadingName ==''){$output.= '<h5 class="outcomesBox outcomeSubheading">'.$subheadingName.'</h5>';}
                                else {$output.='<h5 class="outcomesBox outcomeSubheading">'.$this->coursePrefix.': '.$subheadingName.'</h5>';}

                                $output.='<table id="headingID'.$headingID.'">'.
                                        '<thead><tr><th>Outcome</th><th>Met outcome</th><th>Partially met outcome</th><th>Did not meet outcome</th></tr>';

                                $currentOutcomeHeading=$headingName;
                            }

                    $output.='<tr><td>'.$headingID.$outcomeName.'<input type="hidden" name="otctIDS[]" value="'.$taughtID.'" /></td>';
                    $output.='<td class="assessmentInput"><select name="Met[]" class="assessmentDropDown outcome'.$taughtID.'"><!--<option value=""></option>-->';
                            for($x=0; $x<101; $x++)
                                {
                                $output.='<option value="'.$taughtID.' '.$x.'">'.$x.'</option>';
                                }
                                $output.='</select></td>';

                     $output.='<td class="assessmentInput"><select name="Partial[]" class="assessmentDropDown outcome'.$taughtID.'"><!--<option value=""></option>-->';
                            for($x=0; $x<101; $x++)
                                {
                                $output.='<option value="'.$taughtID.' '.$x.'">'.$x.'</option>';
                                }
                                $output.='</select></td>';

                     $output.='<td class="assessmentInput"><select name="NotMet[]" class="assessmentDropDown outcome'.$taughtID.'"><!--<option value=""></option>-->';
                            for($x=0; $x<101; $x++)
                                {
                                $output.='<option value="'.$taughtID.' '.$x.'">'.$x.'</option>';
                                }
                                $output.='</select></td>';

                    $output.='<td class="assessmentInput didNotAssess"><span class="didNotAssess">Did not assess</span>'.
                                '<input id="notAssessed'.$taughtID.'" type="hidden" name="otctDidNotAssess[]" value="'.$taughtID.' 0" />'.
                               '<input id="outcome'.$taughtID.'" name = "otctDidNotAssessCheck[]" value="'.$taughtID.'" class="didNotAssess" type="checkbox" /></td>';

                     $output.='</tr>';






                    }

                    $output.='</table><input type="hidden" name="assessedCount" value="'.$assessedCount.'" />'.
                            '<input id="assessSubmit" type="submit" name="assessSubmit" disabled="disabled" value="Submit" /></form></div>' ;

                    return $output;
        }
    public function __construct($userName ='')
        {
        $this->user=$userName;
        }

    public function doPost($inPost, $inSuffix='')
        {

        $this->setLibrarianID($inPost['librarianID']);


        $this->setDateOfSession(date("Y-m-d", strtotime($inPost['dateOfSession'.$inSuffix])));

        $this->setLengthOfSessionID($inPost['lengthID'.$inSuffix]);
        $this->setNumberOfStudents($inPost['numberOfStudents'.$inSuffix]);
        $this->setCoursePrefixID($inPost['coursePrefixID'.$inSuffix]);
        $this->setCourseNumber($inPost['courseNumber'.$inSuffix]);
        $this->setCourseSection($inPost['courseSection'.$inSuffix]);
        $this->setCourseTitle($inPost['courseTitle'.$inSuffix]);
        $this->setSessionNumber($inPost['sessionNumber'.$inSuffix]);

        $this->setFaculty($inPost['faculty'.$inSuffix]);

        $this->setLocationID($inPost['locationID'.$inSuffix]);

        //notes
        $this->setSessionNote($inPost['sessionNote'.$inSuffix]);

        //resourcesIntroduced
        $this->setResourcesIntroducedID($inPost['resourcesIntroduced'.$inSuffix]);


        //if(DEBUG==true){return("All the variables are filled.<br /> Suffix: >>$inSuffix<<");}
        return("Session Created <br />");

        }
    public function insertSession()
        {
        $success='query not completed.';
        $query = $this->getSessionInsertQuery();

        $dbc=$this->getConnection();
        $result=mysqli_query($dbc, $query);
        if(!$result){$success.='Session insert failed: <br /> Error: '.mysqli_error($dbc).'<br />Query: '.$query.'<br />';}
        else {$success='Session insert success! <br />';}
        $this->setSessionID(mysqli_insert_id($dbc));



        // resourcesIntroduced and notes
            // if (DEBUG==true){$success.="<br />btw the session note value is: >>".$this->sessionNote."<< <br />";}
            if(isset($this->sessionNote) && trim($this->sessionNote)!='')
                {
            //notes
                $query=$this->getNoteQuery();
                $result=mysqli_query($dbc, $query);
                    if(!$result){$success.=' noteQuery insert failed <br /> Error: '.mysqli_error($dbc).'<br />Query: '.$query.'<br />';}
                    else {$success.=' Note Insert success! <br />';}   /* <br />'."Query is: $query <br />";}*/
                }
             else{$success.=' No note to insert. <br />';}


            //resourcesIntroduced
             if($this->resourcesIntroducedID!='none')
                 {
                    $query= $this->getResourcesQuery($this->sessionID, $this->resourcesIntroducedID);
                    $result=mysqli_query($dbc, $query);
                    if(!$result){$success.='  resourcesQuery insert fail <br />Error: '.mysqli_error($dbc).'<br /> Query: '.$query.'<br />';}
                    else {$success.=' resources Insert success! <br />';}
                 }
                 else{$success.=' No resources introduced';}

        $this->closeConnection($dbc);
        return $success;
        }

    private function getResourcesQuery($inID, $inResources)
        {

        $resourceString="";
        foreach ($inResources as $value)
            {
            $resourceString.= "($inID ,$value),";
            }
            //remove last comma
            $resourceString = rtrim($resourceString, ',');

        //TEST: handle the resources array.
        $query ="insert into resourcesintroduced (rsrisesdID, rsrirsrpID) values $resourceString";
        return $query;
        }

    private function getNoteQuery()
        {
        $query ="insert into sessionnotes (sesnsesdID, sesnNote) values ($this->sessionID, '$this->sessionNote')";
        return $query;
        }
    public function getSessionQuery($inID)
        {
		/*
        $query="select sesdID, sesdUser, sesdlibmID, sesdDate, sesdseslID, ".
              "sesdNumStudents, sesdcrspID, sesdCourseNumber, sesdCourseTitle, sesdCourseSection, ".
               "sesdSessionSection, sesdFaculty, sesdlocaID, sesdOutcomeDone, sesdAssessed ".
                "from sessiondesc where sesdID=$inID";
		 */
		// New query, includes notes. Does not handle resources, though it should. -Webster
//		$query="select sd.sesdID, sd.sesdUser, sd.sesdlibmID, sd.sesdDate, sd.sesdseslID, ".
//			"sd.sesdNumStudents, sd.sesdcrspID, sd.sesdCourseNumber, sd.sesdCourseTitle, sd.sesdCourseSection, ".
//			"sd.sesdSessionSection, sd.sesdFaculty, sd.sesdlocaID, sd.sesdOutcomeDone, sd.sesdAssessed, ".
//			"sn.sesnNote, ri.rsrirsrpID " .
//			"from sessiondesc sd, sessionnotes sn, resourcesintroduced ri where sesdID = $inID and sesnsesdID = $inID and rsrisesdID = $inID";
		// Even newer query, returns rows even when there's no notes. Don't think we do need resources here. Should be more efficient. -Webster
		 $query="SELECT * FROM sessiondesc sd
				LEFT OUTER JOIN sessionnotes sn
					ON sd.sesdID = sn.sesnsesdID
			WHERE sesdID=$inID;";
//		echo $query;
        return $query;
        }
    public function loadSession($inID)
        {
        //TODO loadSession() needs to load resources introduced and notes still.
        $query = $this->getSessionQuery($inID);
        $dbc=$this->getConnection();
        $result = mysqli_query($dbc, $query) or die("Error loading session: " . mysqli_error($dbc));

         while ( $row = mysqli_fetch_assoc( $result) )
            {
            $this->sessionID=$row['sesdID'];
            $this->user=$row['sesdUser'];
            $this->librarianID=$row['sesdlibmID'];
            $this->dateOfSession=$row['sesdDate'];

            $this->lengthOfSessionID=$row['sesdseslID'];
            $this->numberOfStudents=$row['sesdNumStudents'];
            $this->setCoursePrefixID($row['sesdcrspID']);
            $this->courseTitle=$row['sesdCourseTitle'];
            $this->courseNumber=$row['sesdCourseNumber'];
            $this->courseSection=$row['sesdCourseSection'];
            $this->sessionNumber=$row['sesdSessionSection'];

            $this->faculty=$row['sesdFaculty'];
            $this->setLocationID($row['sesdlocaID']);
            $this->outcomeDone=$row['sesdOutcomeDone'];
            $this->assessed=$row['sesdAssessed'];

			$this->sessionNote=$row['sesnNote'];

			//$this->resourcesIntroducedID=$row['rsrirsrpID'];
            }
            mysqli_free_result($result);
        $this->closeConnection($dbc);



        }


    public function getSessionInsertQuery()
        {
            /*TODO: implement mysql_real_escape_string()
             * or:
             * $stmt = $db->prepare('update people set name = ? where id = ?');
               $stmt->bind_param('si',$name,$id);
                $stmt->execute(); ;
             */
            $query = "insert into sessiondesc".
            "(sesdUser, sesdlibmID, sesdDate, sesdseslID, sesdNumStudents, sesdcrspID, ".
             "sesdCourseNumber, sesdCourseTitle, sesdCourseSection, sesdSessionSection, sesdFaculty, sesdlocaID)".
                "values".
             "('".$this->user.
             "', $this->librarianID, '$this->dateOfSession',".
             "$this->lengthOfSessionID, $this->numberOfStudents, $this->coursePrefixID,".
             "$this->courseNumber, '".$this->courseTitle.
             "', $this->courseSection, '$this->sessionNumber', '".
              $this->faculty."',".
             "$this->locationID)";

            return $query;
        }


    private function getConnection()
        {
        $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME)
                    or die('Error connecting to the stupid database');
        return $dbc;
        }
    private function closeConnection($dbc)
        {
        mysqli_close($dbc);
        }



    public function toString()
        {
                $output='';

            $output.="<p class='sessionToString'>".
                    "<span class='name'>User: </span><span class='value'>$this->librarianName</span><br />".
                    "<span class='name'>Date of session: </span><span class='value'>$this->dateOfSession</span><br />".
                    "<span class='name'>Length of Session: </span><span class='value'>$this->lengthOfSessionName</span><br />".
                    "<span class='name'>Number of Students: </span><span class='value'>$this->numberOfStudents</span><br />".
                    "<span class='name'>Course </span>: <span class='value'>$this->coursePrefix $this->courseNumber - $this->courseSection </span><br />".
                    "<span class='name'>Course Title: </span><span class='value'>$this->courseTitle</span><br />".
                    "<span class='name'>Session#: </span><span class='value'>$this->sessionNumber</span><br />".
                    "<span class='name'>FacultyName: </span><span class='value'>$this->faculty</span><br />".
                    "<span class='name'>Location: </span><span class='value'>$this->locationName</span><br />".
                    "<span class='name'>Outcomes taught entered? : </span><span class='value'>$this->outcomeDone</span><br />".
                    "<span class='name'>Outcomes assessed yet? : </span><span class='value'>$this->assessed</span><br />".
                    "<span class='name'>Session Notes: </span><span class='value'>$this->sessionNote</span><br />".
                    "<span class='name'>Resources Introduced: </span><br /><span class='value'>";

                   //TODO work on loading session to include notes and resources introduced
                    if($this->resourcesIntroducedID=='none')
                        {$output.='&nbsp;&nbsp;&nbsp;&nbsp;None introduced<br />';}
                    else
                        {
                        foreach ($this->resourcesIntroducedID as $x)
                            {
                            $output.='&nbsp;&nbsp;&nbsp;&nbsp;'.$this->resourcesIntroducedName[$x].'<br />';
                            }
                        }
                    $output.='</span><br /><br /></p>';

                    return $output;
        }
    public function toStringDebug()
        {
                $output='';

            $output.="<p>".
                    "<strong>User: </strong>$this->user<br />".
                    "<strong>SessionID: </strong>$this->sessionID<br />".
                    "<strong>Librarian: </strong>$this->librarianID"."&nbsp;&nbsp;&nbsp;&nbsp;<strong>User: </strong>$this->librarianName<br />".
                    "<strong>Date of session: </strong>$this->dateOfSession"."<br />".
                    "<strong>Length of sessionID: </strong>$this->lengthOfSessionID"."&nbsp;&nbsp;&nbsp;&nbsp;<strong>Length of Session: </strong>$this->lengthOfSessionName<br />".
                    "<strong>Number of Students: </strong>$this->numberOfStudents<br />".
                    "<strong>Course PrefixID: </strong>$this->coursePrefixID&nbsp;&nbsp;&nbsp;&nbsp;"."<Strong>Course Prefix</strong>:".$this->coursePrefix."<br />".
                    "<strong>Course Number: </strong>$this->courseNumber&nbsp;&nbsp;&nbsp;&nbsp;".
                    "<strong>Course Section: </strong>$this->courseSection&nbsp;&nbsp;&nbsp;&nbsp;".
                    "<strong>Course Title: </strong>$this->courseTitle<br />".
                    "<strong>Session#: </strong>$this->sessionNumber<br />".
                    "<strong>FacultyID: </strong>$this->facultyID"."&nbsp;&nbsp;&nbsp;&nbsp;<strong>FacultyName: </strong>$this->faculty<br />".
                    "<strong>LocationID: </strong>$this->locationID"."&nbsp;&nbsp;&nbsp;&nbsp;<strong>Location Name: </strong>$this->locationName<br />".
                    "<strong>Outcomes taught inserted? : </strong>$this->outcomeDone<br />".
                    "<strong>Outcomes assessed yet? : </strong>$this->assessed<br />".
                    "<strong>Session Notes: </strong>$this->sessionNote<br />".
                    "<strong>Resources Introduced: </strong><br />";

                   //TODO work on loading session to include notes and resources introduced
                    if($this->resourcesIntroducedID=='none')
                        {$output.='&nbsp;&nbsp;&nbsp;&nbsp;None introduced<br />';}
                    else
                        {
                        foreach ($this->resourcesIntroducedID as $x)
                            {
                            $output.='&nbsp;&nbsp;&nbsp;&nbsp;'.$x.' '.$this->resourcesIntroducedName[$x].'<br />';
                            }
                        }
                    $output.='</p>';

                    return $output;
        }
    public function getSessionID()
        {
        return $this->sessionID;
        }
    public function setSessionID($inID)
        {
        $this->sessionID=$inID;
        }
    public function getUser()
        {
        return $this->user;
        }

    public function setUser($user)
        {
        $this->user = $user;
        }

    public function getLibrarianID()
        {
        return $this->librarianID;
        }

    public function setLibrarianID($librarian)
        {

        $this->librarianID = (int)$librarian;
        $this->setLibrarianName($this->librarianID);
        }

     public function getLibrarianName()
         {
         return $this->librarianName;
         }


     public function setCoursePrefix($inID)
         {
         $dbc=$this->getConnection();
         $query = "select crspName as prefixName from courseprefix where crspID= $inID";
         $result = mysqli_query($dbc, $query) or die($query.' crustacean!- query issues.'.mysqli_error($dbc));
          if(!$result){echo "this is an outrage: ".mysqli_error($dbc)."\n";}

            while ( $row = mysqli_fetch_assoc( $result) )
            {
                $prefix=$row['prefixName'];
            }
           $this->coursePrefix=$prefix;
           $this->closeConnection($dbc);

         }
     public function setLibrarianName($inID)
         {
         $dbc=$this->getConnection();
         $query = "select ppleLName as LName, ppleFName as FName from people p, librarianmap l where  libmID= $inID and p.ppleID=l.libmppleID";
         $result = mysqli_query($dbc, $query) or die('crustacean!- query issues.'.mysqli_error($dbc));
          if(!$result){echo "this is an outrage: ".mysqli_error($dbc)."\n";}

            while ( $row = mysqli_fetch_assoc( $result) )
            {
                $LName=$row['LName'];
                $FName = $row['FName'];
            }
           $this->librarianName=$FName.' '.$LName;
           $this->closeConnection($dbc);

         }

    public function getLocation()
        {
        return $this->locationID;
        }

    public function setLocationID($inID)
        {
        $this->locationID = (int)$inID;
        $this->setLocationName($inID);
        }

    public function getLocationName()
        {
            return $this->locationName;
        }

    public function setLocationName($inID)
        {
        $dbc=$this->getConnection();
         $query = "select locaName as Name from location where  locaID= $inID ";
         $result = mysqli_query($dbc, $query) or die('crappy crustacean!- query issues.'.mysqli_error($dbc));
          if(!$result){echo "this is an outrage: ".mysqli_error($dbc)."\n";}

            while ( $row = mysqli_fetch_assoc( $result) )
            {
                $Name=$row['Name'];
            }
           $this->locationName=$Name;
           $this->closeConnection($dbc);
        }
    public function getDateOfSession()
        {
        return $this->dateOfSession;
        }

    public function setDateOfSession($dateOfSession)
        {
        $this->dateOfSession = $dateOfSession;
        }



    public function getLengthOfSessionID()
        {
        return $this->lengthOfSessionID;
        }

    public function setLengthOfSessionName($inID)
        {
        $dbc=$this->getConnection();
         $query = "select seslName as Name from sesslength where seslID= $inID ";
         $result = mysqli_query($dbc, $query) or die('crustacean!- query issues.'.mysqli_error($dbc));
          if(!$result){echo "this is an outrage: ".mysqli_error($dbc)."\n";}


            while ( $row = mysqli_fetch_assoc( $result) )
            {
                $Name=$row['Name'];
            }
           $this->lengthOfSessionName=$Name;
           $this->closeConnection($dbc);
        }

   public function setLengthOfSessionID($lengthOfSession)
        {
        $this->lengthOfSessionID = (int)$lengthOfSession;
        $this->setLengthOfSessionName($this->lengthOfSessionID);
        }

    public function getNumberOfStudents()
        {
        return $this->numberOfStudents;
        }

    public function setNumberOfStudents($numberOfStudents)
        {
        $this->numberOfStudents = (int)$numberOfStudents;
        }

    public function getCoursePrefixID()
        {
        return $this->coursePrefixID;
        }
    public function getCoursePrefix()
        {
        return $this->coursePrefix;
        }

    public function setCoursePrefixID($inID)
        {
        $this->coursePrefixID = (int)$inID;
        $this->setCoursePrefix($inID);
        }

    public function getCourseNumber()
        {
        return $this->courseNumber;
        }

    public function setCourseNumber($courseNumber)
        {
        $this->courseNumber = (int)$courseNumber;
        }

    public function getCourseSection()
        {
        return $this->courseSection;
        }

    public function setCourseSection($courseSection)
        {
        $this->courseSection = (int)$courseSection;
        }

    public function getCourseTitle()
        {
        return $this->courseTitle;
        }

    public function setCourseTitle($courseTitle)
        {
        $this->courseTitle = $courseTitle;
        }

    public function getSessionNumber()
        {
        return $this->sessionNumber;
        }

    public function setSessionNumber($sessionNumber)
        {
        $this->sessionNumber = $sessionNumber;
        }

    public function getFaculty()
        {
        return $this->faculty;
        }

    public function setFaculty($faculty)
        {
        $this->faculty = $faculty;
        }

    public function getResourcesIntroducedID()
        {
        return $this->resourcesIntroducedID;
        }

    public function setResourcesIntroducedID($inResourcesIntroducedID)
        {
        $this->resourcesIntroducedID = $inResourcesIntroducedID;
        $this->setResourcesIntroducedName($inResourcesIntroducedID);
        }
    public function getResourcesIntroducedName()
        {
        return $this->resourcesIntroducedName;
        }
    public function setResourcesIntroducedName($inResourcesIntroducedID)
        {

        $dbc=$this->getConnection();

        foreach($inResourcesIntroducedID as $value)
            {

            $query = "select rsrpName as Name from resourcepool where rsrpID=$value";
            $result = mysqli_query($dbc, $query) or die('crustacean!- query issues.'.mysqli_error($dbc).$query);
            if(!$result){echo "this is an outrage: ".mysqli_error($dbc)."\n";}
            while ( $row = mysqli_fetch_assoc( $result) )
                {
                 $this->resourcesIntroducedName[$value] = $row['Name'];
                }

            }
            $this->closeConnection($dbc);

        }
    public function getSessionNote()
        {
        return $this->sessionNote;
        }

    public function setSessionNote($sessionComment)
        {
        $this->sessionNote = $sessionComment;
        }


        public function getSesdCopyID()
        {
        return $this->sesdCopyID;
        }

    public function setSesdCopyID($sesdCopyID)
        {
        $this->sesdCopyID = $sesdCopyID;
        }

    public function getOutcomeDone()
        {
        return $this->outcomeDone;
        }

    public function setOutcomeDone($outcomeDone)
        {
        $this->outcomeDone = $outcomeDone;
        }

    public function getAssessed()
        {
        return $this->assessed;
        }

    public function setAssessed($assessed)
        {
        $this->assessed = $assessed;
        }

    public function getLengthOfSessionName()
        {
        return $this->lengthOfSessionName;
        }



}

?>
