<?php
	// selftest.php - Khan Academy Workflow, 2/11/13
	// Simple testing script which runs selftest on all classes to ensure they function properly
	//
	// changelog:
	//
	// 2/11/13 MDL:
	//	- initial code

	require_once "class_user.php";
	user::selftest();

	require_once "class_video.php";
	video::selftest();

	require_once "class_course.php";
	course::selftest();
?>
