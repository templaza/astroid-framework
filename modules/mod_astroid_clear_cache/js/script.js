jQuery(function($){
    $('.astroid-clear-cache').on('click', function (e){
        e.preventDefault();
        var $this   =   $(this);
        $.ajax({
            url: "index.php?option=com_ajax&astroid=clear-cache",
            beforeSend: function(){
                $this.find('.icon-refresh').addClass('icon-spin');
            },
            success: function (response) {
                if (response.status === 'success') {
                    $.notify("Astroid Cache Cleared.", "success");
                    $.ajax({
                        url: "index.php?option=com_ajax&astroid=clear-joomla-cache",
                        success: function (response) {
                            if (response.status === 'success') {
                                $.notify("Joomla Cache Cleared.", "success");
                                $this.find('.icon-refresh').removeClass('icon-spin');
                            }
                        }
                    });
                }
            }
        });
    });
});
