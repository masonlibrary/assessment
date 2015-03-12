<?php

	include('control/connection.php');

	function notify($userid, $text, $longtext, $link) {

		global $dbc;

		$email = '';
		$stmt = mysqli_prepare($dbc, '
			select ppleEmail from users u
				left outer join librarianmap l on u.userID = l.libmuserID
				left outer join people p on l.libmppleID = p.ppleID
				where u.userID = ?');
		mysqli_stmt_bind_param($stmt, 'i', $userid);
		mysqli_stmt_execute($stmt) or die('Failed to retrieve email address: ' . mysqli_error($dbc));
		mysqli_stmt_store_result($stmt);
		mysqli_stmt_bind_result($stmt, $email);
		mysqli_stmt_fetch($stmt);

		mail($email, $text, $longtext);

		mysqli_stmt_free_result($stmt);

		$stmt = mysqli_prepare($dbc, 'insert into notifications (target, text, link, datetime) values (?, ?, ?, now())');
		mysqli_stmt_bind_param($stmt, 'iss', $userid, $text, $link);
		mysqli_stmt_execute($stmt) or die('Failed to add notification: ' . mysqli_error($dbc));

	}

	function getNotifications() {

		global $dbc;

		$lastActive = '';
		$stmt = mysqli_prepare($dbc, 'select userLastActive from users where userID = ?');
		mysqli_stmt_bind_param($stmt, 'i', $_SESSION['userID']);
		mysqli_stmt_execute($stmt) or die('Failed to get user last active datetime: ' . mysqli_error($dbc));
		mysqli_stmt_store_result($stmt);
		mysqli_stmt_bind_result($stmt, $lastActive);
		mysqli_stmt_fetch($stmt);
		mysqli_stmt_free_result($stmt);

		$row = array();
		$result = array();
		$stmt = mysqli_prepare($dbc, 'select text, link, datetime from notifications where target = ? order by datetime desc') or die(mysqli_error($dbc));
		mysqli_stmt_bind_param($stmt, 'i', $_SESSION['userID']);
		mysqli_stmt_execute($stmt) or die('Failed to get notifications: ' . mysqli_error($dbc));
		mysqli_stmt_store_result($stmt);
		mysqli_stmt_bind_result($stmt, $row['text'], $row['link'], $row['datetime']);
		while (mysqli_stmt_fetch($stmt)) {
			$result[] = array(
				'text'     => $row['text'],
				'link'     => $row['link'],
				'datetime' => $row['datetime'],
				'unread'   => ($lastActive < $row['datetime']),
			);
		}
		mysqli_stmt_free_result($stmt);

		return $result;

	}

?>
