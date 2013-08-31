var session = {
    
    data: {},

    initialize: function(){
        $(function(){
            session.ajax.initiate();
        });
    },

    ajax: {
        
        initiate: function(){
            setTimeout('session.ajax.initiate()', 1500);
            $.ajax({
                url: 'ajax.php',
                type: 'GET',
                dataType: 'json',
                success: session.ajax.handler.success,
                error: session.ajax.handler.error,
            });
        },

        handler: {
            
            success: function(data, txtStatus, jqXHR){
                var data_empty = true;
                for(var i in data){
                    data_empty = false;
                    break;
                }
                if(true === data_empty){
                    window.location.href="login.php";
                } else {
                    navbar.ajaxHandler(data['navbar']);
                }
            },

            error: function(data, txtStatus, jqXHR){
                window.location.href = 'login.php';
            },

        },

    },
    
};

session.initialize();
