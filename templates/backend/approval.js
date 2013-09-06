//Approval System
// Decide by user, what to be executed by the core command.

var approval = {

    initialize: function(){
        session.ajax.registerHandler('approval', approval.handlers.ajax);
    },

    handlers: {
        ajax: function(j){
            if(j == undefined) return;
            navbar
                .setTaskManager(j['unread_task'])
            ;
        },
    },

};

approval.initialize();
