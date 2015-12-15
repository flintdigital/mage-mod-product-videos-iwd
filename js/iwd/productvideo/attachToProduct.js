/**
 * Created by Kate on 17.03.15.
 */
$ji(document).ready(function(){
    $ji(document).on('change', '.ajax-video-attach', function(){
        var _this = $ji(this);
        var id = _this.val();
        var loaderId = 'loader-' + id;
        var loader = $ji('<i>')
            .attr('class', 'fa fa-circle-o-notch fa-spin')
            .attr('id', loaderId);
        $j(this).wrap(loader);
        _this.hide();

        var checked = _this.prop('checked') ? 1 : 0;

        var data = {
            form_key: FORM_KEY,
            product_id: _this.data('productId'),
            video_id: id,
            attach: checked
        };

        $ji.ajax({
            url: _this.data('url'),
            type: 'post',
            data: data,
            dataType: 'json',
            success: function(response) {
                _this.show();
                _this.unwrap();
                if (typeof(response.error) != 'undefined') {
                    $ji('#messages').html('<ul class="messages"><li class="error-msg"><ul><li>' + response.error + '</li></ul></li></ul>');
                }
            }
        });
    });
});