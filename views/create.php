<?php
	require_once "header.inc.php";
?>

<script type="text/javascript" src="./js/jquery.uploadify-3.1.min.js"></script>

<div id="body_container">
	<div class="centered_on_page section" id="step1">
		<a class="button topRight nextButton">Next step</a>
		<div class="stepbody">
			<div class="inner">
				<p class="top">
					Start by uploading a video.
				</p>
				<p class="bottom">
					Click <input type="file" id="file_upload" href="#">here</input> to browse. mp4 only, please!
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
				<div class="bottom">
					<div class="answer">
						<label class="stack listitem_label">Video title</label>
						<input class="stack textbox q_answer" type="text"/>
					</div>
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
		</div>
		<div id="step2_editor">
			<div class="section_step2 question">
				<a class="button topRight submitButton">Submit</a><a class="button topRight addAnother">Add another</a>
				<ul class="nostyle defaultPaddingMargins">
					<li class="step2_listitem">
					<div>
						<label class="stack listitem_label">Question body</label>
						<input class="stack textbox q_body" placeholder="What is the diameter of the sun?" type="text"/>
					</div>
					</li>
					<li class="step2_listitem">
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
					</li>
				</ul>
			</div>
		</div>
	</div>
	<div class="bottom_spacer"></div>
</div>
<?php
	require_once "footer.inc.php";
?>
