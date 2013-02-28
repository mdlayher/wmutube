<?php
	// selftest.php - Khan Academy Workflow, 2/11/13
	// Simple testing script which runs selftest on all classes to ensure they function properly
	//
	// changelog:
	//
	// 2/26/13 MDL:
	//	- added selftest for question and answer classes
	// 2/11/13 MDL:
	//	- initial code

	require_once __DIR__ . "/class_config.php";
	config::load(array("database", "answer", "course", "question", "user", "video"));

	user::selftest();
	video::selftest();
	course::selftest();
	question::selftest();
	answer::selftest();
?>
