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
            $query="select count(*) as count from librarianmap where libmuserID = $inID";
            $result = mysqli_query($dbc, $query) or die('gah!- query issues.'.mysqli_error().$query);
            if(!$result){echo "this is an outrage: ".mysqli_error()."\n";}
            while ( $row = mysqli_fetch_assoc( $result) )
                {
                    if ($row['count']==1)
                    {
                     $this->isLibrarian=true;
                     $this->setLibrarianName($inID);
                    }
                }
       }
   public function setLibrarianName($inID)
         {
         $dbc=$this->getConnection();
         $query = "select p.ppleLName as LName, p.ppleFName as FName, l.libmID as ID from people p, librarianmap l where  l.libmuserID= $inID and p.ppleID=l.libmppleID";
         $result = mysqli_query($dbc, $query) or die('crustacean!- query issues.'.$query.'<br>'.mysqli_error());
          if(!$result){echo "this is an outrage: ".mysqli_error()."\n";}

            while ( $row = mysqli_fetch_assoc( $result) )
            {
                $this->lastName=$row['LName'];
                $this->firstName = $row['FName'];
                $this->librarianID=$row['ID'];
            }
           $this->firstLastName=$this->firstName.' '.$this->lastName;
           $this->closeConnection($dbc);

         }

     public function getMyAssessments($inID)
         {
            $dbc=$this->getConnection();
             $query ='select '.
                     's.sesdID as SessionID, '.
                     'cp.crspName as CoursePrefix, '.
                     's.sesdCourseNumber as CourseNumber, '.
                     's.sesdCourseSection as CourseSection, '.
                     's.sesdSessionSection as SessionSection, '.
                     's.sesdDate as Date, '.
                     'ot.otctID as OutcomeTaughtID, '.
                     'CONCAT(od.otcdotchID, od.otcdName) as OutcomeName, '.
                     'oa.otcaID as OutcomeID, '.
                     'oa.otcaMet as Met, '.
                     'oa.otcaPartial as Partial, '.
                     'oa.otcaNotMet as NotMet, '.
                     'oa.otcaNotAssessed as NotAssessed '.
                     'from '.
                     'sessiondesc s, '.
                     'courseprefix cp, '.
                     'outcomestaught ot, '.
                     'outcomedetail od, '.
                     'outcomesassessed oa '.
                     'where '.
                     's.sesdlibmID = '.$inID.
                     ' and s.sesdAssessed = "yes" '.
                     'and s.sesdcrspID = cp.crspID '.
                     'and ot.otctsesdID = s.sesdID '.
                     'and ot.otctotcdID = od.otcdID '.
                     'and oa.otcaotctID = ot.otctID '.
                     'order by '.
                     'CoursePrefix, '.
                     'CourseNumber, '.
                     'CourseSection, '.
                     'outcomeName';

             // 8 columns.
             $output = '<table id="myAssessments"><thead id="myAssessmentsHead"><tr>'.



                    // *** for dataTables grouping addOn                  ***
                    "<th>Course</th>".
                    // ***                                                ***

                     '<th>Semester</th>'.
                    // '<th>Course</th>'.
                     '<th>Outcome</th>'.
                     '<th>Met</th>'.
                     '<th>Partially Met</th>'.
                     '<th>Not Met</th>'.
                     '<th>Not Assessed</th>'.
                     '</tr></thead><tbody>';

             $result = mysqli_query($dbc, $query) or die('This is an outrage-in function getMyAssessment query issues.'.$query);
                if(!$result){echo "this is an outrage: ".mysqli_error()."\n $query";}


                while ( $row = mysqli_fetch_assoc( $result) )
                {
                    $sessionID=$row['SessionID'];
                    $coursePrefix=$row['CoursePrefix'];
                    $courseNumber=$row['CourseNumber'];
                    $courseSection=$row['CourseSection'];
                    $sessionSection=$row['SessionSection'];

                    $date=$row['Date'];
                    $sessionDate=  toUSDate($date);
                    $semester=  toSemester($date);

                    $outcomeTaughtID=$row['OutcomeTaughtID'];
                    $outcomeName=$row['OutcomeName'];
                    $outcomeID=$row['OutcomeID'];
                    $met=$row['Met'];
                    $partial=$row['Partial'];
                    $notMet=$row['NotMet'];
                    $notAssessed=$row['NotAssessed'];

                    if($notAssessed=='1'){$notAssessed="n/a";}
                        else {$notAssessed="Assessed";}


                    $output.="<tr class='myAssessments'>".

                            // *** for dataTables grouping addOn                 ***
                            "<td class='myAssessments otcdID$outcomeID coursePrefix'>$coursePrefix $courseNumber-$courseSection $sessionSection    $semester</td>".
                            // ***                                               ***

                            "<td class='myAssessments otcdID$outcomeID semester'>$semester</td>".
                           // "<td class='myAssessments otcdID$outcomeID coursePrefix'>$coursePrefix $courseNumber-$courseSection $sessionSection</td>".
                            "<td class='myAssessments otcdID$outcomeID outcomeName'>$outcomeName</td>".
                            "<td class='myAssessments otcdID$outcomeID met'>$met</td>".
                            "<td class='myAssessments otcdID$outcomeID partial'>$partial</td>".
                            "<td class='myAssessments otcdID$outcomeID notMet'>$notMet</td>".
                            "<td class='myAssessments otcdID$outcomeID notAssessed'>$notAssessed</td></tr>";

                }

                $output.='</tbody></table>';
                return $output;

         }
     public function getMySessions($inID)
         {
            $dbc=$this->getConnection();
             $query ='select '.
                     's.sesdID as sessionID, '.
                     'cp.crspName as CoursePrefix, '.
                     's.sesdCourseNumber as CourseNumber, '.
                     's.sesdCourseSection as CourseSection, '.
                     's.sesdCourseTitle as CourseTitle, '.
                     's.sesdSessionSection as SessionNum, '.
                     's.sesdDate as Date, '.
                     's.sesdFaculty as Faculty, '.
                     's.sesdOutcomeDone as OutcomeDone, '.
                     's.sesdAssessed as AssessedDone '.
                     'from '.
                     'sessiondesc s, '.
                     'courseprefix cp '.
                     'where '.
                     's.sesdlibmID = '.$inID.
                     ' and s.sesdcrspID = cp.crspID '.
                     'order by '.
                     'CoursePrefix, '.
                     'CourseNumber, '.
                     'CourseSection';


             $output = '<table id="mySessions"><thead id="mySessionsHead"><tr>'.
                     '<th>Course</th>'.
                     '<th>Title</th>'.
                     '<th>Faculty</th>'.
                     '<th>Session</th>'.
                     '<th>Semester</th>'.
                     '<th>Date</th>'.
                     '<th>Outcomes</th>'.
                     '<th>Assessed</th>'.
                     '<th>Delete</th>'.
                     '</tr></thead><tbody>';

             $result = mysqli_query($dbc, $query) or die('This is an outrage-in function getMySessions query issues.'.$query);
                if(!$result){echo "this is an outrage: ".mysqli_error()."\n $query";}


                while ( $row = mysqli_fetch_assoc( $result) )
                {
                    $sessionID = $row['sessionID'];
                    $coursePrefix=$row['CoursePrefix'];
                    $courseNumber=$row['CourseNumber'];
                    $courseSection=$row['CourseSection'];
                    $courseTitle=$row['CourseTitle'];
                    $sessionNumber=$row['SessionNum'];

                    $date=$row['Date'];
                    $sessionDate = toUSDate($date);
                    $semester =  toSemester($date);

                    $faculty=$row['Faculty'];
                    $outcomeDone=$row['OutcomeDone'];
                    $AssessedDone=$row['AssessedDone'];



                    $output.="<tr class='mySessions'>".
                            "<td class='coursePrefix'>$coursePrefix $courseNumber-$courseSection</td>".
                            "<td class='courseTitle' >$courseTitle</td>".
                            "<td>$faculty</td>".
                            "<td>$sessionNumber</td>".
                            "<td>$semester</td>".
                            "<td class='dateCell'>$sessionDate</td>".
                            "<td class='sqlDateCel'>$date</td>".
                            "<td>$outcomeDone</td>".
                            "<td>$AssessedDone</td>".
                            "<td><a href='enterSession.php?sesdID=$sessionID'>edit</a>&nbsp;<form method='post' action='deleteSession.php'>".
                            "<input  type='hidden' value='$sessionID' name='inID'>".
                            "<input class='areYouSure inID$sessionID' type='submit' value='X' name='deleteMe'></form></tr>";

                }

                $output.='</tbody></table>';
                return $output;

         }
     public function getNeedAssessed($inID)
         {
         $dbc=$this->getConnection();

         $query = "select  c.crspName as Name, count(s.sesdcrspID) as Count ".
                        "from sessiondesc s, courseprefix c ".
                        "where sesdlibmID= $inID and ".
                        "sesdOutcomeDone='yes' and sesdAssessed='no' and s.sesdcrspID=c.crspID group by s.sesdcrspID";

                $result = mysqli_query($dbc, $query) or die('This is an outrage- in getNeedAssessment - query issues. <br>'.$query);
                if(!$result){echo "this is an outrage -in getNeedAssessment-: ".mysqli_error()."\n".$query;}

                //fill array with prefix as key and count needing outcomes as value
                $counts = array();
                while ( $row = mysqli_fetch_assoc( $result) )
                {
                    $counts[trim($row['Name'])]=$row['Count'];
                }




        $query ="select ".
                "sd.sesdID as ID, ".
                " sd.sesdcrspID as prefixID, ".
                "cp.crspName as prefixName, ".
                "sd.sesdCourseNumber as courseNumber, ".
                "sd.sesdCourseTitle as courseTitle, ".
                "sd.sesdCourseSection as courseSection, ".
                "sd.sesdSessionSection as sessionSection, ".
                "sd.sesdDate as sessionDate ".
                "FROM sessiondesc sd, courseprefix cp ".
                "where sd.sesdlibmID = $inID AND ".
                "cp.crspID=sd.sesdcrspID AND ".
                "sd.sesdAssessed='no' AND sd.sesdOutcomeDone='yes'".
                "order by sd.sesdcrspID, sd.sesdCourseSection, sd.sesdDate";
        $result = mysqli_query($dbc, $query) or die('dang it to heck!- query issues.'.mysqli_error().$query);
        if(!$result){echo "this is an outrage: ".mysqli_error().$query."\n";}


        $output='<ul>DanaJamesPlaceholder</ul><div id="courseByPrefix" class="empty">';
        $currentPrefixSection='none';

        while ( $row = mysqli_fetch_assoc( $result) )
            {
                $id=$row['ID'];
                $prefixID=$row['prefixID'];
                $prefixName=trim($row['prefixName']);
                $courseNumber=$row['courseNumber'];
                $courseSection=$row['courseSection'];
                $courseTitle=$row['courseTitle'];
                $sessionSection=$row['sessionSection'];
                $sessionDate=date("m/d/Y", strtotime($row['sessionDate']));

                if($prefixName!=$currentPrefixSection)
                    {
                    $hrefString='<li><a href="#'.$prefixName.'">'.$prefixName.'<span class="needAssessment">'.$counts[$prefixName].'</span></a></li>DanaJamesPlaceholder';
                    $output = str_replace('DanaJamesPlaceholder', $hrefString, $output);


                    if ($currentPrefixSection=='none'){ $output.='</div> <div id="'.$prefixName.'" class="assessmentList xxx">';}
                    else {$output.='</tbody></table></div> <div id="'.$prefixName.'" class="assessmentList">';}



                    $currentPrefixSection=$prefixName;
                     $output.='<table id="'.$prefixName.'Table" class="assessmentNeeded sortable">'.
                            '<thead><tr><th>Course</th><th>Title</th><th>Session#</th><th>Date</th></tr></thead><tbody>';
                    }

                    $output.='<tr><td><span class="assessmentNeeded">'.$prefixName.''.$courseNumber.'-'.$courseSection.'</span></td>'.
                            '<td>'.$courseTitle.'</td>'.
                            '<td>'.$sessionSection.'</td>'.
                            '<td>'.$sessionDate.'</td>'.
                            '<td><form action="assessOutcome.php" method="post">'.
                                '<input type="hidden" name="assessID" class="assessmentNeeded '.$prefixName.'" value="'.$id.'" />'.
                                '<input type="submit" class="assessOutcomeButton" name="assessMe" value="Go" /></form></td></tr>';
                           ;


            }
            $output.='</table></div>';
            $output = str_replace('DanaJamesPlaceholder', '', $output);
            return $output;
         }
     public function getNeedOutcomes($inID)
        {
        $dbc=$this->getConnection();


                $query = "select  c.crspName as Name, count(s.sesdcrspID) as Count ".
                        "from sessiondesc s, courseprefix c ".
                        "where sesdlibmID= $inID and ".
                        "sesdOutcomeDone='no' and s.sesdcrspID=c.crspID group by s.sesdcrspID";

                $result = mysqli_query($dbc, $query) or die('This is an outrage- query issues.');



                if(!$result){echo "this is an outrage: ".mysqli_error()."\n";}

                //fill array with prefix as key and count needing outcomes as value
                $counts = array();
                while ( $row = mysqli_fetch_assoc( $result) )
                {
                    $counts[trim($row['Name'])]=$row['Count'];
                }








        $query ="select ".
                "sd.sesdID as ID, ".
                " sd.sesdcrspID as prefixID, ".
                "cp.crspName as prefixName, ".
                "sd.sesdCourseNumber as courseNumber, ".
                "sd.sesdCourseTitle as courseTitle, ".
                "sd.sesdCourseSection as courseSection, ".
                "sd.sesdSessionSection as sessionSection, ".
                "sd.sesdDate as sessionDate ".
                "FROM sessiondesc sd, courseprefix cp ".
                "where sd.sesdlibmID = $inID AND ".
                "cp.crspID=sd.sesdcrspID AND ".
                "sd.sesdOutcomeDone='no' ".
                "order by sd.sesdcrspID, sd.sesdCourseSection, sd.sesdDate";
        $result = mysqli_query($dbc, $query) or die('dang it to heck!- query issues.'.mysqli_error().$query);
        if(!$result){echo "this is an outrage: ".mysqli_error().$query."\n";}



        $output='<ul>DanaJamesPlaceholder</ul><div id="courseByPrefix" class="empty">';
        $currentPrefixSection='none';

        while ( $row = mysqli_fetch_assoc( $result) )
            {
                $id=$row['ID'];
                $prefixID=$row['prefixID'];
                $prefixName=trim($row['prefixName']);
                $courseNumber=$row['courseNumber'];
                $courseSection=$row['courseSection'];
                $courseTitle=$row['courseTitle'];
                $sessionSection=$row['sessionSection'];
                $sessionDate=date("m/d/Y", strtotime($row['sessionDate']));

                if($prefixName!=$currentPrefixSection)
                    {
                    $hrefString='<li><a href="#'.$prefixName.'">'.$prefixName.'<span id="span'.$prefixName.'" class="needOutcomes">'.$counts[$prefixName].'</span></a></li>DanaJamesPlaceholder';
                    $output = str_replace('DanaJamesPlaceholder', $hrefString, $output);

                    if ($currentPrefixSection=='none'){ $output.='</div> <div id="'.$prefixName.'" class="outcomesList xxx">';}
                    else {$output.='</tbody></table></div> <div id="'.$prefixName.'" class="outcomesList xxx">';}

                    $output.='<h4 class="xxx outcomesList">'.$prefixName.' Courses</h4> ';

                    $output.='<input type="checkbox" name="checkAll" class="checkAll '.$prefixName.'" value="'.$prefixName.'" />'.
                            '<span class="xxx outcomesNeeded">Check all '.$prefixName.' courses</span><br class="outcomesNeeded" /><br class="outcomesNeeded" />';

                    $currentPrefixSection=$prefixName;
                     $output.='<table id="'.$prefixName.'Table" class="outcomesNeeded xxx sortable">'.
                            '<thead><tr><th>Course</th><th>Title</th><th>Session#</th><th>Date</th></tr></thead><tbody>';
                    }

                    $output.='<tr><td><input type="checkbox" name="outcomesNeeded[]" class="outcomesNeeded '.$prefixName.'" value="'.$id.'" />'.
                    '<span class="outcomesList">'.$prefixName.''.$courseNumber.'-'.$courseSection.'</span></td>'.
                            '<td>'.$courseTitle.'</td>'.
                            '<td>'.$sessionSection.'</td>'.
                            '<td>'.$sessionDate.'</td></tr>';
                           ;


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
