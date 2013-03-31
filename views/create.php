<?php
	require_once "header.inc.php";
?>
<link href="http://vjs.zencdn.net/c/video-js.css" rel="stylesheet"/>
<script src="./js/jquery.transit.min.js" type="text/javascript"></script>
<script src="./js/create.js" type="text/javascript"></script>
<script src="http://vjs.zencdn.net/c/video.js"></script>
<script src="./js/jquery.transit.min.js" type="text/javascript"></script>
<script src="./js/jquery.scrollTo.js"></script>
<script type="text/javascript" src="./js/jquery.uploadify-3.1.min.js"></script>
<div id="body_container">
	<span id="error"></span>
	<div class="centered_on_page section" id="step1">
		<a class="button topRight nextButton">Next step</a>
		<div class="stepbody">
			<div class="inner">
				<p class="top">
					Start by uploading a video.
				</p>
				<p class="bottom">
					<input type="file" id="file_upload" class='' href="#"></input> mp4 only, please!
				</p>
			</div>
		</div>
	</div>

	<div class="centered_on_page section initiallyHidden" id="step3">
		<a class="button topRight nextButton">Next step</a>
		<div class="stepbody">
			<div class="inner">
				<div class="top">
					Tell us a little about your video...
				</div>
				<div class="video_info_bottom">
					<label class="stack listitem_label">Video title</label>
					<input id="video_title" class="stack textbox q_answer" type="text"/>
					<label class="stack listitem_label">Video description</label>
					<input id="video_title" class="stack textbox q_answer" type="text"/>
					<label class="stack listitem_label">Department</label>
					<input id="video_title" class="stack textbox q_answer" type="text"/>
					<label class="stack listitem_label">Subject</label>
					<input id="video_title" class="stack textbox q_answer" type="text"/>
					<label class="stack listitem_label">Year</label>
					<input id="video_title" class="stack textbox q_answer" type="text"/>
				</div>
			</div>
		</div>
	</div>

	<div class="centered_on_page initiallyHidden" id="step2">
		<div class="player_container">
			<!--controls-->
			<video class="video-js vjs-default-skin" data-setup="{}" id="video_player" poster="./img/player_placeholder.png" preload="auto">
				<source src="http://herpderp.me/video/Chrome_ImF.webm" type="video/webm"></source>
			</video>
		</div>
		<div class="editing_scrubber centered_on_page">
			<img class="player_control_img" id="player_playpause" src="./img/controller-play.png"/>
			<div class="editing_scrubber_basebar">
			</div>
			<div class="editing_scrubber_progress">
			</div>
			<div class="editing_scrubber_buffer">
			</div>
			<div class="editing_scrubber_qmarker"></div>
		</div>
		<div id="step2_editor">
			<div class="section_step2 question">
				<div class="leftRightButtonWrapper">
					<span class="previous">&lt</span>
					<span class="next">&gt</span>
				</div>
				<div class="floatLeft">
					<div class="form_section_container">
						<label class="stack listitem_label">Question body</label>
						<input class="stack textbox q_body" placeholder="What is the diameter of the sun?" type="text"/>
					</div>

					<div class="form_section_container">
						<label class="stack listitem_label">Answers / Correct?</label>
						<div class="answer">
							<input class="stack textbox q_answer" placeholder="Hello" type="text"/>
							<input type='radio' name='correct' class='floated_checkbox'>
						</div>
						<div class="answer">
							<input class="stack textbox q_answer" placeholder="Hello" type="text"/>
							<input type='radio' name='correct' class='floated_checkbox'>
						</div>
						<div class="answer">
							<input class="stack textbox q_answer" placeholder="Hello" type="text"/>
							<input type='radio' name='correct' class='floated_checkbox'>
						</div>
						<div class="answer">
							<input class="stack textbox q_answer" placeholder="Hello" type="text"/>
							<input type='radio' name='correct' class='floated_checkbox'>
						</div>
					</div>
				</div>
				

				<div class="floatLeft">
					<div class="form_section_container">
						<label class="stack listitem_label">Presentation time</label>
						<input class="stack textbox q_time" type="text"/>
					</div>
					<div class="form_section_container">
						<label class="stack listitem_label">Hint</label>
						<textarea class="stack textbox q_hint" placeholder="Hello" rows="3" type="text"></textarea>
					</div>
					<div class="form_section_container"><a class="button submitButton">Submit</a><a class="button addAnother">Add another</a></div>
				</div>
				

			</div>
		</div>
	</div>
	<div class="bottom_spacer"></div>
</div>
<?php
	require_once "footer.inc.php";
?>
