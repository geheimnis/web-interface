var notification = {

    notify: function(title, message, type){
        if(type == undefined) type = 'info';
        $.pnotify({
            title: title,
            text: message,
            type: type,
            styling: 'bootstrap',
            delay: 5000,
        });
    },

};
