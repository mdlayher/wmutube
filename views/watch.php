<?php
	require_once "header.inc.php";
?>
<link href="http://vjs.zencdn.net/c/video-js.css" rel="stylesheet">
<script src="http://vjs.zencdn.net/c/video.js"></script>
<link rel="stylesheet" type="text/css" href="<?= $root_uri ?>/css/watch.css">
<script src="<?= $root_uri ?>/js/jquery.scrollTo.js"></script>
<script type="text/javascript">var questions = 
<?php
	// Pull associated questions, if they exist
	$q = $video->to_array();
	if (!empty($q["questions"]))
	{
		printf("%s;", json_encode($q["questions"]));
	}
	else
	{
		echo "{};";
	}
?>
</script>
<script src="<?= $root_uri ?>/js/watch.js" type="text/javascript"></script>
<div class="body_fade"></div>
<div class="quiz_container">
	<div class="quiz_result incorrect">
		<div><p class="bottom"></p></div>
		<div><p class="top"></p></div>
		<div id="result_confirm" class='button'>OK</div>
	</div>
	<header>Quiz</header>
	<hr>
	<div class='question left'>
		<div></div>
	</div>
	<div class="answers left">
		<div><p>Answers:</p></div>
	</div>
	<footer>
		<div id="quiz_submit" class="button right">Submit</div>
	</footer>
</div>
<div id="body_container">
	<span id="error"></span>
	<div class="player_container centered_on_page">
		<h1><header><?= $video->get_title() ?></header></h1>
		<video class="video-js vjs-default-skin" data-setup="{}" id="video_player" autoplay poster="<?= $root_uri ?>/img/player_placeholder.png" preload="auto" controls>
			<source src="<?= $root_uri . "/uploads/" . $video->get_filename() ?>" type="video/mp4"></source>
		</video>
	</div>
</div>
<?php
	require_once "footer.inc.php";
?>
