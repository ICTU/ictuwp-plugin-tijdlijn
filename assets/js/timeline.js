/*
// * ICTU / WP timeline. 
// * 
// * Plugin Name:         ICTU / WP timeline
// * Plugin URI:          https://github.com/ICTU/digitale-overheid-wordpress-plugin-timelineplugin/
// * Description:         Insert usable and accessible timelines in your post or page 
// * Version:             1.3.2
// * Version description: Code cleanup; script to open all sub-nodes.
// * Author:              Paul van Buuren
// * Author URI:          https://wbvb.nl
 */


jQuery.fn.timeline = function () {
    return this.each(function () {

        function doInitialize() {
            e.addClass("initialized");
            setProperties()
        }

        function setProperties() {
            jQuery(".timelineMajor").each(function (e, t) {
                jQuery(this).hasClass("open") && jQuery(this).find(".majorEvent").attr({
                    "aria-hidden": !1,
                    "aria-expanded": !0
                })
            }), jQuery(".timelineMinor").each(function () {
                jQuery(this).hasClass("open") && jQuery(this).find(".timelineEvent").attr({
                    "aria-hidden": !1,
                    "aria-expanded": !0
                })
            }), jQuery(".timelineEvent > table").wrap('<div class="scrollcontainer" />'), doOpenAction()
        }

        function doOpenAction() {

            jQuery(".timelineToggle").hasClass("open") ? jQuery(".timelineToggle").find("button").html(string_close) : jQuery(".timelineToggle").find("button").html(string_open), e.find(".timelineToggle button").on("click", function (r) {
                r.preventDefault();
                var timelineToggle  = e.find(".timelineToggle"),    // buttons
                    timelineMajor   = e.find(".timelineMajor"),     // hoofditem
                    htmlbody        = jQuery("html,body"),
                    timelinemain    = jQuery(this).parent().parent(".timeline-main");

                navigator.userAgent.match(/(iPod|iPhone|iPad|Android)/) ? window.scrollTo(0, timelinemain.offset().top - htmlbody.offset().top) : htmlbody.animate({
                    scrollTop: timelinemain.offset().top - htmlbody.offset().top,
                    scrollLeft: 0
                }, 300), timelineToggle.hasClass("open") ? (timelineMajor.each(function () {
                    // if button has class .open, then close minor objects of major item
                    doCloseMinorObjectsOfItem(jQuery(this))
                }),
                    timelineToggle.find("button").html(string_open),
                    timelineToggle.removeClass("open")) : (timelineMajor.each(function () {
                    doOpenMinorObject(jQuery(this), !1)
                }), timelineToggle.find("button").html(string_close),
                    timelineToggle.addClass("open"))
            }),
            e.find(".timelineMajorMarker a").on("click", function (e) {
                e.preventDefault();
                var timelineMajor = jQuery(this).parents(".timelineMajor");
                timelineMajor.hasClass("open") ? doCloseMinorObjectsOfItem(timelineMajor) : doOpenMinorObject(timelineMajor, !0)
            }),
            e.find(".timelineEventHead a").on("click", function (e) {
                e.preventDefault();
                var timelineMinor = jQuery(this).parents(".timelineMinor");
                timelineMinor.hasClass("open") ? doRemoveClassOpen(timelineMinor) : doAddClassOpen(timelineMinor)
            })
        }

        function doOpenMinorObject(e, t) {
            $minors = e.find(".timelineMinor"), e.addClass("open");
            
            var n = e.find(".majorEvent");
            
            n.attr({
                "aria-hidden": !1,
                "aria-expanded": !0
            }), t && n.focus()
        }

        function doCloseMinorObjectsOfItem(e) {
            $minors = e.find(".timelineMinor"), $minors.each(function () {
                doRemoveClassOpen(jQuery(this))
            });

            var t = e.find(".majorEvent");

            t.attr({
                "aria-hidden": !0,
                "aria-expanded": !1
            }), e.removeClass("open")
        }

        function doAddClassOpen(e) {
            e.addClass("open");

            var t = e.find(".timelineEvent");

            t.attr({
                "aria-hidden": !1,
                "aria-expanded": !0
            }), t.focus()
        }

        function doRemoveClassOpen(e) {
            e.removeClass("open"), e.find(".timelineEvent").attr({
                "aria-hidden": !0,
                "aria-expanded": !1
            }), jQuery("video,audio").trigger("pause")
        }


        var e               = jQuery(this),
            t               = jQuery(this).data("expand"),
            string_close    = timeline.toggle_close,
            string_open     = timeline.toggle_open,
            n               = jQuery(this).data("collapse");

        doInitialize()
    })
}, jQuery(".timeline-main").timeline();
