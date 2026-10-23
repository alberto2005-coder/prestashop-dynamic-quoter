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
        
        header('Content-Type: application/json');
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

        // Security check: Token validation using timing-safe comparison
        $static_token = (string)Tools::getToken(false);
        $provided_token = (string)Tools::getValue('token');
        if (!hash_equals($static_token, $provided_token)) {
            header('Content-Type: application/json');
            die(json_encode(array(
                'success' => false, 
                'message' => $this->module->trans('Security token expired. Please refresh the page.', [], 'Modules.Dynamicprice.Shop')
            )));
        }

        // Validation: Product ID
        if ($id_product <= 0) {
            header('Content-Type: application/json');
            die(json_encode(array(
                'success' => false,
                'message' => $this->module->trans('Invalid product ID.', [], 'Modules.Dynamicprice.Shop')
            )));
        }

        // Validation: Range checks for dimensions
        $min_dim = (int)Configuration::get('DYNAMICPRICE_MIN_DIM');
        $max_dim = (int)Configuration::get('DYNAMICPRICE_MAX_DIM');

        if ($width < $min_dim || $width > $max_dim || $height < $min_dim || $height > $max_dim || !is_finite($width) || !is_finite($height)) {
            header('Content-Type: application/json');
            die(json_encode(array(
                'success' => false, 
                'message' => $this->module->trans('Dimensions out of allowed range.', [], 'Modules.Dynamicprice.Shop')
            )));
        }

        // Validation: Infill density bounds (0% to 100%)
        if ($density < 0 || $density > 100) {
            header('Content-Type: application/json');
            die(json_encode(array(
                'success' => false, 
                'message' => $this->module->trans('Density must be between 0% and 100%.', [], 'Modules.Dynamicprice.Shop')
            )));
        }

        // Validation: Material whitelist
        $allowed_materials = array('pla', 'abs', 'petg');
        if (!in_array($material, $allowed_materials, true)) {
            header('Content-Type: application/json');
            die(json_encode(array(
                'success' => false, 
                'message' => $this->module->trans('Invalid material selected.', [], 'Modules.Dynamicprice.Shop')
            )));
        }

        // Logic for calculating the price using Back Office settings
        $base_price = Product::getPriceStatic($id_product, true);
        $base_cost = (float)Configuration::get('DYNAMICPRICE_BASE_COST');
        
        // Handling Taxes for the extra cost
        $tax_address_type = Configuration::get('PS_TAX_ADDRESS_TYPE');
        $id_address = isset($this->context->cart->{$tax_address_type}) ? (int)$this->context->cart->{$tax_address_type} : 0;
        $tax_rate = Tax::getProductTaxRate($id_product, $id_address);
        
        $material_multiplier = 1.0;
        switch ($material) {
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

        // Extra cost calculation + Tax
        $extra_cost = ($volume_factor * $base_cost * $material_multiplier);
        $extra_cost_taxed = $extra_cost * (1 + ($tax_rate / 100));

        // Final price formula
        $new_price = ($base_price + $extra_cost_taxed) * $density_factor;

        if ($new_price <= 0 || !is_finite($new_price)) {
            header('Content-Type: application/json');
            die(json_encode(array(
                'success' => false,
                'message' => $this->module->trans('Invalid price calculation.', [], 'Modules.Dynamicprice.Shop')
            )));
        }

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

        header('Content-Type: application/json');
        die(json_encode(array(
            'success' => true,
            'raw_price' => $new_price,
            'formatted_price' => Tools::displayPrice($new_price),
        )));
    }
}
