jQuery.fn.timeline=function(){return this.each((function(){function e(){l.addClass("initialized"),n()}function n(){jQuery(".timelineMajor").each((function(e,n){jQuery(this).hasClass("open")&&jQuery(this).find(".majorEvent").attr({"aria-hidden":!1,"aria-expanded":!0})})),jQuery(".timelineMinor").each((function(){jQuery(this).hasClass("open")&&jQuery(this).find(".timelineEvent").attr({"aria-hidden":!1,"aria-expanded":!0})})),jQuery(".timelineEvent > table").wrap('<div class="scrollcontainer" />'),i()}function i(){jQuery(".timelineToggle").hasClass("open")?jQuery(".timelineToggle").find("button").html(d):jQuery(".timelineToggle").find("button").html(u),l.find(".timelineToggle button").on("click",(function(e){e.preventDefault();var n=l.find(".timelineToggle"),i=l.find(".timelineMajor"),o=jQuery("html,body"),r=jQuery(this).parent().parent(".timeline-main");navigator.userAgent.match(/(iPod|iPhone|iPad|Android)/)?window.scrollTo(0,r.offset().top-o.offset().top):o.animate({scrollTop:r.offset().top-o.offset().top,scrollLeft:0},300),n.hasClass("open")?(i.each((function(){a(jQuery(this))})),n.find("button").html(u),n.removeClass("open")):(i.each((function(){t(jQuery(this),!1)})),n.find("button").html(d),n.addClass("open"))})),l.find(".timelineMajorMarker a").on("click",(function(e){e.preventDefault();var n=jQuery(this).parents(".timelineMajor");n.hasClass("open")?a(n):t(n,!0)})),l.find(".timelineEventHead a").on("click",(function(e){e.preventDefault();var n=jQuery(this).parents(".timelineMinor");n.hasClass("open")?r(n):o(n)}))}function t(e,n){$minors=e.find(".timelineMinor"),e.addClass("open");var i=e.find(".majorEvent");i.attr({"aria-hidden":!1,"aria-expanded":!0}),n&&i.focus()}function a(e){var n;$minors=e.find(".timelineMinor"),$minors.each((function(){r(jQuery(this))})),e.find(".majorEvent").attr({"aria-hidden":!0,"aria-expanded":!1}),e.removeClass("open")}function o(e){e.addClass("open");var n=e.find(".timelineEvent");n.attr({"aria-hidden":!1,"aria-expanded":!0}),n.focus()}function r(e){e.removeClass("open"),e.find(".timelineEvent").attr({"aria-hidden":!0,"aria-expanded":!1}),jQuery("video,audio").trigger("pause")}var l=jQuery(this),s=jQuery(this).data("expand"),d=timeline.toggle_close,u=timeline.toggle_open,f=jQuery(this).data("collapse");e()}))},jQuery(".timeline-main").timeline();