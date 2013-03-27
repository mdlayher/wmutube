<?php
	require_once "header.inc.php";
?>
<div id="body_container">
	<span id="error"></span>
	<div class="centered_on_page"><h1>Welcome to <?= $project_title ?>!</h1></div>
	<div>
		<pre>
<?= print_r($_SESSION, true); ?>
		</pre>
	</div>
</div>
<?php
	require_once "footer.inc.php";
?>
