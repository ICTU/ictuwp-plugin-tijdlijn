
jQuery.fn.timeline = function() {
  
  
  
    return this.each(function() {
      
        function i() {
            e(), n();
        }
        function n() {
          return true;
        }
        function e() {
            jQuery(".timelineMajor").each(function(i, n) {
                jQuery(this).hasClass("open") && jQuery(this).find(".majorEvent").attr({
                    "aria-hidden": !1,
                    "aria-expanded": !0
                });
            }), jQuery(".timelineMinor").each(function() {
                jQuery(this).hasClass("open") && jQuery(this).find(".timelineEvent").attr({
                    "aria-hidden": !1,
                    "aria-expanded": !0
                });
            }), jQuery(".timelineEvent > table").wrap('<div class="scrollcontainer" />'), a();
        }

        function a() {

          console.log('open = ' + timeline.toggle_open + ' / close = ' + timeline.toggle_close );

            jQuery(".timelineToggle").hasClass("open") ? jQuery(".timelineToggle").find("a").html( string_close ) : jQuery(".timelineToggle").find("a").html( string_open ), d.find(".timelineToggle a").on("click", function(i) {
                i.preventDefault();
                var n = d.find(".timelineToggle"),
                    e = d.find(".timelineMajor"),
                    a = jQuery("html,body"),
                    l = jQuery(this).parent().parent(".timeline");
                navigator.userAgent.match(/(iPod|iPhone|iPad|Android)/) ? window.scrollTo(0, l.offset().top - a.offset().top) : a.animate({
                    scrollTop: l.offset().top - a.offset().top,
                    scrollLeft: 0
                }, 300), n.hasClass("open") ? (e.each(function() {
                    o(jQuery(this));
                }), n.find("a").html( string_open ), n.removeClass("open")) : (e.each(function() {
                    t(jQuery(this), !1);
                }), n.find("a").html( string_close ), n.addClass("open"));
            }), d.find(".timelineMajorMarker a").on("click", function(i) {
                i.preventDefault();
                var n = jQuery(this).parents(".timelineMajor");
                n.hasClass("open") ? o(n) : t(n, !0);
            }), d.find(".timelineEventHead a").on("click", function(i) {
                i.preventDefault();
                var n = jQuery(this).parents(".timelineMinor");
                n.hasClass("open") ? s(n) : l(n);
            });
        }

        function t(i, n) {
            $minors = i.find(".timelineMinor"), i.addClass("open");
            var e = i.find(".majorEvent");
            e.attr({
                "aria-hidden": !1,
                "aria-expanded": !0
            }), n && e.focus();
        }

        function o(i) {
            $minors = i.find(".timelineMinor"), $minors.each(function() {
                s(jQuery(this));
            });
            var n = i.find(".majorEvent");
            n.attr({
                "aria-hidden": !0,
                "aria-expanded": !1
            }), i.removeClass("open");
        }

        function l(i) {
            i.addClass("open");
            var n = i.find(".timelineEvent");
            n.attr({
                "aria-hidden": !1,
                "aria-expanded": !0
            }), n.focus();
        }

        function s(i) {
            i.removeClass("open"), i.find(".timelineEvent").attr({
                "aria-hidden": !0,
                "aria-expanded": !1
            }), jQuery("video,audio").trigger("pause");
        }
        var d = jQuery(this),
            string_close  = timeline.toggle_close,
            string_open   = timeline.toggle_open;
        i();
    });
}, jQuery(".timeline").timeline();
