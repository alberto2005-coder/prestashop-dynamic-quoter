<?php
/**
 * AJAX Controller for Dynamic Price Calculation
 */

class DynamicPriceAjaxModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();

        if (Tools::getValue('action') == 'calculate') {
            $this->calculatePrice();
        }
        
        die(json_encode(array(
            'success' => false, 
            'message' => $this->module->trans('Invalid action', [], 'Modules.Dynamicprice.Shop')
        )));
    }

    protected function calculatePrice()
    {
        $id_product = (int)Tools::getValue('id_product');
        $width = (float)Tools::getValue('width');
        $height = (float)Tools::getValue('height');
        $material = Tools::getValue('material');
        $density = (int)Tools::getValue('density');

        // Logic for calculating the price
        // This is a placeholder formula. In a real scenario, you might query a table.
        $base_price = Product::getPriceStatic($id_product, true);
        
        $material_multiplier = 1.0;
        switch($material) {
            case 'abs': $material_multiplier = 1.2; break;
            case 'petg': $material_multiplier = 1.5; break;
        }

        $volume_factor = ($width * $height) / 10000; // cm2
        $density_factor = 1 + ($density / 100);

        $new_price = ($base_price + ($volume_factor * $material_multiplier)) * $density_factor;

        // Store the calculation in the session so the cart override can find it
        $this->context->cookie->{'dp_custom_price_'.$id_product} = $new_price;
        $this->context->cookie->write();

        die(json_encode(array(
            'success' => true,
            'raw_price' => $new_price,
            'formatted_price' => Tools::displayPrice($new_price),
        )));
    }
}
