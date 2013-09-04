var pages = {

    pages: {

        contact: {
            initiate: function(){
            },
        },
        codebook: {
            initiate: function(){
                notification.notify(
                    'title',
                    'body'
                );
            },
        },
        pkicomm: {
            initiate: function(){
            },
        },
        tool: {
            initiate: function(){
            },
        },

    },

    show: function(which){
        $('#page-area').children('div').hide();
        $('#page-area [name="' + which + '"]').show();
        pages.pages[which].initiate();
    },

    initialize: function(){
        $(function(){
            pages.show('contact');
            $('.gaui-accordion').accordion();
        });
    },

};
pages.initialize();
