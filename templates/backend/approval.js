//Approval System
// Decide by user, what to be executed by the core command.

var approval = {

    description: {
        'identity-add': function(j){
            return 
                '* 添加新联系人身份\n' + 
                '* 请注意验证各项中的每个字符！' 
            ;
        },
    },

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
