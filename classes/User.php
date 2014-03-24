<?php

/**
 * Description of userSession
 *
 * @author dclark5
 */
class User
    {
    public $userID = 0;
    public $userName='';
    public $isAdmin=false;
    public $isPowerUser=false;
    public $isUser = false;
    public $isLibrarian=false;
    public $librarianID=0;
    public $firstName='';
    public $lastName='';
    public $firstLastName='';
    public $sessionID;


    public function toString()
        {
        $output="logged in as $this->userName";

        return $output;
        }
     public function getLibrarianID()
         {
         return $this->librarianID;
         }
     public function __construct($inUserID, $inUserName, $inRoleName)
        {


        $this->setUserID($inUserID);
        $this->setUserName($inUserName);
        $this->setRole($inRoleName);
        $this->setIsLibrarian($inUserID);
        }

    public function setRole($inRoleName)
        {
        if($inRoleName=='admin'){$this->isAdmin=true;}
        if($inRoleName=='power'){$this->isPowerUser=true;}
        if($inRoleName=='user'){$this->isUser=true;}
        }
   private function getConnection()
        {
        $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME)
                    or die('Error connecting to the database: Function getConnection() in User.php');
        return $dbc;
        }
   private function closeConnection($dbc)
        {
        mysqli_close($dbc);
        }
	public function setIsLibrarian($inID)
		{
			$dbc=$this->getConnection();
//			$result = mysqli_query($dbc, $query) or die('gah!- query issues.'.mysqli_error($dbc).$query);
			$stmt = mysqli_prepare($dbc, 'select exists(select 1 from librarianmap where libmuserID=?) as count');
			mysqli_bind_param($stmt, 'i', $inID);
			mysqli_stmt_execute($stmt) or die('Failed to see if librarian exists: ' . mysqli_error($dbc));
			mysqli_stmt_store_result($stmt);
			if (mysqli_stmt_num_rows($stmt)) {
				$this->isLibrarian=true;
				$this->setLibrarianName($inID);
			}
			mysqli_stmt_free_result($stmt);
		}
	public function setLibrarianName($inID)
	{
		$dbc = $this->getConnection();
		$stmt = mysqli_prepare($dbc, 'select p.ppleLName as LName, p.ppleFName as FName, l.libmID as ID from people p, librarianmap l where l.libmuserID=? and p.ppleID=l.libmppleID');
		mysqli_bind_param($stmt, 'i', $inID);
		mysqli_stmt_execute($stmt) or die('Failed to retrieve librarian info: ' . mysqli_error($dbc));
		mysqli_stmt_store_result($stmt);
		mysqli_stmt_bind_result($stmt, $LName, $FName, $ID);
		while (mysqli_stmt_fetch($stmt)) {
			$this->lastName = $LName;
			$this->firstName = $FName;
			$this->librarianID = $ID;
		}
		mysqli_stmt_free_result($stmt);
		$this->firstLastName = $this->firstName . ' ' . $this->lastName;
	}

     public function getNeedAssessed($inID) {
				$dbc=$this->getConnection();

				$query = 'select c.crspName as Name, count(s.sesdcrspID) as Count
					from sessiondesc s, courseprefix c
					where sesdlibmID=? and sesdOutcomeDone="yes" and sesdAssessed="no" and s.sesdcrspID=c.crspID
					group by s.sesdcrspID';

				$stmt = mysqli_prepare($dbc, $query);
				mysqli_bind_param($stmt, 'i', $inID);
				mysqli_stmt_execute($stmt) or die('Failed to retrieve number of outcomes to assess: ' . mysqli_error($dbc));
				mysqli_stmt_store_result($stmt);
				$row = array();
				mysqli_stmt_bind_result($stmt, $row['Name'], $row['Count']);
				// fill array with prefix as key and count needing outcomes as value
				$counts = array();
				while (mysqli_stmt_fetch($stmt)) {
					$counts[trim($row['Name'])] = $row['Count'];
				}
				mysqli_stmt_free_result($stmt);

				$query = 'select 
						sd.sesdID as ID, 
						cp.crspName as prefixName, 
						sd.sesdCourseNumber as courseNumber, 
						sd.sesdCourseTitle as courseTitle, 
						sd.sesdCourseSection as courseSection, 
						sd.sesdSessionSection as sessionSection, 
						sd.sesdDate as sessionDate 
					from sessiondesc sd, courseprefix cp 
					where sd.sesdlibmID=? and cp.crspID=sd.sesdcrspID and sd.sesdAssessed="no" and sd.sesdOutcomeDone="yes"
					order by sd.sesdcrspID, sd.sesdCourseSection, sd.sesdDate';
				$stmt = mysqli_prepare($dbc, $query);
				mysqli_bind_param($stmt, 'i', $inID);
				mysqli_stmt_execute($stmt) or die('Failed to retrieve session info: ' . mysqli_error($dbc));
				mysqli_stmt_store_result($stmt);
				mysqli_stmt_bind_result($stmt, $id, $prefixName, $courseNumber, $courseTitle, $courseSection, $sessionSection, $sessionDate);

				$output = '<ul>DanaJamesPlaceholder</ul><div id="courseByPrefix" class="empty">';
				$currentPrefixSection = 'none';

				while (mysqli_stmt_fetch($stmt)) {
					if ($prefixName != $currentPrefixSection) {
						$hrefString = '<li><a href="#'.$prefixName.'">'.$prefixName.'<span class="needAssessment">'.$counts[$prefixName].'</span></a></li>DanaJamesPlaceholder';
						$output = str_replace('DanaJamesPlaceholder', $hrefString, $output);

						if ($currentPrefixSection == 'none') {
							$output.='</div> <div id="'.$prefixName.'" class="assessmentList xxx">';
						} else {
							$output.='</tbody></table></div> <div id="'.$prefixName.'" class="assessmentList">';
						}

						$currentPrefixSection = $prefixName;
						$output.='<table id="'.$prefixName.'Table" class="assessmentNeeded sortable">
									<thead><tr><th>Course</th><th>Title</th><th>Session#</th><th>Date</th></tr></thead><tbody>';
					}

					$output.='<tr><td><span class="assessmentNeeded">'.$prefixName.$courseNumber.'-'.$courseSection.'</span></td>' .
									'<td>'.$courseTitle.'</td>' .
									'<td>'.$sessionSection.'</td>' .
									'<td>'.date("m/d/Y", strtotime($sessionDate)).'</td>' .
									'<td><form action="assessOutcome.php" method="post">' .
									'<input type="hidden" name="assessID" class="assessmentNeeded '.$prefixName.'" value="'.$id.'" />' .
									'<input type="submit" class="assessOutcomeButton" name="assessMe" value="Go" /></form></td></tr>';
				}
				mysqli_stmt_free_result($stmt);

				$output.='</table></div>';
				$output = str_replace('DanaJamesPlaceholder', '', $output);

				return $output;
				}
				
     public function getNeedOutcomes($inID)
        {
        $dbc=$this->getConnection();

				$query = 'select c.crspName as Name, count(s.sesdcrspID) as Count
							from sessiondesc s, courseprefix c
							where sesdlibmID=? and sesdOutcomeDone="no" and s.sesdcrspID=c.crspID
							group by s.sesdcrspID';

				$stmt = mysqli_prepare($dbc, $query);
				mysqli_bind_param($stmt, 'i', $inID);
				mysqli_stmt_execute($stmt) or die('Failed to retrieve number of outcomes needed: ' . mysqli_error($dbc));
				mysqli_stmt_store_result($stmt);
				$row = array();
				mysqli_stmt_bind_result($stmt, $row['Name'], $row['Count']);
				// fill array with prefix as key and count needing outcomes as value
				$counts = array();
				while (mysqli_stmt_fetch($stmt)) {
					$counts[trim($row['Name'])] = $row['Count'];
				}
				mysqli_stmt_free_result($stmt);

				$query = 'select 
								sd.sesdID as ID, 
								cp.crspName as prefixName, 
								sd.sesdCourseNumber as courseNumber, 
								sd.sesdCourseTitle as courseTitle, 
								sd.sesdCourseSection as courseSection, 
								sd.sesdSessionSection as sessionSection, 
								sd.sesdDate as sessionDate 
							from sessiondesc sd, courseprefix cp 
							where sd.sesdlibmID=? and cp.crspID=sd.sesdcrspID and sd.sesdOutcomeDone="no"
							order by sd.sesdcrspID, sd.sesdCourseSection, sd.sesdDate';
				
				$stmt = mysqli_prepare($dbc, $query);
				mysqli_bind_param($stmt, 'i', $inID);
				mysqli_stmt_execute($stmt) or die('Failed to retrieve session info: ' . mysqli_error($dbc));
				mysqli_stmt_store_result($stmt);
				mysqli_stmt_bind_result($stmt, $id, $prefixName, $courseNumber, $courseTitle, $courseSection, $sessionSection, $sessionDate);

				$output = '<ul>DanaJamesPlaceholder</ul><div id="courseByPrefix" class="empty">';
				$currentPrefixSection = 'none';

				while (mysqli_stmt_fetch($stmt)) {
					if ($prefixName != $currentPrefixSection) {
						$hrefString = '<li><a href="#'.$prefixName.'">'.$prefixName.'<span id="span'.$prefixName.'" class="needOutcomes">'.$counts[$prefixName].'</span></a></li>DanaJamesPlaceholder';
						$output = str_replace('DanaJamesPlaceholder', $hrefString, $output);

						if ($currentPrefixSection == 'none') {
							$output.='</div> <div id="'.$prefixName.'" class="outcomesList xxx">';
						} else {
							$output.='</tbody></table></div> <div id="'.$prefixName.'" class="outcomesList xxx">';
						}

						$output.='<h4 class="xxx outcomesList">'.$prefixName.' Courses</h4> ';

						$output.='<input type="checkbox" name="checkAll" class="checkAll '.$prefixName.'" value="'.$prefixName.'" />' .
										'<span class="xxx outcomesNeeded">Check all '.$prefixName.' courses</span><br class="outcomesNeeded" /><br class="outcomesNeeded" />';

						$currentPrefixSection = $prefixName;
						$output.='<table id="'.$prefixName.'Table" class="outcomesNeeded xxx sortable">' .
										'<thead><tr><th>Course</th><th>Title</th><th>Session#</th><th>Date</th></tr></thead><tbody>';
					}

					$output.='<tr><td><input type="checkbox" name="outcomesNeeded[]" class="outcomesNeeded '.$prefixName.'" value="'.$id.'" />' .
									'<span class="outcomesList">'.$prefixName.''.$courseNumber.'-'.$courseSection.'</span></td>' .
									'<td>'.$courseTitle.'</td>' .
									'<td>'.$sessionSection.'</td>' .
									'<td>'.date("m/d/Y", strtotime($sessionDate)).'</td></tr>';
				}
				$output.='</table></div>';
				$output = str_replace('DanaJamesPlaceholder', '', $output);

				return $output;
				}
				
    public function getUserID()
        {
        return $this->userID;
        }

    public function setUserID($userID)
        {
        $this->userID = $userID;
        }

    public function getUserName()
        {
        return $this->userName;
        }

    public function setUserName($userName)
        {
        $this->userName = $userName;
        }

    public function getIsAdmin()
        {
        return $this->isAdmin;
        }

    public function setIsAdmin($isAdmin)
        {
        $this->isAdmin = $isAdmin;
        }

    public function getIsPowerUser()
        {
        return $this->isPowerUser;
        }

    public function setIsPowerUser($isPowerUser)
        {
        $this->isPowerUser = $isPowerUser;
        }

    public function getIsUser()
        {
        return $this->isUser;
        }

    public function setIsUser($isUser)
        {
        $this->isUser = $isUser;
        }

    public function getFirstName()
        {
        return $this->firstName;
        }

    public function setFirstName($firstName)
        {
        $this->firstName = $firstName;
        }

    public function getLastName()
        {
        return $this->lastName;
        }

    public function setLastName($lastName)
        {
        $this->lastName = $lastName;
        }



    }

?>
