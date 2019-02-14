var Weltpixel_LayeredNavigation = Weltpixel_LayeredNavigation || {};

Weltpixel_LayeredNavigation.init = function(options) {
    this.options = options;
    this.observeTopFilterToggle();
    this.bindEvents();
}


Weltpixel_LayeredNavigation.bindEvents = function() {
    var that = this;
    jQuery(document.body).on('click', '.block-layered-nav .show_more span', function(){
        jQuery(this).parent().parent().find('li.attribute_more_less').show();
        jQuery(this).parent().hide();
        jQuery(this).parent().next().show();
    });

    jQuery(document.body).on('click', '.block-layered-nav .show_less span', function(){
        jQuery(this).parent().parent().find('li.attribute_more_less').hide();
        jQuery(this).parent().hide();
        jQuery(this).parent().prev().show();
    });

    jQuery(document.body).on('click', 'a.filters-close', function(e){
        e.preventDefault();
        var $filterBtn = jQuery('.toggle-filters');
        that.LayeredNavToggle($filterBtn);
    });

}

Weltpixel_LayeredNavigation.LayeredNavToggle = function(button) {
    var filtersContainer = jQuery('.block-layered-nav').parent(),
        colMain = jQuery('.col-main.main'),
        filterOverlay = colMain.find('.filter-overlay');

    if (!filtersContainer.hasClass('open')) {
        filtersContainer.animate({
            left: '0'
        },{
            duration: 500,
            start: function() {
                filterOverlay.show();
                jQuery('body').addClass('no-scroll');
                filtersContainer.addClass('open');
                button.find('span').text('See Results');
            },
            queue: false
        });
    } else {
        filtersContainer.animate({
            left: '-100%'
        },{
            duration: 500,
            start: function() {
                filterOverlay.hide();
                jQuery('body').removeClass('no-scroll');
                filtersContainer.removeClass('open');
                button.find('span').text('Open Filters');
            },
            queue: false
        });
    }
};

Weltpixel_LayeredNavigation.filterNamesToggle = function() {

    $$('.filters-close').invoke('observe','click',function(event){
        if ($$('.trigger-sign').length) {
            $$('.trigger-sign')[0].select('i').each(function(x) {
                if(x.hasClassName('icon-line-minus') == true) {
                    if (x.hasClassName('icon-filter') == false) {
                        x.removeClassName('icon-line-minus').addClassName('icon-line-plus');
                    }
                }
                else {
                    if (x.hasClassName('icon-filter') == false) {
                        x.removeClassName('icon-line-plus').addClassName('icon-line-minus');
                    }

                }
            });
        }
    });


    $$('.filter-name').invoke('observe','click',function(event){
        var element = event.findElement('p');
        if(!element.next().hasClassName('current'))
        {
            $$('.filter-container').each(function(p) {
                p.select('.current').each(function(x) {
                    x.removeClassName('current');
                });
            });
            element.addClassName('current');
            element.next().addClassName('current');
        } else{
            $$('.filter-container').each(function(p) {
                p.select('.current').each(function(x) {
                    x.removeClassName('current');
                });
            });
        }
    });


     $$('.trigger-sign').invoke('observe','click',function(event){
        $(this).select('i').each(function(x) {
            if(x.hasClassName('icon-line-minus') == true) {
                if (x.hasClassName('icon-filter') == false) {
                    x.removeClassName('icon-line-minus').addClassName('icon-line-plus');
                }
            }
            else {
                if (x.hasClassName('icon-filter') == false) {
                    jQuery('.block-layered-nav').find('.filter-name:first').addClass('current');
                    jQuery('.block-layered-nav').find('.filter-content:first').addClass('current');
                    x.removeClassName('icon-line-plus').addClassName('icon-line-minus');
                }

            }
        });

        if($(this).next().hasClassName('trigger-content') == true) {
            $(this).next().toggle();
        }
        else {
        //$(this).select('trigger-content').toggle();
        }
    });

}
Weltpixel_LayeredNavigation.observeTopFilterToggle = function() {
    $$('.filter-button').invoke('observe','click',function(event){
        $$('.block-layered-nav').each(function(c) {
            if( c.hasClassName('hidden') ){
                c.removeClassName('hidden');
            }
            else {
                c.addClassName('hidden');
            }
        });
    });


    this.filterNamesToggle();

}

// Click outside filters close
jQuery(document).mouseup(function (e)
{
    var container = jQuery(".filter-container");

    if (!container.is(e.target) // if the target of the click isn't the container...
        && container.has(e.target).length === 0) // ... nor a descendant of the container
    {
        jQuery(".filter-container .filter-name"). removeClass('current');
        jQuery(".filter-container .filter-name").next().removeClass('current');
    }
});