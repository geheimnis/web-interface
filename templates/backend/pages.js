var pages = {

    pages: {

        contact: {
            initiate: function(){
            },
        },
        codebook: {
            initiate: function(){
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
        });
    },

};
pages.initialize();
