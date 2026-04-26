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

        // Logic for calculating the price using Back Office settings
        $base_price = Product::getPriceStatic($id_product, true);
        $base_cost = (float)Configuration::get('DYNAMICPRICE_BASE_COST');
        
        $material_multiplier = 1.0;
        switch($material) {
            case 'pla': 
                $material_multiplier = (float)Configuration::get('DYNAMICPRICE_PLA_MULT'); 
                break;
            case 'abs': 
                $material_multiplier = (float)Configuration::get('DYNAMICPRICE_ABS_MULT'); 
                break;
            case 'petg': 
                $material_multiplier = (float)Configuration::get('DYNAMICPRICE_PETG_MULT'); 
                break;
        }

        $volume_factor = ($width * $height) / 10000; // cm2
        $density_factor = 1 + ($density / 100);

        // Formula: (Product Base Price + (Dimensions * Base Cost * Material)) * Density
        $new_price = ($base_price + ($volume_factor * $base_cost * $material_multiplier)) * $density_factor;

        // Store the calculation in the session so the cart override can find it
        $this->context->cookie->{'dp_custom_price_'.$id_product} = $new_price;
        $this->context->cookie->write();

        // Persist to database
        if (!$this->context->cart->id) {
            $this->context->cart->add();
            $this->context->cookie->id_cart = (int)$this->context->cart->id;
        }

        // Check if entry already exists for this cart and product
        $id_info = Db::getInstance()->getValue('SELECT id_dynamicprice_info FROM ' . _DB_PREFIX_ . 'dynamicprice_info 
            WHERE id_cart = ' . (int)$this->context->cart->id . ' AND id_product = ' . (int)$id_product);

        if ($id_info) {
            Db::getInstance()->update('dynamicprice_info', array(
                'width' => $width,
                'height' => $height,
                'material' => pSQL($material),
                'density' => $density,
                'price' => $new_price,
                'date_add' => date('Y-m-d H:i:s'),
            ), 'id_dynamicprice_info = ' . (int)$id_info);
        } else {
            Db::getInstance()->insert('dynamicprice_info', array(
                'id_cart' => (int)$this->context->cart->id,
                'id_product' => (int)$id_product,
                'width' => $width,
                'height' => $height,
                'material' => pSQL($material),
                'density' => $density,
                'price' => $new_price,
                'date_add' => date('Y-m-d H:i:s'),
            ));
        }

        die(json_encode(array(
            'success' => true,
            'raw_price' => $new_price,
            'formatted_price' => Tools::displayPrice($new_price),
        )));
    }
}
