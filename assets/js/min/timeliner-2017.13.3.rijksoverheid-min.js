jQuery.fn.timeline=function(){return this.each((function(){function e(){i()}function n(){return!0}function i(){jQuery(".timelineMajor").each((function(e,n){jQuery(this).hasClass("open")&&jQuery(this).find(".majorEvent").attr({"aria-hidden":!1,"aria-expanded":!0})})),jQuery(".timelineMinor").each((function(){jQuery(this).hasClass("open")&&jQuery(this).find(".timelineEvent").attr({"aria-hidden":!1,"aria-expanded":!0})})),jQuery(".timelineEvent > table").wrap('<div class="scrollcontainer" />'),t()}function t(){jQuery(".timelineToggle").hasClass("open")?jQuery(".timelineToggle").find("a").html(d):jQuery(".timelineToggle").find("a").html(f),s.find(".timelineToggle a").on("click",(function(e){e.preventDefault();var n=s.find(".timelineToggle"),i=s.find(".timelineMajor"),t=jQuery("html,body"),o=jQuery(this).parent().parent(".timeline-main");navigator.userAgent.match(/(iPod|iPhone|iPad|Android)/)?window.scrollTo(0,o.offset().top-t.offset().top):t.animate({scrollTop:o.offset().top-t.offset().top,scrollLeft:0},300),n.hasClass("open")?(i.each((function(){r(jQuery(this))})),n.find("a").html(f),n.removeClass("open")):(i.each((function(){a(jQuery(this),!1)})),n.find("a").html(d),n.addClass("open"))})),s.find(".timelineMajorMarker a").on("click",(function(e){e.preventDefault();var n=jQuery(this).parents(".timelineMajor");n.hasClass("open")?r(n):a(n,!0)})),s.find(".timelineEventHead a").on("click",(function(e){e.preventDefault();var n=jQuery(this).parents(".timelineMinor");n.hasClass("open")?l(n):o(n)}))}function a(e,n){$minors=e.find(".timelineMinor"),e.addClass("open");var i=e.find(".majorEvent");i.attr({"aria-hidden":!1,"aria-expanded":!0}),n&&i.focus()}function r(e){var n;$minors=e.find(".timelineMinor"),$minors.each((function(){l(jQuery(this))})),e.find(".majorEvent").attr({"aria-hidden":!0,"aria-expanded":!1}),e.removeClass("open")}function o(e){e.addClass("open");var n=e.find(".timelineEvent");n.attr({"aria-hidden":!1,"aria-expanded":!0}),n.focus()}function l(e){e.removeClass("open"),e.find(".timelineEvent").attr({"aria-hidden":!0,"aria-expanded":!1}),jQuery("video,audio").trigger("pause")}var s=jQuery(this),d=timeline.toggle_close,f=timeline.toggle_open;e()}))},jQuery(".timeline-main").timeline();