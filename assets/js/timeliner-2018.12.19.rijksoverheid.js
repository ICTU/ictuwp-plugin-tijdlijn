
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
 */


jQuery.fn.timeline = function() {
    return this.each(function() {
        function r() {
            s()
        }

        function s() {
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
            jQuery(".timelineToggle").hasClass("open") ? jQuery(".timelineToggle").find("a").html(n) : jQuery(".timelineToggle").find("a").html(t), e.find(".timelineToggle a").on("click", function(r) {
                r.preventDefault();
                var i = e.find(".timelineToggle"),
                    s = e.find(".timelineMajor"),
                    o = jQuery("html,body"),
                    f = jQuery(this).parent().parent(".timeline-main");
                navigator.userAgent.match(/(iPod|iPhone|iPad|Android)/) ? window.scrollTo(0, f.offset().top - o.offset().top) : o.animate({
                    scrollTop: f.offset().top - o.offset().top,
                    scrollLeft: 0
                }, 300), i.hasClass("open") ? (s.each(function() {
                    a(jQuery(this))
                }), i.find("a").html(t), i.removeClass("open")) : (s.each(function() {
                    u(jQuery(this), !1)
                }), i.find("a").html(n), i.addClass("open"))
            }), e.find(".timelineMajorMarker a").on("click", function(e) {
                e.preventDefault();
                var t = jQuery(this).parents(".timelineMajor");
                t.hasClass("open") ? a(t) : u(t, !0)
            }), e.find(".timelineEventHead a").on("click", function(e) {
                e.preventDefault();
                var t = jQuery(this).parents(".timelineMinor");
                t.hasClass("open") ? l(t) : f(t)
            })
        }

        function u(e, t) {
            $minors = e.find(".timelineMinor"), e.addClass("open");
            var n = e.find(".majorEvent");
            n.attr({
                "aria-hidden": !1,
                "aria-expanded": !0
            }), t && n.focus()
        }

        function a(e) {
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
            n = jQuery(this).data("collapse");
        r()
    })
}, jQuery(".timeline-main").timeline();
