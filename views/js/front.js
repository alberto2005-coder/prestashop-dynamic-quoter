/**
 * Dynamic Price Frontend Logic
 */
$(document).ready(function() {
    // Listen for changes in custom fields
    $('.dp-calc-field').on('change input', function() {
        calculateDynamicPrice();
    });

    // Initial calculation
    calculateDynamicPrice();

    function calculateDynamicPrice() {
        const formData = {
            action: 'calculate',
            id_product: id_product,
            width: $('#dp_width').val(),
            height: $('#dp_height').val(),
            material: $('#dp_material').val(),
            density: $('#dp_density').val(),
            ajax: true
        };

        $.ajax({
            type: 'POST',
            url: dynamicprice_ajax_url,
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Update visual price
                    $('#computed-price-display').text(response.formatted_price);
                    
                    // Update the hidden input that will be sent to the cart
                    $('#custom_dynamic_price').val(response.raw_price);

                    // Optional: Update PrestaShop's core price display if needed
                    // $('.current-price span[itemprop="price"]').text(response.formatted_price);
                }
            },
            error: function(err) {
                console.error("Price calculation error", err);
            }
        });
    }
});
