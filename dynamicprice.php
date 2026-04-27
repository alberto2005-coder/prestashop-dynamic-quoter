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
            $this->registerHook('displayProductPriceBlock') &&
            $this->registerHook('displayAdminOrderMain') &&
            $this->registerHook('displayOrderDetail') &&
            $this->registerHook('actionCartSave') &&
            $this->installSql() &&
            Configuration::updateValue('DYNAMICPRICE_PLA_MULT', 1.0) &&
            Configuration::updateValue('DYNAMICPRICE_ABS_MULT', 1.2) &&
            Configuration::updateValue('DYNAMICPRICE_PETG_MULT', 1.5) &&
            Configuration::updateValue('DYNAMICPRICE_BASE_COST', 0.05) &&
            Configuration::updateValue('DYNAMICPRICE_MIN_DIM', 10) &&
            Configuration::updateValue('DYNAMICPRICE_MAX_DIM', 500);
    }

    protected function installSql()
    {
        $sql = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'dynamicprice_info` (
            `id_dynamicprice_info` INT(11) NOT NULL AUTO_INCREMENT,
            `id_cart` INT(11) NOT NULL,
            `id_product` INT(11) NOT NULL,
            `width` FLOAT NOT NULL,
            `height` FLOAT NOT NULL,
            `material` VARCHAR(50) NOT NULL,
            `density` INT(11) NOT NULL,
            `price` DECIMAL(20, 6) NOT NULL,
            `date_add` DATETIME NOT NULL,
            PRIMARY KEY (`id_dynamicprice_info`),
            KEY `id_cart_product` (`id_cart`, `id_product`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

        return Db::getInstance()->execute($sql);
    }

    public function uninstall()
    {
        return parent::uninstall() &&
            $this->uninstallSql() &&
            Configuration::deleteByName('DYNAMICPRICE_PLA_MULT') &&
            Configuration::deleteByName('DYNAMICPRICE_ABS_MULT') &&
            Configuration::deleteByName('DYNAMICPRICE_PETG_MULT') &&
            Configuration::deleteByName('DYNAMICPRICE_BASE_COST') &&
            Configuration::deleteByName('DYNAMICPRICE_MIN_DIM') &&
            Configuration::deleteByName('DYNAMICPRICE_MAX_DIM');
    }

    protected function uninstallSql()
    {
        return Db::getInstance()->execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'dynamicprice_info`');
    }

    /**
     * Back office configuration page
     */
    public function getContent()
    {
        $output = '';

        if (Tools::isSubmit('submit' . $this->name)) {
            $pla = (float)Tools::getValue('DYNAMICPRICE_PLA_MULT');
            $abs = (float)Tools::getValue('DYNAMICPRICE_ABS_MULT');
            $petg = (float)Tools::getValue('DYNAMICPRICE_PETG_MULT');
            $base = (float)Tools::getValue('DYNAMICPRICE_BASE_COST');
            $min = (int)Tools::getValue('DYNAMICPRICE_MIN_DIM');
            $max = (int)Tools::getValue('DYNAMICPRICE_MAX_DIM');

            Configuration::updateValue('DYNAMICPRICE_PLA_MULT', $pla);
            Configuration::updateValue('DYNAMICPRICE_ABS_MULT', $abs);
            Configuration::updateValue('DYNAMICPRICE_PETG_MULT', $petg);
            Configuration::updateValue('DYNAMICPRICE_BASE_COST', $base);
            Configuration::updateValue('DYNAMICPRICE_MIN_DIM', $min);
            Configuration::updateValue('DYNAMICPRICE_MAX_DIM', $max);

            $output .= $this->displayConfirmation($this->trans('Settings updated', [], 'Modules.Dynamicprice.Admin'));
        }

        return $output . $this->renderForm();
    }

    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_callbacks = true;

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submit' . $this->name;
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->trans('Settings', [], 'Modules.Dynamicprice.Admin'),
                    'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'text',
                        'label' => $this->trans('Base Cost (per unit)', [], 'Modules.Dynamicprice.Admin'),
                        'name' => 'DYNAMICPRICE_BASE_COST',
                        'size' => 20,
                        'required' => true,
                        'desc' => $this->trans('The base cost used in the formula.', [], 'Modules.Dynamicprice.Admin'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->trans('PLA Multiplier', [], 'Modules.Dynamicprice.Admin'),
                        'name' => 'DYNAMICPRICE_PLA_MULT',
                        'size' => 20,
                        'required' => true,
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->trans('ABS Multiplier', [], 'Modules.Dynamicprice.Admin'),
                        'name' => 'DYNAMICPRICE_ABS_MULT',
                        'size' => 20,
                        'required' => true,
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->trans('PETG Multiplier', [], 'Modules.Dynamicprice.Admin'),
                        'name' => 'DYNAMICPRICE_PETG_MULT',
                        'size' => 20,
                        'required' => true,
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->trans('Minimum Dimension (mm)', [], 'Modules.Dynamicprice.Admin'),
                        'name' => 'DYNAMICPRICE_MIN_DIM',
                        'size' => 20,
                        'required' => true,
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->trans('Maximum Dimension (mm)', [], 'Modules.Dynamicprice.Admin'),
                        'name' => 'DYNAMICPRICE_MAX_DIM',
                        'size' => 20,
                        'required' => true,
                    ),
                ),
                'submit' => array(
                    'title' => $this->trans('Save', [], 'Modules.Dynamicprice.Admin'),
                ),
            ),
        );

        return $helper->generateForm(array($fields_form));
    }

    protected function getConfigFormValues()
    {
        return array(
            'DYNAMICPRICE_PLA_MULT' => Configuration::get('DYNAMICPRICE_PLA_MULT'),
            'DYNAMICPRICE_ABS_MULT' => Configuration::get('DYNAMICPRICE_ABS_MULT'),
            'DYNAMICPRICE_PETG_MULT' => Configuration::get('DYNAMICPRICE_PETG_MULT'),
            'DYNAMICPRICE_BASE_COST' => Configuration::get('DYNAMICPRICE_BASE_COST'),
            'DYNAMICPRICE_MIN_DIM' => Configuration::get('DYNAMICPRICE_MIN_DIM'),
            'DYNAMICPRICE_MAX_DIM' => Configuration::get('DYNAMICPRICE_MAX_DIM'),
        );
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
            'dynamicprice_token' => Tools::getToken(false),
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

    /**
     * Display custom dimensions in the back office order page
     */
    public function hookDisplayAdminOrderMain($params)
    {
        $order = new Order((int)$params['id_order']);
        $id_cart = (int)$order->id_cart;

        $custom_data = Db::getInstance()->executeS('SELECT * FROM ' . _DB_PREFIX_ . 'dynamicprice_info WHERE id_cart = ' . (int)$id_cart);

        if (!$custom_data) {
            return;
        }

        $this->context->smarty->assign(array(
            'custom_data' => $custom_data,
        ));

        return $this->display(__FILE__, 'views/templates/admin/order_info.tpl');
    }

    /**
     * Display custom dimensions in the customer's order history
     */
    public function hookDisplayOrderDetail($params)
    {
        $id_order = (int)$params['order']->id;
        $order = new Order($id_order);
        $id_cart = (int)$order->id_cart;

        $custom_data = Db::getInstance()->executeS('SELECT * FROM ' . _DB_PREFIX_ . 'dynamicprice_info WHERE id_cart = ' . (int)$id_cart);

        if (!$custom_data) {
            return;
        }

        $this->context->smarty->assign(array(
            'custom_data' => $custom_data,
        ));

        return $this->display(__FILE__, 'views/templates/hook/order_info_front.tpl');
    }
}
