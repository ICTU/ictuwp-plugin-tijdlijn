
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


jQuery.fn.timeline = function() {
    return this.each(function() {
        function doInitialize() {
          e.addClass("initialized");
          setProperties()
        }

        function setProperties() {
            jQuery(".timelineMajor").each(function(e, t) {
                jQuery(this).hasClass("open") && jQuery(this).find(".majorEvent").attr({
                    "aria-hidden": !1,
                    "aria-expanded": !0
                })
            }), jQuery(".timelineMinor").each(function() {
                jQuery(this).hasClass("open") && jQuery(this).find(".timelineEvent").attr({
                    "aria-hidden": !1,
                    "aria-expanded": !0
                })
            }), jQuery(".timelineEvent > table").wrap('<div class="scrollcontainer" />'), doOpenAction()
        }

        function doOpenAction() {

            jQuery(".timelineToggle").hasClass("open") ? jQuery(".timelineToggle").find("button").html( string_close ) : jQuery(".timelineToggle").find("button").html( string_open ), e.find(".timelineToggle button").on("click", function(r) {
                r.preventDefault();
                var timelineToggle  = e.find(".timelineToggle"),
                    timelineMajor   = e.find(".timelineMajor"),
                    htmlbody        = jQuery("html,body"),
                    timelinemain    = jQuery(this).parent().parent(".timeline-main");
                navigator.userAgent.match(/(iPod|iPhone|iPad|Android)/) ? window.scrollTo(0, timelinemain.offset().top - htmlbody.offset().top) : htmlbody.animate({
                    scrollTop: timelinemain.offset().top - htmlbody.offset().top,
                    scrollLeft: 0
                }, 300), timelineToggle.hasClass("open") ? (timelineMajor.each(function() {
                    doCloseObject(jQuery(this))
                }), timelineToggle.find("button").html( string_open ), timelineToggle.removeClass("open")) : (timelineMajor.each(function() {
                    doOpenObject(jQuery(this), !1)
                }), timelineToggle.find("button").html( string_close ), timelineToggle.addClass("open"))
            }), e.find(".timelineMajorMarker a").on("click", function(e) {
                e.preventDefault();
                var timelineMajor = jQuery(this).parents(".timelineMajor");
                timelineMajor.hasClass("open") ? doCloseObject( timelineMajor ) : doOpenObject( timelineMajor, !0)
            }), e.find(".timelineEventHead a").on("click", function(e) {
                e.preventDefault();
                var timelineMinor = jQuery(this).parents(".timelineMinor");
                timelineMinor.hasClass("open") ? l( timelineMinor ) : f( timelineMinor )
            })
        }

        function doOpenObject(e, t) {
            $minors = e.find(".timelineMinor"), e.addClass("open");
            var n = e.find(".majorEvent");
            n.attr({
                "aria-hidden": !1,
                "aria-expanded": !0
            }), t && n.focus()
        }

        function doCloseObject(e) {
            $minors = e.find(".timelineMinor"), $minors.each(function() {
                l(jQuery(this))
            });
            var t = e.find(".majorEvent");
            t.attr({
                "aria-hidden": !0,
                "aria-expanded": !1
            }), e.removeClass("open")
        }

        function f(e) {
            e.addClass("open");
            var t = e.find(".timelineEvent");
            t.attr({
                "aria-hidden": !1,
                "aria-expanded": !0
            }), t.focus()
        }

        function l(e) {
            e.removeClass("open"), e.find(".timelineEvent").attr({
                "aria-hidden": !0,
                "aria-expanded": !1
            }), jQuery("video,audio").trigger("pause")
        }
        var e = jQuery(this),
            t = jQuery(this).data("expand"),

            string_close  = timeline.toggle_close,
            string_open   = timeline.toggle_open,

            n = jQuery(this).data("collapse");

        doInitialize()
    })
}, jQuery(".timeline-main").timeline();
