<?php
	// selftest.php - Khan Academy Workflow, 2/11/13
	// Simple testing script which runs selftest on all classes to ensure they function properly
	//
	// changelog:
	//
	// 2/11/13 MDL:
	//	- initial code

	require_once __DIR__ . "/class_database.php";

	config::load("user");
	user::selftest();

	config::load("video");
	video::selftest();

	config::load("course");
	course::selftest();
?>
