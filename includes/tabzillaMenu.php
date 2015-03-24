<div id="slider" class="closed">

	<div id="tabzilla-panel" tabindex="-1" style="height: auto;">
		<div id="tabzilla-contents" style="display:none;">
			<div id="tabzilla-promo">
				<div id="tabzilla-promo-mobile" class="snippet">
					<a href="#">
						<h4>Assessment</h4>
						<h4>Menu</h4>
						<p>See options to the right&nbsp;»</p>
					</a>
				</div>
			</div>
			<div id="tabzilla-nav">
				<ul>
					<li>
						<h2>Session Requests</h2>
						<ul>
							<li><a href="requestSession.php">Request new IL session</a></li>
							<li><a href="requestList.php">View existing IL requests</a></li>
						</ul>
					</li>
					<li>
						<h2>Enter Data</h2>
						<ul>
							<li><a href="enterSession.php">Enter Session</a></li>
							<li><a href="enterOutcomes.php">Enter Outcomes Taught</a></li>
							<li><a href="assessOutcomes.php">Assess Outcomes Taught</a></li>
						</ul>
					</li>
					<li>
						<h2>My Reports</h2>
						<ul>
							<li><a href="mySessions.php">My Sessions</a></li>
							<li><a href="myAssessments.php">My Assessments</a></li>
							<li><a href="reportSessionsByLength.php">Sessions by Length</a></li>
							<!-- <li><a href="reportSessionsByLengthTable.php">Sessions by Length tbl</a></li>    -->
						</ul>
					</li>
					<li>
						<h2>Admin</h2>
						<ul>
							<li><a href="adminReports.php">Admin Reports</a></li>
							<?php
								if ($_SESSION['roleID'] == 1) {
									echo '<li><a href="userAdmin.php">Manage users</a></li>';
									echo '<li><a href="resourceManage.php">Manage resources</a></li>';
									echo '<li><a href="outcomeList.php">Manage outcomes</a></li>';
								}
							?>
						</ul>
					</li>
				</ul>
			</div>
		</div>
	</div>

	<?php
		include('includes/notifications.php');
		$notifications = 0;
	?>
	<div id="notifications-panel" style="height: auto;">
		<div id="notifications-contents" style="display:none;">
			<div id="tabzilla-promo">
				<div id="tabzilla-promo-mobile" class="snippet">
					<a href="#">
						<h4>Assessment</h4>
						<h4>Notifications</h4>
						<p>See options to the right&nbsp;»</p>
					</a>
				</div>
			</div>
			<div id="notification-list" class="left">
					<?php
						$ns = getNotifications();
						foreach ($ns as $n) {
							if ($n['unread']) {
								$notifications++;
								echo '<a class="notification-item notification-unread" href="'.$n['link'].'">';
							} else {
								echo '<a class="notification-item" href="'.$n['link'].'">';
							}
							echo '<div>'.$n['text'].'</div>';
							echo '<div class="light">'.$n['datetime'].'</div>';
							echo '</a>';
						}
					?>
			</div>
		</div>
	</div>

</div>

<?php
	echo '<a class="tab menu"><div>menu</div></a>';
	if ($notifications > 0) {
		echo '<a class="tab notifications symbol glow"><div>&#128276;'.$notifications.'</div></a>';
	} else {
		echo '<a class="tab notifications symbol"><div>&#128276;</div></a>';
	}
?>
