jQuery(function($){
    if ($('.as-form-builder').length) {
        $(document).on('submit', '.as-form-builder' , function (e) {
            e.preventDefault();
            var request = {},
                $this   = $(this),
                data    = $this.serializeArray();
            let id = Date.now() * 1000 + Math.random() * 1000;
            id = id.toString(16).replace(/\./g, "").padEnd(14, "0")+Math.trunc(Math.random() * 100000000);
            for (let i = 0; i < data.length; i++) {
                request[data[i]['name']] = data[i]['value'];
            }
            request[$this.find('.token').attr('name')] = 1;
            $.ajax({
                type   : 'POST',
                url    : $this.attr('action')+'&t='+id,
                data   : request,
                beforeSend: function(){
                    $this.find('.as-formbuilder-status').empty();
                    $this.find('.as-form-builer-submit').attr('disabled', 'disabled');
                },
                success: function (response) {
                    if (response.status === 'success') {
                        $this.find('.as-formbuilder-status').append('<div class="alert alert-success" role="alert">'+response.message+'</div>');
                        $this.trigger("reset");
                        $this.find('.g-recaptcha').each(function(){
                            grecaptcha.reset(this);
                        })
                    } else {
                        $this.find('.as-formbuilder-status').append('<div class="alert alert-danger" role="alert">'+response.message+'</div>');
                    }
                    $this.find('.as-form-builer-submit').removeAttr('disabled');
                }
            });
        });
    }
});