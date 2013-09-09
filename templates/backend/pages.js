var pages = {

    handlers: {
        post_error: function(data, txtStatus, jqXHR){
        },

        task_added: function(data, txtStatus, jqXHR){
            notification.notify(
                '新任务已经添加',
                '请使用右上角的任务审批系统查看和批准这一任务。'
            );
        },
    },

    pages: {

        contact: {

            handlers: {

                click_add: function(e){
                    var serialized = pages.pages.contact.root()
                        .find('[name="add-contact"]')
                        .serialize()
                    ;
                    $.ajax({
                        type: "POST",
                        url: 'ajax.php?core=identity&operand=add',
                        data: serialized,
                        success: pages.handlers.task_added,
                        dataType: 'json',
                    });
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
                    .click(pages.pages.contact.handlers.click_add)
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
