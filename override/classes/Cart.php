<?php
/**
 * Cart Override for Dynamic Pricing
 */

class Cart extends CartCore
{
    /**
     * Override to ensure the custom price is preserved when updating quantities or checking out.
     */
    public function getProductPrice($id_product, $usetax = true, $id_product_attribute = null, $quantity = 1)
    {
        $context = Context::getContext();
        
        // Check if this cart/product combination has a dynamic price
        if (isset($context->cookie->{'dp_custom_price_'.$id_product})) {
            return (float)$context->cookie->{'dp_custom_price_'.$id_product};
        }

        return parent::getProductPrice($id_product, $usetax, $id_product_attribute, $quantity);
    }

    /**
     * Optional: If you want to support multiple different configurations of the SAME product
     * in the cart, you would need to use PrestaShop's Customization system.
     * This override ensures the basic dynamic price is fetched.
     */
}
