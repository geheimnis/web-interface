var session = {
    
    _ajaxHandlers: {},
    
    data: {},

    initialize: function(){
        $(function(){
            session.ajax.initiate();
        });
    },

    ajax: {

        _failedCount: 0,
        
        initiate: function(){
            setTimeout('session.ajax.initiate()', 1500);
            $.ajax({
                url: 'ajax.php',
                type: 'GET',
                dataType: 'json',
                success: session.ajax.handler.success,
                error: session.ajax.handler.error,
                cache: false,
            });
        },

        registerHandler: function(namespace, handler){
            if(session._ajaxHandlers[namespace] == undefined)
                session._ajaxHandlers[namespace] = [handler,];
            else
                session._ajaxHandlers[namespace].push(handler);
        },

        handler: {
            
            success: function(data, txtStatus, jqXHR){
                var data_empty = true;
                for(var i in data){
                    data_empty = false;
                    break;
                }
                if(true === data_empty){
                    session.ajax.handler.error(data, txtStatus, jqXHR);
                } else {
                    session.ajax._failedCount = 0;
                    for(var namespace in data)
                        if(session._ajaxHandlers[namespace] != undefined)
                            for(
                                var handler in
                                session._ajaxHandlers[namespace]
                            )
                                session._ajaxHandlers[namespace][handler](
                                    data[namespace]
                                );
                }
            },

            error: function(data, txtStatus, jqXHR){
                session.ajax._failedCount += 1;
                if(session.ajax._failedCount == 1){
                    notification.notify(
                        '与系统的连接中断',
                        '可能是暂时错误，也可能由于您的登录失效，或者其他网络问题导致。<br /><strong>如问题持续，30秒内将退出系统。</strong>',
                        'error'
                    );
                } else if(session.ajax._failedCount == 10){
                    notification.notify(
                        '系统连接未恢复',
                        '15秒后将自动退出系统'
                    );
                }
                if(session.ajax._failedCount > 20)
                    window.location.href = 'login.php';
            },

        },

    },
    
};

session.initialize();
