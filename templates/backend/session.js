var session = {
    
    data: {},

    initialize: function(){
        $(function(){
            session.ajax();
        });
    },

    ajax: function(){
        setTimeout('session.ajax()', 1500);
        $.getJSON('ajax.php', {}, session.ajaxHandler);
    },

    ajaxHandler: function(data, txtStatus, jqXHR){
        navbar.ajaxHandler(data['navbar']);
    },

};

session.initialize();
