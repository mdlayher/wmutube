<?php
	require_once "header.inc.php";
?>
<script type="text/javascript">
	$(function()
	{
		$("#selftest_link").click(function()
		{
			$("#selftest_result").text("loading . . .");
			$.post("<?= $root_uri ?>/inc/test/selftest.php", function(data)
			{
				$("#selftest_result").text('\n' + data);
			});
		});

		$("#logintest_link").click(function()
		{
			$("#logintest_result").text("loading . . .");
			$.post("<?= $root_uri ?>/inc/test/login_test.php", function(data)
			{
				$("#logintest_result").text('\n' + data);
			});
		});
	});
</script>
<div id="body_container">
	<span id="error"></span>
	<div style="margin-left: 25%">
		<pre>
			<big>Session Dump</big>
<?= print_r($_SESSION, true); ?>
			<br />
			<big>Cache Version</big>

<?= print_r(cache::version(), true); ?>
			<br />
			<big><a href="#" id="selftest_link">Selftest</a></big>
			<span id="selftest_result"></span>
			<br />
			<big><a href="#" id="logintest_link">Login Test</a></big>
			<span id="logintest_result"></span>
		</pre>
		<br />
		<br />
		<br />
	</div>
</div>
<?php
	require_once "footer.inc.php";
?>
