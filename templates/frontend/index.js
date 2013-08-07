function page_switch(which, page_group){
    var activated = $(which).attr('name');
    $('.ga-paging-page[data-pagegroup="' + page_group + '"]').each(function(){
        if($(this).attr('data-pagename') != activated)
            $(this).hide();
        else
            $(this).show();
    });
    $('ul.ga-paging-links[data-pagegroup="' + page_group + '"] > li').each(function(){
        if($(this).attr('name') != activated)
            $(this).removeClass('active');
        else
            $(this).addClass('active');
    });
}

$(function(){
    $('#jswarn').hide();
    $('#root').show();
    
    $('ul.ga-paging-links').each(function(){
        var page_group_name = $(this).attr('data-pagegroup');
        $(this).children('li').click(function(){
            page_switch(this, page_group_name);
        });
    });

    $('.ga-index').click(function(){
        $('#page-home').hide();
        $('#page-content').show();
        $('.navbar > .ga-paging-links > li[name="' + $(this).attr('data-index') + '"]').click();
    });
});
