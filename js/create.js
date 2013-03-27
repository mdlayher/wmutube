var wmutube = (function () {

	//document.write('<script src="http://' + (location.host || 'localhost').split(':')[0] + ':35729/livereload.js?snipver=1"></' + 'script>')
	
	// utility functions
	var util = (function () {
		
		var publicMembers = {
			formattedTimeWithSeconds: function () {
				var stringTime, hrs, mins, secs;

				stringTime = "";
				hrs = Math.floor(seconds / 3600);
				mins = Math.floor((seconds - (hrs * 3600)) / 60);
				secs = Math.floor(seconds - (hrs * 3600) - (mins * 60));

				if (hrs > 0) {
					stringTime += hrs + ":";
				}

				if (mins < 10 && hrs > 0) {
					stringTime += "0" + mins + ":";
				} else {
					stringTime += mins + ":";
				}

				if (secs < 10) {
					stringTime += "0" + secs;
				} else {
					stringTime += secs;
				}

				return stringTime;
			},
			generateGuid: function () {
				// courtesy of http://stackoverflow.com/questions/105034/how-to-create-a-guid-uuid-in-javascript
				var retVal = 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
					var r = Math.random()*16|0, v = c == 'x' ? r : (r&0x3|0x8);
					return v.toString(16);
				});

				return retVal;
			}
		};

		return publicMembers;
	})();

	var editor_step2 = (function () {

		const QSPACING = 50;

		// private members
		var qSelectors = [];
		var currentQ = null;
		var animating = false;

		// self refers to the element, not this object.
		function addAnother(self) {

			if (animating === true) {
				// console.log("Animation in progress, not adding.");
				return;
			} 
			animating = true;

			var newId = util.generateGuid();
			var oldQ = $('.question:last');
			var newQ = oldQ.clone(false);

			// adjust attributes of new object
			$(newQ).attr("id", newId);
			$(newQ).find(":input").each(function () { $(this).prop("checked", false).attr("name", newId) });
			$(newQ).css("margin-top", -($(oldQ).outerHeight()));
			$(newQ).find(".q_body:first").each(function () {$(this).val('');});;
			$(newQ).find(".q_answer").each(function () {$(this).val('');});

			// shove the old questions left
			$('.question').each(function (index) {
				$(this).transition({x: -($(this).outerWidth() + QSPACING), "opacity": .3}, 100, function () {
					var l = $(this).css("left") === 'auto' ? 0 : parseInt($(this).css("left"));
					console.log("l: " + l);

					// there must be a better way to do this...
					$(this).removeAttr('style');
					$(this).css('opacity', .3)
					$(this).css("left", l - ($(this).outerWidth() + QSPACING));

					if (index > 0) {
						$(this).css('margin-top', -($(this).outerHeight()));
					}
					animating = false;
					console.log(l - ($(this).width() + QSPACING));
					console.log('callback!');
				});
				console.log(this.id + ": " + $(this).css("left"));
			});

			// append the new question to the DOM
			qSelectors.push("#" + newId);
			currentQ = qSelectors.indexOf("#" + newId);
			$('#step2_editor').append(newQ);
		}

		function move (direction) {

			// -1 is next, 1 is previous

			if (currentQ == 0 && direction === 1 || currentQ + 1 == qSelectors.length && direction === -1) {
				return;
			}

			var movesLeft = $('.question').length;

			$('.question').each(function (index) {
				$(this).transition({x: direction * ($(this).outerWidth() + QSPACING), "opacity": .3}, 100, function () {
					var l = $(this).css("left") === 'auto' ? 0 : parseInt($(this).css("left"));

					// there must be a better way to do this...
					$(this).removeAttr('style');
					$(this).css('opacity', .3)
					$(this).css("left", l + direction * ($(this).outerWidth() + QSPACING));

					if (index > 0) {
						$(this).css('margin-top', -($(this).outerHeight()));
					}

					movesLeft--;
					if (movesLeft === 0) {
						currentQ = currentQ - direction;
						$(qSelectors[currentQ]).css("opacity", "1");
					}
				});
			});

			console.log(qSelectors);
			console.log(currentQ);
		}

		// public functions

		var publicMembers = {

		}

		// on document ready
		$(function () {
			$('#step2_editor').on('click', '.addAnother', function () {
				addAnother(this);
				console.log("called");
			});

			$("#previous").on('click', function (e) {
				e.preventDefault();
				move(1);
			});

			$("#next").on('click', function (e) {
				e.preventDefault();
				move(-1);
			});

			$('#step2').on("click", ".submitButton", function () {

				// the submission object
				var theObj = {};
				theObj.questions = [];

				var qi = 0;
				// iterate over the questions
				$(".question").each(function () {
					theObj.questions.push({"text": $(this).find(".q_body").first().val(), "answers": []});
					$(this).find(".answer").each(function () {
						// iterate over each question's answers.
						var kids = $(this).children();
						theObj.questions[qi].answers.push({"text": $(kids[0]).val(), "correct": $(kids[1]).is(":checked")});
					});
					qi++;
				});

				console.log(theObj);
			});

			var guid = util.generateGuid();
			$('.section_step2').attr('id', guid);
			qSelectors.push("#" + guid);
			currentQ = "#" + guid;
		});
	})();


	// on document ready
	$(function () {

		$('#file_upload').uploadify({
			'auto'				: true,
			'swf'				: './swf/uploadify.swf',
			'uploader'			: 'ajax/upload',
			'method'			: 'post',
			'fileTypeDesc'		: 'mp4 files',
			'fileTypeExts'		: '*.mp4',
			'onUploadSuccess'	: function (file, data, response) {
				console.log("upload was successful");
			},
			'onUploadError'		: function (file, errorCode, errorMsg, errorString) {
				console.log("upload failed");
				$("#step3").css("display", "block").transition({opacity: 1});
				$.scrollTo("#step3", 600, { offset: { top: -70 }});
			}
		});

		// on video.js ready
		_V_(video_player).ready(function () {
			var thePlayer = this;
			var lastTimeUpdate = 0;
			thePlayer.addEvent("timeupdate", function () {
				var update = Math.floor(thePlayer.currentTime());
				if (update !== lastTimeUpdate) {
					console.log(update);
				}

				var progressBar = $(".editing_scrubber_progress");
				var baseBar = $(".editing_scrubber_basebar");
				var timePercent = thePlayer.currentTime() / thePlayer.duration();
				//console.log(timePercent * progressBar.width());
				progressBar.transition({ width: (timePercent * baseBar.width()) }, 100, 'linear');
			});

			thePlayer.addEvent("progress", function () {
				var baseBar = $(".editing_scrubber_basebar");
				$(".editing_scrubber_buffer").transition({ width: (thePlayer.bufferedPercent() * baseBar.width()) }, 100, 'linear');
			})

			$("#" + this.id).width("100%");
			$("#" + this.id).height("100%");
			$("#player_playpause").on("click", function () {
				if (thePlayer.paused()) {
					thePlayer.play();
					$(this).attr("src", "img/controller-pause.png");
				} else {
					thePlayer.pause();
					$(this).attr("src", "img/controller-play.png");
				}
			});
		});

		var timeout;
		$("#videos_link").hover(function (){
			// on mouse in
			timeout = setTimeout(function () {
				$('#browse_drawer').transition({ height: '250px' }, 300, 'snap', function () {
					// callback
					$('browse_drawer').addClass('shadow');
				});
			}, 300);
		}, function () {
			// on mouse out
			clearTimeout(timeout);
			$('#browse_drawer').css('height', 0);
			console.log(':O hello world canceled!');
		});

		$(document).on("click", ".nextButton", function () {
			var parent = $(this).parent();
			var parentId = parent.attr("id");

			var selector = null;
			if (parentId === "step1") {
				selector = "#step3";
			} else if (parentId === "step3") {
				selector = "#step2";
			} else if (parent.parent().attr("id") === "step2_editor") {
				selector = "#step4";
			}
			console.log(selector + " " + parentId);
			$(selector).css("display", "block").transition({opacity: 1});
			$.scrollTo(selector, 600, { offset: { top: -70 }});
		});
	});
})();
