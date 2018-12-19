/*
// * ICTU / WP timeline. 
// * 
// * Plugin Name:         ICTU / WP timeline
// * Plugin URI:          https://github.com/ICTU/digitale-overheid-wordpress-plugin-timelineplugin/
// * Description:         Insert usable and accessible timelines in your post or page 
// * Version:             1.1.0
// * Version description: CSS-bestand naar LESS omgezet. Kleuren aangepast en functionaliteit verbeterd.
// * Author:              Paul van Buuren
// * Author URI:          https://wbvb.nl
// * License:             GPL-2.0+
 */
jQuery.fn.timeline=function(){return this.each(function(){function e(){n()}function n(){jQuery(".timelineMajor").each(function(e,n){jQuery(this).hasClass("open")&&jQuery(this).find(".majorEvent").attr({"aria-hidden":!1,"aria-expanded":!0})}),jQuery(".timelineMinor").each(function(){jQuery(this).hasClass("open")&&jQuery(this).find(".timelineEvent").attr({"aria-hidden":!1,"aria-expanded":!0})}),jQuery(".timelineEvent > table").wrap('<div class="scrollcontainer" />'),i()}function i(){jQuery(".timelineToggle").hasClass("open")?jQuery(".timelineToggle").find("a").html(d):jQuery(".timelineToggle").find("a").html(l),s.find(".timelineToggle a").on("click",function(e){e.preventDefault();var n=s.find(".timelineToggle"),i=s.find(".timelineMajor"),a=jQuery("html,body"),t=jQuery(this).parent().parent(".timeline-main");navigator.userAgent.match(/(iPod|iPhone|iPad|Android)/)?window.scrollTo(0,t.offset().top-a.offset().top):a.animate({scrollTop:t.offset().top-a.offset().top,scrollLeft:0},300),n.hasClass("open")?(i.each(function(){o(jQuery(this))}),n.find("a").html(l),n.removeClass("open")):(i.each(function(){r(jQuery(this),!1)}),n.find("a").html(d),n.addClass("open"))}),s.find(".timelineMajorMarker a").on("click",function(e){e.preventDefault();var n=jQuery(this).parents(".timelineMajor");n.hasClass("open")?o(n):r(n,!0)}),s.find(".timelineEventHead a").on("click",function(e){e.preventDefault();var n=jQuery(this).parents(".timelineMinor");n.hasClass("open")?t(n):a(n)})}function r(e,n){$minors=e.find(".timelineMinor"),e.addClass("open");var i=e.find(".majorEvent");i.attr({"aria-hidden":!1,"aria-expanded":!0}),n&&i.focus()}function o(e){var n;$minors=e.find(".timelineMinor"),$minors.each(function(){t(jQuery(this))}),e.find(".majorEvent").attr({"aria-hidden":!0,"aria-expanded":!1}),e.removeClass("open")}function a(e){e.addClass("open");var n=e.find(".timelineEvent");n.attr({"aria-hidden":!1,"aria-expanded":!0}),n.focus()}function t(e){e.removeClass("open"),e.find(".timelineEvent").attr({"aria-hidden":!0,"aria-expanded":!1}),jQuery("video,audio").trigger("pause")}var s=jQuery(this),l=jQuery(this).data("expand"),d=jQuery(this).data("collapse");e()})},jQuery(".timeline-main").timeline();