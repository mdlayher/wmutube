<?php
	require_once "header.inc.php";
?>
<div id="body_container">
	<span id="error"></span>
	<div class="centered_on_page">
		<h1>Welcome to <?= $project_title ?>!</h1>

		<p class="centered_on_page">This project is a collaborative effort between Computer Science Senior Design students
		<a href="https://github.com/justindhill">Justin Hill</a>, <a href="https://github.com/mdlayher">Matt Layher</a>,
		and <a href="https://github.com/ejleese">Eric Leese</a>, in an effort to build an
		"<a href="http://en.wikipedia.org/wiki/Flip_teaching">Inverted Classroom</a>" learning system for the
		<a href="http://cs.wmich.edu">Computer Science Department</a> at <a href="http://wmich.edu">Western Michigan University.</a></p>

		<p>WMU students: To get started, you may log in using your Bronco NetID and password.  From here, you can view
		any video on the site, and take interactive quizzes during each video.  Your scores will be recorded in order
		to better aid your instructors during future classes!</p>

		<p>WMU staff and faculty: To get started, please contact <a href="mailto:matthew.d.layher@wmich.edu">Matt Layher</a>
		to have your account created.  From here, you may upload videos and create interactive quizzes for your students.</a>
	</div>
</div>
<?php
	require_once "footer.inc.php";
?>
