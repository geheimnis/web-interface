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

    setTaskManager: function(unread){
        var target = navbar.elements.topbar.find("[name='task_manager']");
        if(unread > 0){
            target.text(unread).show();
        }else
            target.text('').hide();
    },

};

navbar.initialize();
