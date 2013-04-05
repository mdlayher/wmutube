<?php
	require_once "header.inc.php";
?>

<link href="http://vjs.zencdn.net/c/video-js.css" rel="stylesheet">
<script src="http://vjs.zencdn.net/c/video.js"></script>

<link rel="stylesheet" type="text/css" href="<?= $root_uri ?>/css/watch.css">
<script src="<?= $root_uri ?>/js/jquery.scrollTo.js"></script>
<script src="<?= $root_uri ?>/js/watch.js" type="text/javascript"></script>

<div class="body_fade"></div>
<div class="quiz_container">
	<header>Quiz</header>
	<hr>
	<div class='question left'>
		<div>
			<p>Which of these is a member of the seven wonders of the world?</p>
			<p>Lorem ipsum Magna in proident do amet incididunt incididunt proident laborum pariatur cupidatat enim nostrud elit do Excepteur laboris magna commodo exercitation dolore ut nisi minim Duis nulla occaecat nostrud.</p>
		</div>
	</div>
	<div class="answers left">
		<div>
			<p>Answers:</p>
			<div class="answer">
				<div class="radio"></div>
				<div class="answer_text">Hello world</div>
			</div>
			<div class="answer">
				<div class="radio selected"></div>
				<div class="answer_text">Another answer</div>
			</div>
			<div class="answer">
				<div class="radio"></div>
				<div class="answer_text">Cool man</div>
			</div>
			<div class="answer">
				<div class="radio"></div>
				<div class="answer_text">It works!</div>
			</div>
		</div>
	</div>
	<footer>
		<div id="quiz_submit" class="button right">Submit</div>
	</footer>
</div>

<div id="body_container">
	<div class="player_container centered_on_page">
		<h1><header>CS 2240 - Programming in C</header></h1>
		<video class="video-js vjs-default-skin" data-setup="{}" id="video_player" autoplay poster="<?= $root_uri ?>/img/player_placeholder.png" preload="auto" controls>
			<source src="http://herpderp.me/video/Chrome_ImF.webm" type="video/webm"></source>
		</video>
	</div>
</div>
<?php
	require_once "footer.inc.php";
?>
