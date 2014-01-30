<div id="tabzilla-panel" class="tabzilla-closed open" tabindex="-1" style="height: auto;">
    <div id="tabzilla-contents">
        <div id="tabzilla-promo">
            <div id="tabzilla-promo-mobile" class="snippet">
                <a href="#">     <h4>Assessment</h4>
                    <h4>Menu</h4>
                    <p>See options to the right&nbsp;»</p>
                </a>
            </div>
        </div>

        <div id="tabzilla-nav">

            <ul>
                <li><h2>Enter Data</h2>
                    <ul>
                        <li><a href="enterSession.php">Enter Session</a></li>
                        <li><a href="enterOutcomes.php">Enter Outcomes Taught</a></li>
                        <li><a href="assessOutcomes.php">Assess Outcomes Taught</a></li>
                    </ul>
                </li>
                <li><h2>My Reports</h2>
                    <ul>
                        <li><a href="mySessions.php">My Sessions</a></li>
                        <li><a href="myAssessments.php">My Assessments</a></li>
                        <li><a href="reportSessionsByLength.php">Sessions by Length</a></li>
                       <!-- <li><a href="reportSessionsByLengthTable.php">Sessions by Length tbl</a></li>    -->
                    </ul>
                </li>
                <li><h2>&nbsp;</h2>
                    <ul>
                        <li><a href="#">&nbsp;</a></li>
                        <li><a href="#">&nbsp;</a></li>
                        <li><a href="#">&nbsp;</a></li>
                    </ul>      </li>
                <li><h2>Admin</h2>
                    <ul>
                        <li><a href="adminReports.php">Admin Reports</a></li>
												<?php
													if($_SESSION['roleID'] == 1) {
														echo '<li><a href="userAdd.php">Add user</a></li>';
														echo '<li><a href="userAdmin.php">Manage users</a></li>';
													}
												?>
                    </ul>
                </li>
            </ul>
        </div>

    </div>

</div>