var khan_academy = (function () {

	document.write('<script src="http://' + (location.host || 'localhost').split(':')[0] + ':35729/livereload.js?snipver=1"></' + 'script>')

	var formattedTimeWithSeconds = function () {
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
	}
	$(function () {

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

		$(".nextButton").on("click", function () {
			var parentId = $(this).parent().attr("id");
			var selector = null;
			if (parentId === "step1") {
				selector = "#step2";
			} else if (parentId === "step2_editor") {
				selector = "#step3";
			}
			console.log(selector + " " + parentId);
			$(selector).css("display", "block").transition({opacity: 1});
			$.scrollTo(selector, 600, { offset: { top: -70 }});
		});
	});
})();
