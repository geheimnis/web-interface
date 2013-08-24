var navbar = {

    data: {},

    elements: {
        topbar: null,

    },

    initialize: function(){
        $(function(){
            navbar.elements.topbar = $('#topbar');

        });
    },

    ajaxHandler: function(j){
        navbar
            .setTaskManager(j['unread_task'])
        ;
    },

    setTaskManager: function(unread){
        var target = navbar.elements.topbar.find("[name='task_manager']");
        if(unread > 0){
            target.text(unread).show();
        }else
            target.text('').hide();
        return navbar;
    },

};

navbar.initialize();
