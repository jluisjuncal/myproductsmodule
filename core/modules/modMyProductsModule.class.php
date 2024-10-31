<?php
include_once DOL_DOCUMENT_ROOT .'/core/modules/DolibarrModules.class.php';

class modMyProductsModule extends DolibarrModules
{
    public function __construct($db)
    {
        global $langs, $conf;

        $this->db = $db;
        $this->numero = 502300;
        $this->rights_class = 'myproductsmodule';
        $this->family = "products";
        $this->module_position = '50';
        $this->name = preg_replace('/^mod/i', '', get_class($this));
        $this->description = "Análisis de Productos";
        $this->descriptionlong = "Módulo de análisis y reportes avanzados de productos";
        $this->editor_name = 'jluisjuncal';
        $this->version = '0.8';
        $this->const_name = 'MAIN_MODULE_'.strtoupper($this->name);
        $this->picto = 'product';
        
        $this->menu = array();
        
        $this->menu[0] = array(
            'fk_menu'  => 'fk_mainmenu=products',
            'type'     => 'left',
            'titre'    => 'ProductAnalysis',
            'mainmenu' => 'products',
            'leftmenu' => 'myproductsmodule',
            'url'      => '/myproductsmodule/index.php',
            'langs'    => 'myproductsmodule@myproductsmodule',
            'position' => 100,
            'enabled'  => '1',
            'perms'    => '$user->rights->produit->lire',
            'target'   => '',
            'user'     => 0
        );
        
        $this->menu[1] = array(
            'fk_menu'  => 'fk_mainmenu=products,fk_leftmenu=myproductsmodule',
            'type'     => 'left',
            'titre'    => 'TopSellingProducts',
            'url'      => '/myproductsmodule/include/top_selling.php',
            'langs'    => 'myproductsmodule@myproductsmodule',
            'position' => 101,
            'enabled'  => '1',
            'perms'    => '$user->rights->produit->lire',
            'target'   => '',
            'user'     => 0
        );
        
        $this->menu[2] = array(
            'fk_menu'  => 'fk_mainmenu=products,fk_leftmenu=myproductsmodule',
            'type'     => 'left',
            'titre'    => 'LowStockProducts',
            'url'      => '/myproductsmodule/include/low_stock.php',
            'langs'    => 'myproductsmodule@myproductsmodule',
            'position' => 102,
            'enabled'  => '1',
            'perms'    => '$user->rights->produit->lire',
            'target'   => '',
            'user'     => 0
        );

        $this->menu[3] = array(
            'fk_menu'  => 'fk_mainmenu=products,fk_leftmenu=myproductsmodule',
            'type'     => 'left',
            'titre'    => 'LowMarginProducts',
            'url'      => '/myproductsmodule/include/low_margin.php',
            'langs'    => 'myproductsmodule@myproductsmodule',
            'position' => 103,
            'enabled'  => '1',
            'perms'    => '$user->rights->produit->lire',
            'target'   => '',
            'user'     => 0
        );
    }

    public function init($options = '')
    {
        $sql = array();
        return $this->_init($sql, $options);
    }
}