$ = jQuery.noConflict();
$(document).ready(function(){

	// check IE 
	function getInternetExplorerVersion(){var e=-1;if("Microsoft Internet Explorer"==navigator.appName){var r=navigator.userAgent,a=new RegExp("MSIE ([0-9]{1,}[.0-9]{0,})");null!=a.exec(r)&&(e=parseFloat(RegExp.$1))}else if("Netscape"==navigator.appName){var r=navigator.userAgent,a=new RegExp("Trident/.*rv:([0-9]{1,}[.0-9]{0,})");null!=a.exec(r)&&(e=parseFloat(RegExp.$1))}return e}
    
    if(getInternetExplorerVersion()==-1){
        // func if NOT IE
	}
    else {
        // func if IE
    };

    // Список городов в шапке
    $('.sity_select').click(function() {
    	$('.site-header .sity-list').slideToggle("fast");
    });

    // скролл к якорям
    $('a[data-href]').click(function () { 
        elementClick = $(this).attr("data-href");
        destination = $(elementClick).offset().top;
        if($.browser.safari){
          $('html,body').animate( { scrollTop: destination }, 1100 );
        }else{
          $('html,body').animate( { scrollTop: destination }, 1100 );
        }
        return false;
    });
  
    // показать карту
    $('.show_map').click(function () {
      $('.map').slideToggle("slow");
        var showText = $('.show_map').text();
        if(showText == 'ПОСМОТРЕТЬ НА КАРТЕ'){
            $('.show_map').text('СВЕРНУТЬ КАРТУ');
        } else {
            $('.show_map').text('ПОСМОТРЕТЬ НА КАРТЕ');
        }
    });

    // fancybox
    $('.fancybox').fancybox({
        openEffect  : 'elastic',
        closeEffect : 'elastic',
        prevEffect : 'elastic',
        nextEffect : 'elastic',
        closeBtn  : true,
        arrows    : true,
        tpl       : {
            wrap : '<div class="fancybox-wrap" tabIndex="-1"><div class="fancybox-skin"><div class="fancybox-outer"><div class="fancybox-inner"></div></div></div></div>',
            next : '<a title="Next" class="fancybox-nav fancybox-next" href="javascript:;"><span></span></a>',
            prev : '<a title="Prev" class="fancybox-nav fancybox-prev" href="javascript:;"><span></span></a>',
            image : '<img class="fancybox-image" src="{href}" alt="" />'
        },
        helpers : {
            title : {
                type : 'inside'
            },
        },
        afterLoad : function() {
            this.title = '<span style="float:left;">' +(this.title ? '' + this.title : '')+ '</span>' + (this.index + 1) + ' из ' + this.group.length ;
        }
    });

    // табы
    $( ".tab-nav li" ).click(function() {
        var data_link = $(this).attr("data-link");
        $('.tab').slideUp(300);
        setTimeout(function() {$('#' + data_link ).slideDown(300);}, 300 );
        $('.tab-nav li').removeClass('active');
        $(this).addClass('active');
    });

}); /*---------------------- END READY FUNCTION ------------------------*/