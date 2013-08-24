var session = {
    
    data: {},

    initialize: function(){
        $(function(){
            session.ajax();
        });
    },

    ajax: function(){
        setTimeout('session.ajax()', 1500);
        $.get('ajax.php', {}, session.ajaxHandler);
    },

    ajaxHandler: function(data, txtStatus, jqXHR){
    },

};

session.initialize();
