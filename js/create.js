// var wmutube = (function () {

	//document.write('<script src="http://' + (location.host || 'localhost').split(':')[0] + ':35729/livereload.js?snipver=1"></' + 'script>')
	var publicMembers = {};

	// var videoInfo = {"questions":[{"text":"", "time": 15, "answers":[{"text":"","correct":false},{"text":"","correct":false},{"text":"","correct":false},{"text":"","correct":false}]}],"title":"omg","description":"wtf","subject":"CS","course":"why","year":"2013"};

	var videoPlayer = null;

	// utility functions
	var util = (function () {
		
		var publicMembers = {
			formattedTimeWithSeconds: function (seconds) {
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
			secondsWithFormattedTime: function (time) {

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

	var step3 = (function () {

		var updateCourseListing = function () {
			$("#video_course>option").remove();
			var subject = $("#video_subject").val();
			$.get("./ajax/course/" + subject, function (data) {
				var courses = JSON.parse(data);
				$courseSelect = $("#video_course");
				if (courses !== undefined) {
					$.each(courses, function (index, item) {
						var course = "<option value='" + item.id + "'>" + item.subject + item.number + " - " + item.title + "</option>\n";
						$courseSelect.append(course);
					});
				}
			})
		};

		// on document ready
		$(function () {
			updateCourseListing();
			$("#video_subject").change(function () {
				updateCourseListing();
			});
		});

		var publicMembers = {
			validate: function (alertUser) {
				var errors = [];

				if ($("#video_title").val().trim().length === 0) {
					errors.push("Video must have a title.\n");
				}
				if ($("#video_description").val().trim().length === 0) {
					errors.push("Video description cannot be empty.\n");
				}

				if (errors.length > 0) {
					if (alertUser === true) {
						var alertText = "Please correct the following errors: \n\n";
						$.each(errors, function (index, item) {
							alertText += item;
						});

						alert(alertText);
					}
					return false;
				}
				return true;
			}
		};

		return publicMembers;
	})();

	var editor_step2 = (function () {

		const QSPACING = 25;

		// private members
		var qSelectors = [];
		var currentQ = null;
		var animating = false;

		function addQuestionTimeMarker (secondMarker) {
			
			// create new element
			var newMarker = document.createElement("div");
			newMarker.className = "editing_scrubber_qmarker";

			// figure out its offset
			var offset = (secondMarker / videoPlayer.duration()) * $(".editing_scrubber_basebar").width();
			$(newMarker).css("margin-left", offset);

			// append
			$(".editing_scrubber").append(newMarker);
		}

		// self refers to the element, not this object.
		function addAnother (self, animate, questionInfo) {

			if (animating === true) {
				// console.log("Animation in progress, not adding.");
				return;
			} 

			var newId = util.generateGuid();
			var oldQ = $('.question:last');
			var newQ = oldQ.clone(false);
			var $newQ = $(newQ);

			// adjust attributes of new object
			$newQ.attr("id", newId);
			$newQ.css("margin-top", -($(oldQ).outerHeight()));

			if (typeof(questionInfo) === "undefined")
			{
				$newQ.find(":input").each(function () { $(this).prop("checked", false).attr("name", newId) });
				$newQ.find(".q_body:first").each(function () {$(this).val('');});
				$newQ.find(".q_answer").each(function () {$(this).val('');});
				$newQ.find(".q_time").val(util.formattedTimeWithSeconds(videoPlayer.currentTime()));
			}
			else
			{
					$newQ.find(":input").each(function (index) {
						if (typeof(questionInfo.answers[index]) !== "undefined") {
							var isCorrect = questionInfo.answers[index].correct == true ? true : false;
							$(this).prop("checked", isCorrect).attr("name", newId) 
						}
					});

					$newQ.find(".q_body:first").each(function (index) {
						if (typeof(questionInfo.answers[index]) !== "undefined") {
							var qText = typeof(questionInfo.text) === "undefined" ? "" : questionInfo.text;
							$(this).val(qText);
						}
					});

					$newQ.find(".q_answer").each(function (index) {
						if (typeof(questionInfo.answers[index]) !== "undefined") {
							var t = questionInfo.answers[index].text;
							var aText = typeof(t) === undefined ? "" : t;
							$(this).val(t);
						}
					});

				var presentTime = typeof(questionInfo.time) === undefined ? 0 : questionInfo.time;
				$newQ.find(".q_time").val(util.formattedTimeWithSeconds(presentTime));
			}

			// shove the old questions left
			$('.question').each(function (index) {

				var l = $(this).css("left") === 'auto' ? 0 : parseInt($(this).css("left"));
				if (animate === true)
				{
					animating = true;
					$(this).transition({x: -($(this).outerWidth() + QSPACING), "opacity": .3}, 100, function () {
						finalizeAdd(this, l, index);
						animating = false;
					});
				}
				else finalizeAdd(this, l, index);
				console.log(this.id + ": " + $(this).css("left"));
			});

			// append the new question to the DOM
			qSelectors.push("#" + newId);
			currentQ = qSelectors.indexOf("#" + newId);
			$('#step2_editor').append(newQ);

			if (typeof(questionInfo) !== "undefined") {
				addQuestionTimeMarker(questionInfo.time)
			} else {
				addQuestionTimeMarker(videoPlayer.currentTime());
			}
		}

		function finalizeAdd (that, left, index) {
			// there must be a better way to do this...
			$(that).removeAttr('style');
			$(that).css('opacity', .3)
			$(that).css("left", left - ($(that).outerWidth() + QSPACING));

			if (index > 0) {
				$(that).css('margin-top', -($(that).outerHeight()));
			}
		}

		function validateQuestion (question, alertUser) {
			var $q = $(question);
			var errors = [];

			// check for an empty question body
			var qBody = $q.find(".q_body").val().trim();
			if (qBody.length === 0) {
				errors.push("Question body cannot be empty.\n"); 
			}

			$checked = $q.find(":checked");
			if ($checked.length < 1) {
				errors.push("You must select a correct answer.\n");
			} else if ($($($checked[0]).siblings()[0]).val().trim() === "") {
				errors.push("The correct answer cannot have an empty body.\n");
			}

			if (errors.length > 0) {
				if (alertUser === true) {
					var errString = "Please correct the following errors: \n\n";
					$.each(errors, function (index, item) {
						errString += item;
					});
					alert(errString);
				}
				return false;
			} else {
				return true;
			}

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
				
				if (validateQuestion(currentQ, true)) {
					addAnother(this, true);
				}
				console.log("called");
			});

			$('#step2_editor').on('click', ".previous", function (e) {
				e.preventDefault();
				move(1);
			});

			$('#step2_editor').on('click', ".next", function (e) {
				e.preventDefault();
				move(-1);
			});

			$('#step2_editor').on("click", ".submitButton", function () {

				// the submission object
				var theObj = {};
				theObj.questions = [];

				// get some basic information about the video
				theObj.title = $("#video_title").val();
				theObj.description = $("#video_description").val();
				theObj.course = $("#video_course").val();

				// get the questions and answers
				// iterate over the questions
				$(".question").each(function (index, item) {
					if (validateQuestion(item, false) === true) {
						theObj.questions.push({
							"text": $(item).find(".q_body").first().val().trim(), 
							"hint": $(item).find(".q_hint").first().val().trim(), 
							"timestamp": $(item).find(".q_time").first().val().trim(),
							"answers": []});
						$(item).find(".answer").each(function (iIndex, iItem) {
							// iterate over each question's answers.
							var kids = $(iItem).children();
							var text = $(kids[0]).val().trim();
							var correct = $(kids[1]).is(":checked");

							if (text !== "") {
								theObj.questions[index].answers.push({"text": text, "correct": correct});
							} else {
								console.log("discarded an answer: " + text);
							}
						});
					} else {
						console.log("Discarded invalid item at index: " + index);
					}
				});

				$.post("./ajax/create", "videoInfo=" + JSON.stringify(theObj), function (data, textStatus, jqXHR) {
					console.log("Posted videoInfo object...\ndata: " + data + "\ntextStatus: " + textStatus);
					$(".submitButton").css("background-color", "green").html("Success!");
					setTimeout(function () {
						var d = JSON.parse(data);
						console.log(d.id);
						window.location = "./watch/" + d.id;
					}, 1000);
				})
				console.log(JSON.stringify(theObj));
			});

			// add the first question, which we will clone.
			var guid = util.generateGuid();
			currentQ = "#" + guid;
			qSelectors.push("#" + guid);
			$('.section_step2').attr('id', guid);
			$(currentQ).find(".q_time").val(util.formattedTimeWithSeconds(0));
			
			// restore the video state if there is videoInfo object in the global namespace
			if (typeof(videoInfo !== "undefined"))
			{
				$("#video_title").val(videoInfo.title);
				$("#video_description").val(videoInfo.description);
				$("#video_subject").val(videoInfo.subject);
				$("#video_course").val(videoInfo.course);
				$("#video_year").val(videoInfo.year);

				$.each(videoInfo.questions, function (index, item) {
					addAnother(null, false, item);
				});
			}

			
		});
	});


	// on document ready
	$(function () {

		// if there is video info present, don't show the uploader
		if (typeof(videoInfo) !== "undefined") {
			$("#step1").hide();
			$("#step3").show().css("opacity", 1);
		}

		$('#file_upload').uploadify({
			'buttonText'		: "Select File",
			'auto'				: true,
			'swf'				: './swf/uploadify.swf',
			'uploader'			: 'ajax/upload',
			'method'			: 'post',
			'fileTypeDesc'		: 'mp4 files',
			'fileTypeExts'		: '*.mp4',
			'onUploadSuccess'	: function (file, data, response) {
				var realData = JSON.parse(data);
				$("#step3").css("display", "block").transition({opacity: 1});
				$.scrollTo("#step3", 600, { offset: { top: -70 }});
				videoPlayer.src({src: "." + realData.filename, type: "video/mp4"});
				console.log("upload was successful");
				console.log("response: " + realData);
				console.log("filename: " + realData.filename);
			},
			'onUploadError'		: function (file, errorCode, errorMsg, errorString) {
				console.log("upload failed");
				$("#step3").css("display", "block").transition({opacity: 1});
				$.scrollTo("#step3", 600, { offset: { top: -70 }});
			}
		});

		// Make the uploadify button look like an actual button
		$("#file_upload").addClass("button");

		// on video.js ready
		_V_("video_player").ready(function () {

			// don't set up the editor until video.js is ready
			editor_step2 = editor_step2();

			videoPlayer = this;
			var lastTimeUpdate = 0;
			videoPlayer.addEvent("timeupdate", function () {
				var update = Math.floor(videoPlayer.currentTime());
				if (update !== lastTimeUpdate) {
					console.log(update);
				}

				var progressBar = $(".editing_scrubber_progress");
				var baseBar = $(".editing_scrubber_basebar");
				var timePercent = videoPlayer.currentTime() / videoPlayer.duration();
				progressBar.transition({ width: (timePercent * baseBar.width()) }, 100, 'linear');
			});

			videoPlayer.addEvent("progress", function () {
				var baseBar = $(".editing_scrubber_basebar");
				$(".editing_scrubber_buffer").transition({ width: (videoPlayer.bufferedPercent() * baseBar.width()) }, 100, 'linear');
			})

			$("#" + this.id).width("100%");
			$("#" + this.id).height("100%");
			$("#player_playpause").on("click", function () {
				if (videoPlayer.paused()) {
					videoPlayer.play();
					$(this).attr("src", "img/controller-pause.png");
				} else {
					videoPlayer.pause();
					$(this).attr("src", "img/controller-play.png");
				}
			});
		});

		$(document).on("click", ".nextButton", function () {
			var parent = $(this).parent();
			var parentId = parent.attr("id");

			var selector = null;
			if (parentId === "step1") {
				selector = "#step3";
			} else if (parentId === "step3") {
				if (!step3.validate(true)) {
					return;
				}
				selector = "#step2";
			} else if (parent.parent().attr("id") === "step2_editor") {
				selector = "#step4";
			}
			console.log(selector + " " + parentId);
			$(selector).css("display", "block").transition({opacity: 1});
			$.scrollTo(selector, 600, { offset: { top: -70 }});
		});
	});

// 	return publicMembers;
// })();
