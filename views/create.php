<doctype></doctype><html>
<head>
<link href="../css/main.css" rel="stylesheet" type="text/css"/>
<link href="http://vjs.zencdn.net/c/video-js.css" rel="stylesheet"/>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
<script src="../js/jquery.transit.min.js" type="text/javascript"></script>
<script src="../js/main.js" type="text/javascript"></script>
<script src="http://vjs.zencdn.net/c/video.js"></script>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
<script src="../js/jquery.transit.min.js" type="text/javascript"></script>
<script src="../js/jquery.scrollTo.js"></script>
<script type="text/javascript">| document.write('</' + 'script>')</script>
<title>Senior design prototype</title>
</head>
<body>
<a href="#" id="previous">Previous</a><a href="#" id="next">Next</a>
<div id="header_container">
	<div class="centered_on_page headerfooter" id="header">
		<div class="left">
			CourseraClone
		</div>
		<div class="leftish">
			<a href="#" id="videos_link">Videos</a>
		</div>
		<div class="right">
			Hello, Justin!
		</div>
	</div>
</div>
<div id="browse_drawer">
</div>
<div id="body_container">
	<div class="centered_on_page section" id="step1">
		<a class="button topRight nextButton">Next step</a>
		<div class="stepbody">
			<div class="inner">
				<p class="top">
					Start by uploading a video.
				</p>
				<p class="bottom">
					Click <a id="fileChooseLink" href="#">here</a> to browse. mp4 only, please!
				</p>
			</div>
		</div>
	</div>
	<div class="centered_on_page section initiallyHidden" id="description_container">
		<div class="padright">
			<div class="desc_body">
			</div>
		</div>
	</div>
	<div class="centered_on_page initiallyHidden" id="step2">
		<div class="player_container">
			<!--controls-->
			<video class="video-js vjs-default-skin" data-setup="{}" id="video_player" poster="../img/player_placeholder.png" preload="auto"><source src="http://herpderp.me/video/Chrome_ImF.webm" type="video/webm"></source></video>
		</div>
		<div class="editing_scrubber centered_on_page">
			<img class="player_control_img" id="player_playpause" src="../img/controller-play.png"/>
			<div class="editing_scrubber_basebar">
			</div>
			<div class="editing_scrubber_progress">
			</div>
			<div class="editing_scrubber_buffer">
			</div>
		</div>
		<div id="step2_editor">
			<div class="section_step2 question">
				<a class="button topRight nextButton">Next step</a><a class="button topRight addAnother">Add another</a>
				<ul class="nostyle defaultPaddingMargins">
					<li class="step2_listitem">
					<div>
						<label class="stack listitem_label">Question body</label><input class="stack textbox q_body" placeholder="What is the diameter of the sun?" type="text"/>
					</div>
					</li>
					<li class="step2_listitem">
					<div>
						<label class="stack listitem_label">Answers / Correct?</label><input class="stack textbox q_answer" placeholder="Hello" type="text"/><input class="stack textbox q_answer" placeholder="Hello" type="text"/><input class="stack textbox q_answer" placeholder="Hello" type="text"/><input class="stack textbox q_answer" placeholder="Hello" type="text"/>
					</div>
					</li>
				</ul>
			</div>
		</div>
	</div>
	<div class="centered_on_page section initiallyHidden" id="step3">
		<a class="button topRight submitButton">Submit</a>
	</div>
</div>
<div id="footer_container">
	<div class="centered_on_page headerfooter" id="footer">
		&copy Western Michigan University 2013
	</div>
</div>
</body>
</html>