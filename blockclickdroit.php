<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class BlockClickDroit extends Module
{
    public function __construct()
    {
        $this->name = 'blockclickdroit';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Doryan Fourrichon';
        $this->ps_versions_compliancy = [
            'min' => '1.6',
            'max' => _PS_VERSION_
        ];
        
        //récupération du fonctionnement du constructeur de la méthode __construct de Module
        parent::__construct();
        $this->bootstrap = true;

        $this->displayName = $this->l('Block Click Droit');
        $this->description = $this->l('Module qui empêche le clic droit sur le site');

        $this->confirmUninstall = $this->l('Do you want to delete this module');

    }

    public function install()
    {
        if (!parent::install() ||
        !Configuration::updateValue('BLOCKCLICKDROIT',0) ||
        !$this->registerHook('displayHeader')
        ) {
            return false;
        }
            return true;
    }

    public function uninstall()
    {
        if (parent::install() ||
        !Configuration::deleteByName('BLOCKCLICKDROIT') ||
        !$this->unregisterHook('displayHeader')
        ) {
            return false;
        }
            return true;
    }

    public function getContent()
    {
        return $this->postProcess().$this->renderForm();
    }

    public function renderForm()
    {
        $field_form[0]['form'] = [
            'legend' => [
                'title' => $this->l('Setings'),
            ],
            'input' => [
                [
                    'type' => 'switch',
                        'label' => $this->l('Block right click button'),
                        'name' => 'BLOCKCLICKDROIT',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'label2_on',
                                'value' => 1,
                                'label' => $this->l('Oui')
                            ),
                            array(
                                'id' => 'label2_off',
                                'value' => 0,
                                'label' => $this->l('Non')
                            )
                        )
                ],
            ],
            'submit' => [
                'title' => $this->l('save'),
                'class' => 'btn btn-primary',
                'name' => 'saving'
            ]
        ];

        $helper = new HelperForm();
        $helper->module  = $this;
        $helper->name_controller = $this->name;
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->fields_value['BLOCKCLICKDROIT'] = Configuration::get('BLOCKCLICKDROIT');
        return $helper->generateForm($field_form);
    }

    public function postProcess()
    {
        if (Tools::isSubmit('saving')) {
                if (Validate::isBool(Tools::getValue('BLOCKCLICKDROIT'))) {
                    Configuration::updateValue('BLOCKCLICKDROIT',Tools::getValue('BLOCKCLICKDROIT'));

                    return $this->displayConfirmation('Bien enregistré !');
                }
        }
    }

    public function hookDisplayHeader($params)
    {
        if (Configuration::get('BLOCKCLICKDROIT') == 1) {
            $this->context->controller->registerJavascript('js_script_block','modules/blockclickdroit/views/js/script.js');
        }
    }
}