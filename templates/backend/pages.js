var pages = {

    pages: {

        contact: {
            root: function(){ return $('#page-area [name="contact"]'); },
            initialize: function(){
                pages.pages.contact.root()
                    .find('[name="main-accordion"]')
                    .accordion({
                        collapsible: true,
                        active: false,
                    })
                ;

                return pages.pages;
            },
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
            pages.pages.
                contact.initialize()
            ;
            pages.show('contact');
        });
    },

};
pages.initialize();
