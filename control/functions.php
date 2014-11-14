<?php

include('control/globalVars.php');

function getMonth($inMySQLDate){}
function getDay($inMySQLDate){}
function getYear($inMySQLDate){}

function getDateArray($inMySQLDate){
    
        $dateDelimited = explode('-', $inMySQLDate);
        $dateArray = array();
        $dateArray['Year']=$dateDelimited[0];
        $dateArray['Month']=$dateDelimited[1];
        $dateArray['Day']=$dateDelimited[2];
        
        return $dateArray;
        
}



function toSemester($inMySQLDate){
    
    $dateArray=  getDateArray($inMySQLDate);
    $calendarYear = $dateArray['Year'];
    $calendarMonth = $dateArray['Month'];
    $calendarDay = $dateArray['Day'];
    $semesterSeason = 'notSet';
    
            if ( intval($calendarMonth) >= 1)
                {
                $semesterSeason="Spring";
                }

            if ( intval($calendarMonth) >= 6)
                {
                $semesterSeason="Summer";
                }

            if ( intval($calendarMonth) >= 8)
                {
                $semesterSeason="Fall";
                }
    
    $semester = $semesterSeason.' '.$calendarYear;
    
    return $semester;
    
}






function toUSDate ($inMySQLDate){
        $dateArray= getDateArray($inMySQLDate);
        
        $USDate= $dateArray['Month'].'/'.$dateArray['Day'].'/'.$dateArray['Year'];
        
        return $USDate;
}
function toWeekNumber ($inMySQLDate){
    $date=strtotime($inMySQLDate);
    $weekNumber = date("W", $date);
    return $weekNumber;
}
function toFiscalYear($inMySQLDate){
    
    $dateArray=  getDateArray($inMySQLDate);
    $calendarYear = $dateArray['Year'];
    $calendarMonth = $dateArray['Month'];
    $calendarDay = $dateArray['Day'];
    $fiscalYearPart1 = 'notSet';
    $fiscalYearPart2 = 'alsoNotSet';
    
    $fiscalYearPart1 = $calendarYear;
    $fiscalYearPart2 = strval(intval($calendarYear)-1);
    
    
            if ( intval($calendarMonth) >= 7)
                {
                $fiscalYearPart1 = $calendarYear;
                $fiscalYearPart2 = strval(intval($calendarYear)+1);
                }

           
    
    $fiscalYear = 'FY '.$fiscalYearPart1.'-'.$fiscalYearPart2;
    
    return $fiscalYear;
    
}


function toAcademicYear($inMySQLDate){  //this will need to be updated to be more exact and make use of "day"
    
   // $thisDate = strtotime($inMySQLDate);
    
    $dateArray=  getDateArray($inMySQLDate);
    $calendarYear = $dateArray['Year'];
    $calendarMonth = $dateArray['Month'];
    $calendarDay = $dateArray['Day'];
    $academicYearPart1 = 'notSet';
    $academicYearPart2 = 'alsoNotSet';
    
    $academicYearPart1 = $calendarYear;
    $academicYearPart2 = strval(intval($calendarYear)-1);
    
    
            if ( (intval($calendarMonth) >= AY_END_MONTH) && ( intval($calendarDay)>=AY_END_DAY ) )
                {
                $academicYearPart1 = $calendarYear;
                $academicYearPart2 = strval(intval($calendarYear)+1);
                }

           
    
    $academicYear = 'AY '.$academicYearPart1.'-'.$academicYearPart2;
    
    return $academicYear;
    
    
    
}

function toHours($inMinutes){
    
    $hours = $inMinutes/60;
    return $hours;
}


function inSemester($semester, $colname) {
	
	$semester = strtolower($semester);

	switch ($semester) {
		case 'fall':
			return ' DAYOFYEAR('.$colname.') between DAYOFYEAR("0000-' . FALL_START_MMDD . '") and DAYOFYEAR("0000-' . FALL_END_MMDD . '") ';
			break;
		case 'spring':
			return ' DAYOFYEAR('.$colname.') between DAYOFYEAR("0000-' . SPRING_START_MMDD . '") and DAYOFYEAR("0000-' . SPRING_END_MMDD . '") ';
			break;
		case 'summer':
			return ' DAYOFYEAR('.$colname.') between DAYOFYEAR("0000-' . SUMMER_START_MMDD . '") and DAYOFYEAR("0000-' . SUMMER_END_MMDD . '") ';
			break;
		case 'any':
		default:
			return ' true ';
			break;
	}

}

function inFiscalYear($inFiscalYear){
    $fiscalYears = explode(" ", $inFiscalYear);
    $inFiscalYears = $fiscalYears[1];
    
    //return date-between clause for sql queries
    $dateBetweenString = 'initialValue';
    $inFiscalYears = explode("-",$inFiscalYears);
    $fyYear1 =$inFiscalYears[0];
    $fyYear2 = $inFiscalYears[1];
    
    $dateBetweenString = " between '".$fyYear1."-".FY_START_MMDD."' and '".$fyYear2."-".FY_END_MMDD."' ";
    
    return $dateBetweenString;
}

function inAcademicYearOld($inAcademicYear){
	$academicYears = explode(" ", $inAcademicYear);
	$inAcademicYears = $academicYears[1];

	//return date-between clause for sql queries
	$dateBetweenString = 'initialValue';
    $inAcademicYears = explode("-",$inAcademicYears);
    $ayYear1 =$inAcademicYears[0];
	$ayYear2 = $inAcademicYears[1];

    $dateBetweenString = " between '".$ayYear1."-".AY_START_MMDD."' and '".$ayYear2."-".AY_END_MMDD."' ";

	return $dateBetweenString;
}

function inAcademicYear($year, $colname){
	// Be aware that $year may be passed directly into an SQL query string, so
	// keep SQL injections in mind. is_numeric() should take care of us here.
	if (!is_numeric($year)) { return ' true '; }
	return ' '.$colname.' between "'.($year-1).'-'.AY_START_MMDD.'" and "'.$year.'-'.AY_END_MMDD.'" ';
}


?>
