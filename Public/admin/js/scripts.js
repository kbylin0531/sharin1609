(function() {
    "use strict";
    var leftside = $(".left-side");
    var thisbody = $('body');
    var thisdoc = $(document);
    var menulist = $('.menu-list');
    var maincontent = $('.main-content');
    var customnav = $('.custom-nav');
    var searchform = $(".searchform");
    // custom scrollbar

    $("html").niceScroll({styler:"fb",cursorcolor:"#65cea7", cursorwidth: '6', cursorborderradius: '0px', background: '#424f63', spacebarenabled:false, cursorborder: '0',  zindex: '1000'});
    leftside.niceScroll({styler:"fb",cursorcolor:"#65cea7", cursorwidth: '3', cursorborderradius: '0px', background: '#424f63', spacebarenabled:false, cursorborder: '0'});


    leftside.getNiceScroll();
    if (thisbody.hasClass('left-side-collapsed')) {
        leftside.getNiceScroll().hide();
    }

    // Toggle Left Menu
    menulist.children('a').click(function() {
        var parent = $(this).parent();
        var sub = parent.children('ul');
        if(!thisbody.hasClass('left-side-collapsed')) {
            if(sub.is(':visible')) {
                sub.slideUp(200, function(){
                    parent.removeClass('nav-active');
                    maincontent.css({height: ''});
                    mainContentHeightAdjust();
                });
            } else {
                visibleSubMenuClose();
                parent.addClass('nav-active');
                sub.slideDown(200, function(){
                    mainContentHeightAdjust();
                });
            }
        }
        return false;
    });

    function visibleSubMenuClose() {
        menulist.each(function() {
            var t = $(this);
            if(t.hasClass('nav-active')) {
                t.children('ul').slideUp(200, function(){
                    t.removeClass('nav-active');
                });
            }
        });
    }

    function mainContentHeightAdjust() {
        // Adjust main content height
        var docHeight = thisdoc.height();
        if(docHeight > maincontent.height()) maincontent.height(docHeight);
    }

    //  class add mouse hover
    customnav.children('li').hover(function(){
        $(this).addClass('nav-hover');
    }, function(){
        $(this).removeClass('nav-hover');
    });

    var LeftSide = {
        isCollapsed : function () {
            return thisbody.hasClass('left-side-collapsed');
        },
        collapse : function () {
            thisbody.addClass('left-side-collapsed');
            customnav.find('ul').attr('style','');
            $(this).addClass('menu-collapsed');
            L.cookie.set('left-side-show',0,0);
        },
        uncollapse:function () {
            thisbody.removeClass('left-side-collapsed chat-view');
            customnav.find('li.active ul').css({display: 'block'});
            $(this).removeClass('menu-collapsed');
            L.cookie.set('left-side-show',1,0);
        }
    };
    if(L.cookie.get('left-side-show') == 0){
        LeftSide.collapse();
    }else{
        LeftSide.uncollapse();
    }

    // Menu Toggle  collapse-折叠
    $('.toggle-btn').click(function(){
        leftside.getNiceScroll().hide();

        if (LeftSide.isCollapsed()) {
            leftside.getNiceScroll().hide();
        }

        if(thisbody.css('position') != 'relative') {
            if(!LeftSide.isCollapsed()){
                LeftSide.collapse();
            }else{
                LeftSide.uncollapse();
            }
        } else {
            if(thisbody.hasClass('left-side-show')){
                thisbody.removeClass('left-side-show');
            } else {
                thisbody.addClass('left-side-show');
            }
            mainContentHeightAdjust();
        }

    });

    $(window).resize(function(){
        if(thisbody.css('position') == 'relative') {
            thisbody.removeClass('left-side-collapsed');
        } else {
            thisbody.css({left: '', marginRight: ''});
        }

        if(searchform.css('position') == 'relative') {
            searchform.insertBefore('.left-side-inner .logged-user');
        } else {
            searchform.insertBefore('.menu-right');
        }

    }).trigger('resize');

    // panel collapsible
    $('.panel .tools .fa').click(function () {
        var el = $(this).parents(".panel").children(".panel-body");
        if ($(this).hasClass("fa-chevron-down")) {
            $(this).removeClass("fa-chevron-down").addClass("fa-chevron-up");
            el.slideUp(200);
        } else {
            $(this).removeClass("fa-chevron-up").addClass("fa-chevron-down");
            el.slideDown(200); }
    });

    $('.todo-check label').click(function () {
        $(this).parents('li').children('.todo-title').toggleClass('line-through');
    });

    thisdoc.on('click', '.todo-remove', function () {
        $(this).closest("li").remove();
        return false;
    });

    $("#sortable-todo").sortable();

    // panel close
    $('.panel .tools .fa-times').click(function () {
        $(this).parents(".panel").parent().remove();
    });
    $('.tooltips').tooltip();
    $('.popovers').popover();

})($);