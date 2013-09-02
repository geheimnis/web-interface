var navbar = {

    data: {},

    initialize: function(){
        $(function(){
            $('#topbar li[name]').click(function(){
                pages.show($(this).attr('name'));
            });
        });
    },

    ajaxHandler: function(j){
        if(j == undefined) return;
        navbar
            .setTaskManager(j['unread_task'])
        ;
    },

    setTaskManager: function(unread){
        var target = $('#topbar').find("[name='task_manager']");
        if(unread > 0){
            target.text(unread).show();
        }else
            target.text('').hide();
        return navbar;
    },

};

navbar.initialize();
