/**
 * Dynamic Price Frontend Logic
 */
$(document).ready(function() {
    // Listen for changes in range sliders
    $('#dp_width_range, #dp_height_range, #dp_density_range').on('input', function() {
        const id = $(this).attr('id').replace('_range', '');
        const val = $(this).val();
        $(`#${id}`).val(val);
        $(`#${id}_val`).text(val);
        
        updatePreview();
        calculateDynamicPrice();
    });

    // Listen for changes in other fields
    $('#dp_material').on('change', function() {
        calculateDynamicPrice();
    });

    // Initial setup
    updatePreview();
    calculateDynamicPrice();

    function updatePreview() {
        const width = $('#dp_width').val();
        const height = $('#dp_height').val();
        const density = $('#dp_density').val();
        
        // Scale down for preview (max 150px)
        const scale = 0.3;
        const pWidth = Math.max(10, width * scale);
        const pHeight = Math.max(10, height * scale);
        
        $('#dynamic-preview-box').css({
            'width': pWidth + 'px',
            'height': pHeight + 'px',
            'opacity': 0.3 + (density / 100) * 0.7
        });
    }

    function calculateDynamicPrice() {
        const formData = {
            action: 'calculate',
            id_product: id_product,
            token: dynamicprice_token,
            width: $('#dp_width').val(),
            height: $('#dp_height').val(),
            material: $('#dp_material').val(),
            density: $('#dp_density').val(),
            ajax: true
        };

        $('#dp-loader').show();

        $.ajax({
            type: 'POST',
            url: dynamicprice_ajax_url,
            data: formData,
            dataType: 'json',
            success: function(response) {
                $('#dp-loader').hide();
                if (response.success) {
                    $('#computed-price-display').text(response.formatted_price);
                    $('#custom_dynamic_price').val(response.raw_price);
                } else {
                    $('#computed-price-display').text(response.message).css('color', 'red');
                }
            },
            error: function(err) {
                $('#dp-loader').hide();
                console.error("Price calculation error", err);
            }
        });
    }
});
