<?php
/**
 * Dynamic Price Calculator Module
 * 
 * @author  Alberto Ortiz
 * @license http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * @version 1.0.0
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class DynamicPrice extends Module
{
    public function __construct()
    {
        $this->name = 'dynamicprice';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Alberto Ortiz';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = array('min' => '1.7.0.0', 'max' => _PS_VERSION_);
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->trans('Dynamic Price Calculator', [], 'Modules.Dynamicprice.Admin');
        $this->description = $this->trans('Calculates product prices in real-time based on custom user parameters.', [], 'Modules.Dynamicprice.Admin');

        $this->confirmUninstall = $this->trans('Are you sure you want to uninstall?', [], 'Modules.Dynamicprice.Admin');
    }

    public function install()
    {
        return parent::install() &&
            $this->registerHook('header') &&
            $this->registerHook('displayProductAdditionalInfo') &&
            $this->registerHook('displayProductPriceBlock');
    }

    public function uninstall()
    {
        return parent::uninstall();
    }

    /**
     * Hook Header to include JS/CSS
     */
    public function hookHeader()
    {
        $this->context->controller->addJS($this->_path . 'views/js/front.js');
        $this->context->controller->addCSS($this->_path . 'views/css/front.css');
        
        Media::addJsDef(array(
            'dynamicprice_ajax_url' => $this->context->link->getModuleLink($this->name, 'ajax', array(), true),
            'id_product' => (int)Tools::getValue('id_product')
        ));
    }

    /**
     * Hook to display custom fields on product page
     */
    public function hookDisplayProductAdditionalInfo($params)
    {
        // Only show for products that need it (you could add a configuration per product)
        $this->context->smarty->assign(array(
            'calculator_title' => $this->trans('Custom Dimensions', [], 'Modules.Dynamicprice.Shop'),
        ));

        return $this->display(__FILE__, 'views/templates/hook/product_fields.tpl');
    }
}
