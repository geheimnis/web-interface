var session = {
    
    data: {},

    initialize: function(){
        setTimeout('session.ajax()', 1500);
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
