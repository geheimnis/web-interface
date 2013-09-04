var pages = {

    handlers: {
        post_error: function(data, txtStatus, jqXHR){
        },
    },

    pages: {

        contact: {

            handlers: {

                click_add_test: function(e){
                    $.ajax({
                        type: "POST",
                        url: 'ajax.php?core=contact&operand=test',
                        data: 
                            pages.pages.contact.root()
                                .find('[name="add-contact"]')
                                .serialize(),
                        success:
                            pages.pages.contact.handlers.click_add_test_done,
                        dataType: 'text',
                    });
                },

                click_add_test_done: function(data, txtStatus, jqXHR){
                    alert(data);
                },

            },

            root: function(){ return $('#page-area [name="contact"]'); },

            initialize: function(){
                pages.pages.contact.root()
                    .find('[name="main-accordion"]')
                    .accordion({
                        collapsible: true,
                        active: false,
                    })
                ;
                pages.pages.contact.root()
                    .find('[name="add-contact"] button[name="submit"]')
                    .click(pages.pages.contact.handlers.click_add_test)
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
