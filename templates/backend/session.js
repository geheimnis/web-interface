var session = {
    
    data: {},

    initialize: function(){
        $(function(){
            session.ajax();
        });
    },

    ajax: function(){
        setTimeout('session.ajax()', 1500);
        $.get({
            url: 'ajax.php', 
            success: session.ajaxHandler,
        });
    },

    ajaxHandler: function(data, txtStatus, jqXHR){
    },

};

session.initialize();
