var pages = {

    handlers: {
        post_error: function(data, txtStatus, jqXHR){
        },
    },

    pages: {

        contact: {

            handlers: {

                click_add_test: function(e){
                    var serialized = pages.pages.contact.root()
                        .find('[name="add-contact"]')
                        .serialize()
                    ;
                    function success_callback(data, txtStatus, jqXHR){
                        pages.pages.contact.handlers.
                            click_add_test_done_base(
                                data,
                                txtStatus,
                                jqXHR,
                                serialized
                            )
                        ;
                    }
                    $.ajax({
                        type: "POST",
                        url: 'ajax.php?core=contact&operand=test',
                        data: serialized,
                        success: success_callback,
                        dataType: 'json',
                    });
                },

                click_add_test_done_base: function(data, txtStatus, jqXHR, s){
                    if(
                        data != undefined &&
                        data.contact != undefined &&
                        data.contact[0] != undefined
                    ){
                        var got = data.contact[0];
                        if(got.signal == '+')
                            alert(got.data);
                            //TODO
                            // 使用统一的“审批系统”接收任务队列。
                            // 不再使用其他方式处理任务。
                            // 甚至可以考虑将上一步的请求（test计算校验值）也
                            // 放进来。
                    }
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
