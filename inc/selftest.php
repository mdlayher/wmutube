<?php
	// selftest.php - Khan Academy Workflow, 2/11/13
	// Simple testing script which runs selftest on all classes to ensure they function properly
	//
	// changelog:
	//
	// 2/11/13 MDL:
	//	- initial code

	require_once __DIR__ . "/class_config.php";
	config::load("database");

	config::load("user");
	user::selftest();

	config::load("video");
	video::selftest();

	config::load("course");
	course::selftest();

	config::load("question");
	question::selftest();

	config::load("answer");
	answer::selftest();
?>
