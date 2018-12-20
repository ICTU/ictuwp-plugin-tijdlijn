/*
// * ICTU / WP timeline. 
// * 
// * Plugin Name:         ICTU / WP timeline
// * Plugin URI:          https://github.com/ICTU/digitale-overheid-wordpress-plugin-timelineplugin/
// * Description:         Insert usable and accessible timelines in your post or page 
// * Version:             1.1.1
// * Version description: Betere check op condities. Styling aangepast voor als JS niet geladen is + als wel geladen is.
// * Author:              Paul van Buuren
// * Author URI:          https://wbvb.nl
 */
jQuery.fn.timeline=function(){return this.each(function(){function e(){l.addClass("initialized"),n()}function n(){jQuery(".timelineMajor").each(function(e,n){jQuery(this).hasClass("open")&&jQuery(this).find(".majorEvent").attr({"aria-hidden":!1,"aria-expanded":!0})}),jQuery(".timelineMinor").each(function(){jQuery(this).hasClass("open")&&jQuery(this).find(".timelineEvent").attr({"aria-hidden":!1,"aria-expanded":!0})}),jQuery(".timelineEvent > table").wrap('<div class="scrollcontainer" />'),i()}function i(){jQuery(".timelineToggle").hasClass("open")?jQuery(".timelineToggle").find("button").html(d):jQuery(".timelineToggle").find("button").html(u),l.find(".timelineToggle button").on("click",function(e){e.preventDefault();var n=l.find(".timelineToggle"),i=l.find(".timelineMajor"),t=jQuery("html,body"),a=jQuery(this).parent().parent(".timeline-main");navigator.userAgent.match(/(iPod|iPhone|iPad|Android)/)?window.scrollTo(0,a.offset().top-t.offset().top):t.animate({scrollTop:a.offset().top-t.offset().top,scrollLeft:0},300),n.hasClass("open")?(i.each(function(){r(jQuery(this))}),n.find("button").html(u),n.removeClass("open")):(i.each(function(){o(jQuery(this),!1)}),n.find("button").html(d),n.addClass("open"))}),l.find(".timelineMajorMarker a").on("click",function(e){e.preventDefault();var n=jQuery(this).parents(".timelineMajor");n.hasClass("open")?r(n):o(n,!0)}),l.find(".timelineEventHead a").on("click",function(e){e.preventDefault();var n=jQuery(this).parents(".timelineMinor");n.hasClass("open")?a(n):t(n)})}function o(e,n){$minors=e.find(".timelineMinor"),e.addClass("open");var i=e.find(".majorEvent");i.attr({"aria-hidden":!1,"aria-expanded":!0}),n&&i.focus()}function r(e){var n;$minors=e.find(".timelineMinor"),$minors.each(function(){a(jQuery(this))}),e.find(".majorEvent").attr({"aria-hidden":!0,"aria-expanded":!1}),e.removeClass("open")}function t(e){e.addClass("open");var n=e.find(".timelineEvent");n.attr({"aria-hidden":!1,"aria-expanded":!0}),n.focus()}function a(e){e.removeClass("open"),e.find(".timelineEvent").attr({"aria-hidden":!0,"aria-expanded":!1}),jQuery("video,audio").trigger("pause")}var l=jQuery(this),s=jQuery(this).data("expand"),d=timeline.toggle_close,u=timeline.toggle_open,f=jQuery(this).data("collapse");e()})},jQuery(".timeline-main").timeline();