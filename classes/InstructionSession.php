<?php


class InstructionSession {

    //SessionDesc variables
    private $sessionID=0;
    private $user='';
    private $librarianID = 0;
		private $fellowPresent = 'no';
    private $dateOfSession = '';

    private $lengthOfSessionID = null;
    private $numberOfStudents;
    private $coursePrefixID=0;
    private $courseNumber;
    private $courseSection;
    private $courseTitle='';
    private $sessionNumber='';
    private $faculty='';
    private $locationID = null;
    private $sesdCopyID=0;
    private $outcomeDone='no';
    private $assessed='no';


    private $outcomesTaught = array();
    //private $outcomesAssessed=array();
		private $outcomesAssessed;
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
				$success = 'Inserting outcomes taught:<br/>';
				
				$dbc=$this->getConnection();
				$stmt = mysqli_prepare($dbc, 'insert into outcomestaught (otctsesdID, otctotcdID) values (?, ?)');
				
				$numOutcomes = count($this->outcomesTaught);
				
				for ($i=0; $i<$numOutcomes; $i++) {
					mysqli_bind_param($stmt, 'ii', $this->sessionID, $this->outcomesTaught[$i]);
					if (mysqli_stmt_execute($stmt)) {
						
						$success .= 'Successfully inserted session '.$this->sessionID.', outcome '.$this->outcomesTaught[$i].'<br/>';
						$stmt2 = mysqli_prepare($dbc, 'update sessiondesc set sesdOutcomeDone="yes" where sesdID=?');
						mysqli_bind_param($stmt2, 'i', $this->sessionID);
						
						if (mysqli_stmt_execute($stmt2)) {
							$success .= 'Successfully updated session '.$this->sessionID.' to assessed<br/>';
						} else {
							$success .= 'Error: Failed to update session '.$this->sessionID.' to assessed: '.mysqli_error($dbc).'<br/>';
						}
						
					} else {
						$success .= 'Error: Failed to insert session '.$this->sessionID.', outcome '.$this->outcomesTaught[$i].': '.mysqli_error($dbc).'<br/>';
					}
				}
						 
        return $success;
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
						$success='Setting and inserting outcomes assessed...<br/>';
						$dbc=$this->getConnection();
						$stmt = mysqli_prepare($dbc, 'insert into outcomesassessed (otcaotctID, otcaMet, otcaPartial, otcaNotMet, otcaNotAssessed) values (?, ?, ?, ?, ?)');
						
						// Loop through each element of $inArray, binding variables to and executing the above query in each iteration
						foreach ($inArray as $row) {
							
							// If not assessed, force values to 0
							if ($row['NotAssessed']) {
								$row['Met']=0;
								$row['Partial']=0;
								$row['NotMet']=0;
							}
							
							mysqli_bind_param($stmt, 'iiiii', $row['otctID'], $row['Met'], $row['Partial'], $row['NotMet'], $row['NotAssessed']);
							
							// If inserting outcome assessment succeeds, update sessiondesc for this session and set assessed to 'yes':
							if (mysqli_stmt_execute($stmt)) {
								
								$success .= 'Successfully assessed outcome '.$row['otctID'].'!<br/>';
								
								// Set assessed to 'yes'
								
								$stmt2 = mysqli_prepare($dbc, 'update sessiondesc set sesdAssessed="yes" where sesdID=?');
								mysqli_bind_param($stmt2, 'i', $this->sessionID);
								
								if (mysqli_stmt_execute($stmt2)) {
									$this->assessed='yes';
									$success .= 'Successfully set session '.$this->sessionID.' to assessed!<br/>';
								} else {
									// Setting assessed to 'yes' failed
									$success .= 'Error: Failed to set session '.$this->sessionID.' as assessed: '.mysqli_error($dbc).'<br/>';
								}
								
							} else {
								// Inserting assessment failed
								$success .= 'Error: Failed to insert outcome assessment '.$row['otctID'].': '.mysqli_error($dbc).'<br/>';
							}
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
                    "ot.otctsesdID=? ".
                    "and otpm.otcmotchID=oh.otchID ".
                    "and otpm.otcmcrspID=? ".
                    "and od.otcdotchID=oh.otchID ".
                    "and od.otcdID = ot.otctotcdID ".
                    //"group by headingName ".
                    "order by oh.otchID, od.otcdID";

                $currentOutcomeHeading='first';
                //$output='<div class="test">'.$query.'</div>';
                $output='';

                $dbc=$this->getConnection();
								$stmt = mysqli_prepare($dbc, $query);
								mysqli_bind_param($stmt, 'ii', $this->sessionID, $this->coursePrefixID);
								mysqli_stmt_execute($stmt) or die('Failed to get outcomes to assess: ' . mysqli_error($dbc));
								mysqli_stmt_store_result($stmt);
								mysqli_stmt_bind_result($stmt, $taughtID, $headingID, $headingName, $subheadingName, $outcomeID, $outcomeName);

                    $assessedCount=0;
                    $output.='<div class="assessmentDiv">';
                    $output.='<h4 id="courseIdent">'.$this->coursePrefix.' '.$this->courseNumber.'-'.$this->courseSection.' '.$this->courseTitle.'</h4>';
                    $output.='<div id="courseSummary" class="hidden">'.$this->toString().'</div>';
                    $output.= '<form action="submitAssessment.php" method="post">';
										while (mysqli_stmt_fetch($stmt)) {
                        $assessedCount++;

                        if ($headingName!=$currentOutcomeHeading)
                            {
                                if($currentOutcomeHeading!='first'){$output.='</table>';}
                                $currentOutcomeHeading = $headingName;
                                $output.= '<h4 class="xxx assessmentHeading">'.$headingName.'</h4>';
                                if ($subheadingName ==''){$output.= '<h5 class="outcomesBox outcomeSubheading">'.$subheadingName.'</h5>';}
                                else {$output.='<h5 class="outcomesBox outcomeSubheading">'.$this->coursePrefix.': '.$subheadingName.'</h5>';}

                                $output.='<table id="headingID'.$headingID.'">'.
                                        '<thead><tr><th>Outcome</th><th>Met outcome</th><th>Partially met outcome</th><th>Did not meet outcome</th><th>Did not assess</th></tr>';

                                $currentOutcomeHeading=$headingName;
                            }

                    // Since these default to 0 assessOutcomes() in sessionInput.js is unused
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

                    $output.='<td class="assessmentInput didNotAssess">'.
                                '<input id="notAssessed'.$taughtID.'" type="hidden" name="otctDidNotAssess[]" value="'.$taughtID.' 0" />'.
                               '<input id="outcome'.$taughtID.'" name = "otctDidNotAssessCheck[]" value="'.$taughtID.'" class="didNotAssess" type="checkbox" /></td>';

                     $output.='</tr>';






                    }

                    $output.='</table><input type="hidden" name="assessedCount" value="'.$assessedCount.'" />
														<input type="hidden" name="sessionID" value="'.$this->sessionID.'"/>
                            <input id="assessSubmit" type="submit" name="assessSubmit" disabled="disabled" value="Submit" /></form></div>' ;

                    return $output;
        }
    public function __construct($userName ='')
        {
        $this->user=$userName;
        }

    public function doPost($inPost, $inSuffix='')
        {

        $this->setLibrarianID($inPost['librarianID']);
				
				if (isset($inPost['fellowPresent']) && $inPost['fellowPresent'] == 'on') {
					$this->setFellowPresent('yes');
				} else {
					$this->setFellowPresent('no');
				}
				
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
        $dbc=$this->getConnection();

		$stmt = mysqli_prepare($dbc, $this->getSessionInsertQuery());
		$stmt->bind_param("sissiiiisissi",
				$this->user,
				$this->librarianID,
				$this->fellowPresent,
				$this->dateOfSession,
				$this->lengthOfSessionID,
				$this->numberOfStudents,
				$this->coursePrefixID,
				$this->courseNumber,
				$this->courseTitle,
				$this->courseSection,
				$this->sessionNumber,
				$this->faculty,
				$this->locationID);
		$stmt->execute() or die("Failed to insert session: " . mysqli_error($dbc));

		$id = $stmt->insert_id;
		$this->setSessionID($id);

		$this->setNotes($dbc, $this->sessionID, $this->sessionNote);
		$this->setResources($dbc, $this->sessionID, $this->resourcesIntroducedID);

		return $id;
	}

	public function updateSession($id) {
			$dbc = $this->getConnection();
			$stmt = mysqli_prepare($dbc, $this->getSessionUpdateQuery());
			$stmt->bind_param("sissiiiisissii",
				$this->user,
				$this->librarianID,
				$this->fellowPresent,
				$this->dateOfSession,
				$this->lengthOfSessionID,
				$this->numberOfStudents,
				$this->coursePrefixID,
				$this->courseNumber,
				$this->courseTitle,
				$this->courseSection,
				$this->sessionNumber,
				$this->faculty,
				$this->locationID,
				$id);
			$stmt->execute() or die("Failed to update session: " . $stmt->error);
			$stmt->close;

			$this->setNotes($dbc, $id, $this->sessionNote);
			$this->setResources($dbc, $id, $this->resourcesIntroducedID);
	}

		private function setResources($dbc, $inID, $inResources) {
			// Potentially dangerous two-step operation, so we use a transaction to ensure both succeed, or both fail. -Webster
			try {
				mysqli_autocommit($dbc, false);

				$stmt = mysqli_prepare($dbc, 'delete from resourcesintroduced where rsrisesdID = ?');
				$stmt->bind_param('i', $inID);
				if(!$stmt->execute()) throw new Exception("Error performing resource deletion query: " . $stmt->error);

				if($inResources != 'none') {
					$stmt = mysqli_prepare($dbc, 'insert into resourcesintroduced (rsrisesdID, rsrirsrpID) values (?, ?)');
					foreach ($inResources as $value) {
						$stmt->bind_param('is', $inID, $value);
						if(!$stmt->execute()) throw new Exception("Failed to insert resource ($inID, $value): " . $stmt->error);
					}
				}

				mysqli_commit($dbc);
				mysqli_autocommit($dbc, true);
			} catch (Exception $e) {
				mysqli_rollback($dbc);
				mysqli_autocommit($dbc, true);
				die("Couldn't set resources: " . $e->getMessage());
			}
		}

		private function setNotes($dbc, $inID, $inNote) {
			// Potentially dangerous two-step operation, so we use a transaction to ensure both succeed, or both fail. -Webster
			try {
				mysqli_autocommit($dbc, false);

				$stmt = mysqli_prepare($dbc, "delete from sessionnotes where sesnsesdID = ?");
				$stmt->bind_param('i', $inID);
				if(!$stmt->execute()) throw new Exception("Error performing note deletion query: " . $stmt->error);

				// Note has been deleted. If note is blank, don't worry about inserting a row just for that.
				if (isset($inNote) && trim($inNote) != '') {
					$stmt = mysqli_prepare($dbc, 'insert into sessionnotes (sesnsesdID, sesnNote) values (?, ?)');
					$stmt->bind_param('is', $inID, trim($inNote));
					if(!$stmt->execute()) throw new Exception("Failed to insert note: " . $stmt->error);
				}

				mysqli_commit($dbc);
				mysqli_autocommit($dbc, true);
			} catch (Exception $e) {
				mysqli_rollback($dbc);
				mysqli_autocommit($dbc, true);
				die("Couldn't set note: " . $e->getMessage());
			}
		}

	public function loadSession($inID) {
		$dbc = $this->getConnection();

		$row = array();
		$stmt = mysqli_prepare($dbc, 'SELECT sesdID, sesdUser, sesdlibmID, sesdFellowPresent, sesdDate, sesdseslID, sesdNumStudents, sesdcrspID, sesdCourseTitle,
				sesdCourseNumber, sesdCourseSection, sesdSessionSection, sesdFaculty, sesdlocaID, sesdOutcomeDone, sesdAssessed, sesnNote
			FROM sessiondesc sd LEFT OUTER JOIN sessionnotes sn
			ON sd.sesdID = sn.sesnsesdID WHERE sesdID=?');
		mysqli_bind_param($stmt, 'i', $inID);
		mysqli_stmt_execute($stmt);
		mysqli_stmt_store_result($stmt);
		mysqli_stmt_bind_result($stmt, $row['sesdID'], $row['sesdUser'], $row['sesdlibmID'], $row['fellowPresent'], $row['sesdDate'], $row['sesdseslID'], $row['sesdNumStudents'],
			$row['sesdcrspID'], $row['sesdCourseTitle'], $row['sesdCourseNumber'], $row['sesdCourseSection'], $row['sesdSessionSection'], $row['sesdFaculty'],
			$row['sesdlocaID'], $row['sesdOutcomeDone'], $row['sesdAssessed'], $row['sesnNote']);
		mysqli_stmt_fetch($stmt);

		$this->sessionID = $row['sesdID'];
		$this->user = $row['sesdUser'];
		$this->librarianID = $row['sesdlibmID'];
		$this->fellowPresent = $row['fellowPresent'];
		$this->dateOfSession = $row['sesdDate'];
		$this->lengthOfSessionID = $row['sesdseslID'];
		$this->numberOfStudents = $row['sesdNumStudents'];
		$this->setCoursePrefixID($row['sesdcrspID']);
		$this->courseTitle = $row['sesdCourseTitle'];
		$this->courseNumber = $row['sesdCourseNumber'];
		$this->courseSection = $row['sesdCourseSection'];
		$this->sessionNumber = $row['sesdSessionSection'];
		$this->faculty = $row['sesdFaculty'];
		$this->setLocationID($row['sesdlocaID']);
		$this->outcomeDone = $row['sesdOutcomeDone'];
		$this->assessed = $row['sesdAssessed'];
		$this->sessionNote = $row['sesnNote'];

		$row = array();
		$stmt = mysqli_prepare($dbc, 'select rsrpName, rsrirsrpID from resourcepool rp
			left outer join resourcesintroduced ri
			on (ri.rsrirsrpID = rp.rsrpID and ri.rsrisesdID = ?)
			where rp.rsrpActive="yes"');
		mysqli_bind_param($stmt, 'i', $inID);
		mysqli_stmt_execute($stmt) or die('Failed to execute query: ' . mysqli_error($dbc));
		mysqli_stmt_store_result($stmt);
		mysqli_stmt_bind_result($stmt, $row['name'], $row['id']);
		while (mysqli_stmt_fetch($stmt)) {
			if($row['id']) {
				$this->resourcesIntroducedID[$row['id']] = $row['id'];
				$this->resourcesIntroducedName[$row['id']] = $row['name'];
			}
		}

		mysqli_stmt_free_result($stmt);
	}

    public function getSessionInsertQuery()
        {
//            $query = "insert into sessiondesc".
//            "(sesdUser, sesdlibmID, sesdDate, sesdseslID, sesdNumStudents, sesdcrspID, ".
//             "sesdCourseNumber, sesdCourseTitle, sesdCourseSection, sesdSessionSection, sesdFaculty, sesdlocaID)".
//                "values".
//             "('".$this->user.
//             "', $this->librarianID, '$this->dateOfSession',".
//             "$this->lengthOfSessionID, $this->numberOfStudents, $this->coursePrefixID,".
//             "$this->courseNumber, '".$this->courseTitle.
//             "', $this->courseSection, '$this->sessionNumber', '".
//              $this->faculty."',".
//             "$this->locationID)";

			return "insert into sessiondesc".
				"(sesdUser, sesdlibmID, sesdFellowPresent, sesdDate, sesdseslID, sesdNumStudents, sesdcrspID, ".
				"sesdCourseNumber, sesdCourseTitle, sesdCourseSection, sesdSessionSection, sesdFaculty, sesdlocaID)".
				"values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        }

	public function getSessionUpdateQuery() {
		return "update sessiondesc set
				sesdUser=?,
				sesdlibmID=?,
				sesdFellowPresent=?,
				sesdDate=?,
				sesdseslID=?,
				sesdNumStudents=?,
				sesdcrspID=?,
				sesdCourseNumber=?,
				sesdCourseTitle=?,
				sesdCourseSection=?,
				sesdSessionSection=?,
				sesdFaculty=?,
				sesdlocaID=?
				where sesdID = ?";
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

                    if($this->resourcesIntroducedID) {
                      foreach ($this->resourcesIntroducedID as $x) {
                        $output.='&nbsp;&nbsp;&nbsp;&nbsp;'.$x.': '.$this->resourcesIntroducedName[$x].'<br />';
                      }
                    } else {
                      $output .= '&nbsp;&nbsp;&nbsp;&nbsp;None<br />';
                    }
                    $output.='</span></p>';

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

                    if($this->resourcesIntroducedID) {
                      foreach ($this->resourcesIntroducedID as $x) {
                        $output.='&nbsp;&nbsp;&nbsp;&nbsp;'.$x.': '.$this->resourcesIntroducedName[$x].'<br />';
                      }
                    } else {
                      $output .= '&nbsp;&nbsp;&nbsp;&nbsp;None<br />';
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

		public function getFellowPresent()
				{
					return $this->fellowPresent;
				}
				
		public function setFellowPresent($fellowPresent)
				{
					$this->fellowPresent = $fellowPresent;
				}

     public function getLibrarianName()
         {
         return $this->librarianName;
         }


     public function setCoursePrefix($inID)
         {
					$dbc = $this->getConnection();
					$prefixName = "";
					$stmt = mysqli_prepare($dbc, 'select crspName from courseprefix where crspID=?');
					mysqli_bind_param($stmt, 'i', $inID);
					mysqli_stmt_execute($stmt) or die('Failed to retrieve course prefix name: ' . mysqli_error($dbc));
					mysqli_stmt_store_result($stmt);
					mysqli_stmt_bind_result($stmt, $prefixName);
					mysqli_stmt_fetch($stmt);
					$this->coursePrefix = $prefixName;
					$this->closeConnection($dbc);
				 }

     public function setLibrarianName($inID)
         {
					$dbc = $this->getConnection();
					$LName="";
					$FName="";
					$stmt = mysqli_prepare($dbc, 'select ppleLName, ppleFName from people p, librarianmap l where libmID=? and p.ppleID=l.libmppleID');
					mysqli_bind_param($stmt, 'i', $inID);
					mysqli_stmt_execute($stmt) or die('Failed to retrieve librarian name: ' . mysqli_error($dbc));
					mysqli_stmt_store_result($stmt);
					mysqli_stmt_bind_result($stmt, $LName, $FName);
					mysqli_stmt_fetch($stmt);
					$this->librarianName = $FName.' '.$LName;
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
					$dbc = $this->getConnection();
					$name = "";
					$stmt = mysqli_prepare($dbc, 'select locaName from location where locaID=?');
					mysqli_bind_param($stmt, 'i', $inID);
					mysqli_stmt_execute($stmt) or die('Failed to retrieve location name: ' . mysqli_error($dbc));
					mysqli_stmt_store_result($stmt);
					mysqli_stmt_bind_result($stmt, $name);
					mysqli_stmt_fetch($stmt);
					$this->locationName = $name;
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
					$dbc = $this->getConnection();
					$name = "";
					$stmt = mysqli_prepare($dbc, 'select seslName from sesslength where seslID=?');
					mysqli_bind_param($stmt, 'i', $inID);
					mysqli_stmt_execute($stmt) or die('Failed to retrieve session length name: ' . mysqli_error($dbc));
					mysqli_stmt_store_result($stmt);
					mysqli_stmt_bind_result($stmt, $name);
					mysqli_stmt_fetch($stmt);
					$this->lengthOfSessionName = $name;
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
					$dbc = $this->getConnection();
					$resourceName = "";
					$stmt = mysqli_prepare($dbc, 'select rsrpName from resourcepool where rsrpID=?');
					foreach($inResourcesIntroducedID as $value) {
						mysqli_bind_param($stmt, 'i', $value);
						mysqli_stmt_execute($stmt) or die('Failed to retrieve resource name: ' . mysqli_error($dbc));
						mysqli_stmt_store_result($stmt);
						mysqli_stmt_bind_result($stmt, $resourceName);
						mysqli_stmt_fetch($stmt);
						$this->resourcesIntroducedName[$value] = $resourceName;
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
