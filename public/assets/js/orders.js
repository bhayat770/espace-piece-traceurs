function showOrder(mode, var_content, file) {
    $.get(file, ((mode === 1) ? {
        'id_order': var_content,
        'ajax': true
    } : {
        'id_order_return': var_content,
        'ajax': true
    }), function(data) {
        $('#block-order-detail').fadeOut('slow', function() {
            $(this).html(data);
            $('.footab').footable();
            if ($('#order-detail-content .order_cb').length > 0) {
                $('#order-detail-content th input[type=checkbox]').click(function() {
                    $('#order-detail-content td input[type=checkbox]').each(function() {
                        this.checked = $('#order-detail-content th input[type=checkbox]').is(':checked');
                        updateOrderLineDisplay(this);
                    });
                });
                $('#order-detail-content td input[type=checkbox]').click(function() {
                    updateOrderLineDisplay(this);
                });
                $('#order-detail-content td .order_qte_input').keyup(function() {
                    var maxQuantity = parseInt($(this).parent().find('.order_qte_span').text());
                    var quantity = parseInt($(this).val());
                    if (isNaN($(this).val()) && $(this).val() !== '') {
                        $(this).val(maxQuantity);
                    } else {
                        if (quantity > maxQuantity)
                            $(this).val(maxQuantity);
                        else if (quantity < 1)
                            $(this).val(1);
                    }
                });
                $(document).on('click', '.return_quantity_down', function(e) {
                    e.preventDefault();
                    var $input = $(this).parent().parent().find('input');
                    var count = parseInt($input.val()) - 1;
                    count = count < 1 ? 1 : count;
                    $input.val(count);
                    $input.change();
                });
                $(document).on('click', '.return_quantity_up', function(e) {
                    e.preventDefault();
                    var maxQuantity = parseInt($(this).parent().parent().find('.order_qte_span').text());
                    var $input = $(this).parent().parent().find('input');
                    var count = parseInt($input.val()) + 1;
                    count = count > maxQuantity ? maxQuantity : count;
                    $input.val(count);
                    $input.change();
                });
            }
            $('form#sendOrderMessage').submit(function() {
                return sendOrderMessage();
            });
            $(this).fadeIn('slow', function() {
                $.scrollTo(this, 1200);
            });
        });
    });
}