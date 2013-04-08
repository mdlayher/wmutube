$(function () {
	var questionAnswers = (function () {
		console.log("Hello world!");
		
	}());

	$(document).on("mousedown", ".answer", function () {
			console.log("clicked!");
			$(".answer>.radio").removeClass("selected");
			$(this).children(".radio").addClass("selected");
		});

	var quizzing = (function () {

		var quizQuestions = questions;
		var currentQuestionId = undefined;

		var newAnswerDiv = function (answerText, answerId) {
			// create the div that contains the answer
			var ans = 	"<div class='answer' data-answer_id='" + answerId + "'>\
							<div class='radio'></div>\
							<div class='answer_text'>" + answerText + "</div>\
						</div>";

			return ans;
		};

		var publicMembers = {
			PresentQuizIfNecessary: function (currentTime) {
				var presentIndex = undefined;
				$.each(quizQuestions, function (index, item) {
					if (parseInt(item.timestamp) <= currentTime && typeof(item.presented) === "undefined") {
						console.log("Q" + index + ": We should now present this item. ");
						item.presented = true;
						presentIndex = index;
						return false;
					}
				});

				if (presentIndex !== undefined) {

					// remove all the answers from the dom
					$(".answer").remove();

					var question = quizQuestions[presentIndex];
					currentQuestionId = question.id;

					$(".question>div").html("<p>" + question.text + "</p>");
					$.each(question.answers, function (index, item) {
						$(".answers>div").append(newAnswerDiv(item.text, item.id));
					});

					$(".quiz_result").hide();

					$('.body_fade').css("display", "block");
					$('.quiz_container').css("display", "block");

					return true;
				} else {
					return false;
				}
			}
		};

		$(function () {
			// on ready
			$("#quiz_submit").click(function () {
				
				var selectedAnswer = $(".selected").parent().attr("data-answer_id");

				$.get("/wmutube/ajax/answer/correct/" + selectedAnswer, function (data) {
					var result = JSON.parse(data);
					$(".quiz_result").removeClass("correct incorrect")

					if (result.correct == 1) {
						$(".quiz_result").addClass("correct");
						$(".bottom").html("CORRECT");
					} else if (result.correct == 0) {
						$(".quiz_result").addClass("incorrect");
						$(".bottom").html("INCORRECT");
					}
					$(".top").html("Loading justification...");

					$(".quiz_result").show();
					$.get("/wmutube/ajax/question/hint/" + currentQuestionId, function (data) {
						var result = JSON.parse(data);
						$(".top").html(result.hint);
					});

				});
				//player.play();
			});

			$("#result_confirm").click(function () {
				$(".body_fade").hide();
				$(".quiz_container").hide();
				player.play();
			});

			console.log("questions: " + questions);
		});
		return publicMembers;

	})();

	var player = (function () {

		var videoPlayer = null;
		_V_("video_player").ready(function () {
			videoPlayer = this;

			$("#" + this.id).css("width", "100%");
			$("#" + this.id).height("100%");

			videoPlayer.addEvent("timeupdate", function (e) {

				var currentTime = videoPlayer.currentTime();
				// check if it's time to show another quiz.
				if (quizzing.PresentQuizIfNecessary(currentTime) === true) {
					console.log("Aaaaaaaand we should pause");
					videoPlayer.cancelFullScreen();
					videoPlayer.pause();
				}
				console.log(currentTime);
			});
		});

		var publicMembers = {
			play: function () {
				videoPlayer.play();
			},
			pause: function () {
				videoPlayer.pause();
			}
		};

		return publicMembers;
	}());
});