<?php
/**
 * Product Override for Dynamic Pricing
 */

class Product extends ProductCore
{
    /**
     * Override getPriceStatic to return custom dynamic price if available in cookie/session
     */
    public static function getPriceStatic(
        $id_product,
        $usetax = true,
        $id_product_attribute = null,
        $decimals = 6,
        $divisor = null,
        $only_reduc = false,
        $usereduc = true,
        $quantity = 1,
        $force_associated_tax = false,
        $id_customer = null,
        $id_cart = null,
        $id_address = null,
        &$specific_price_output = null,
        $with_eco_tax = true,
        $use_group_reduction = true,
        Context $context = null,
        $use_customer_price = true
    ) {
        $price = parent::getPriceStatic(
            $id_product,
            $usetax,
            $id_product_attribute,
            $decimals,
            $divisor,
            $only_reduc,
            $usereduc,
            $quantity,
            $force_associated_tax,
            $id_customer,
            $id_cart,
            $id_address,
            $specific_price_output,
            $with_eco_tax,
            $use_group_reduction,
            $context,
            $use_customer_price
        );

        if (!$context) {
            $context = Context::getContext();
        }

        // Check if there is a custom price stored in the cookie for this product
        // Note: In a production environment, you should probably store this in a database table 
        // linked to the cart_product to avoid session clear issues.
        if (isset($context->cookie->{'dp_custom_price_'.$id_product})) {
            $custom_price = (float)$context->cookie->{'dp_custom_price_'.$id_product};
            if ($custom_price > 0) {
                return $custom_price;
            }
        }

        return $price;
    }
}
