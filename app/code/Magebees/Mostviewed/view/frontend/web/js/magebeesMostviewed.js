/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'jquery',
    "jquery/ui",
    "magebees/mvowlcarousel"
], function ($,ui) {
    "use strict";

    $.widget('magebees.magebeesMostviewed', {
        
        options: {
            nav:'',
            autoplay:''
        },
        is_loading: 0,
        _create: function (options) {
            this._initialize();
        },
        _initialize: function () {
            var self = this;
            self.removeRedirectAttr();
            self.findPaginationElement();
            self.addSlider();
                $(document).on('DOMNodeInserted','.cwsMostviewed', function (e) {
                    
                        self.removeRedirectAttr();
                        self.findPaginationElement();
                });
        },
        removeRedirectAttr: function () {
            var self = this;
          $("body .mageMostviewedToolbar").find('[ data-mage-init]')
            .each(function (index, element) {
                var ele_id=$(element).attr('id');
                if (ele_id=="limiter") {
                $(element).removeAttr('data-mage-init');
                     $(element).change(function () {
                         var url=$(this).val();
                         self.ajaxLoadContent(url);
                     });
                }
            });
        },
        findPaginationElement: function () {
            var self = this;
            $("body .mageMostviewedToolbar").find("a").each(function () {
                var link = $(this);
                var link_class= $(this).attr("class");
                var classes = [ "page","action  previous","action  next"];
                var found_class = $.inArray(link_class,classes);
                if (found_class >-1) {
                link.attr("onclick", "return false;");
                    var url = link.attr("href");
                    link.click(function () {
                        
                         if (self.is_loading == 0) {
                        self.ajaxLoadContent(url);
                         }
                    });
                }
            });
        },
        ajaxLoadContent: function (url) {
            var self = this;
            self.is_loading = 1;
                $.ajax({
                        url: url,
                        type: "GET",
                        cache:false,
                        beforeSend : function () {
                               
                            $('body').addClass('mp-stop-scrolling');
                            $('#mp_scroll_loading').css('display','block');
                        },
                        success: function (data) {
                            self.is_loading = 0;
                            $('body').removeClass('mp-stop-scrolling');
                            history.pushState({}, "", url);
                            $('#mp_scroll_loading').css('display','none');
                            var page_content=$(data).find('.cwsMostviewed').html();                                                 if (page_content==undefined) {
                            var page_content=$(data).filter('.cwsMostviewed').html();
                            }
                            $(".cwsMostviewed").html(page_content);
                            $(".cwsMostviewed").trigger('contentUpdated');
                        }
                        });
        },
        addSlider: function () {
            var nav=this.options.nav;
            var navigation = nav === "true";
            var autoplay=this.options.autoplay;
            var autoplayslider = autoplay === "true";
            var owl = $('#'+this.options.slider_id);
            owl.owlCarousel({
                slideSpeed:200,
                paginationSpeed: 800,
                nav:navigation,
                dots:false,
                autoplay:autoplayslider,
                margin:20,
                loop:true,
                responsiveClass:true,
                responsive:{
                    300:{ items:1 },
                    479:{ items:2 },
                    600:{ items:2 },
                    767:{ items:3 },
                    999:{ items:4 },
                    1280:{ items:5, loop:true }
                }

            });
            if (autoplay === "true") {
            owl.on('mouseenter',function () {
               owl.trigger('stop.owl.autoplay');
              })
              owl.on('mouseleave',function () {
                   owl.trigger('play.owl.autoplay',[200]);
              });
            }
        }
    });

    return $.magebees.magebeesMostviewed;
});

