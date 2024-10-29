<?php
// Load Dolibarr environment
if (file_exists('../main.inc.php')) {
    require '../main.inc.php';
} elseif (file_exists('../../main.inc.php')) {
    require '../../main.inc.php';
} elseif (file_exists('../../../main.inc.php')) {
    require '../../../main.inc.php';
} else {
    die('Cannot find main.inc.php');
}

require_once DOL_DOCUMENT_ROOT.'/core/lib/product.lib.php';

$langs->load("products");
$langs->load("myproductsmodule@myproductsmodule");

// Access control
if (!$user->rights->produit->lire) accessforbidden();

$title = $langs->trans("ProductMargins");

// Header
llxHeader('', $title);
print_fiche_titre($title);

// SQL Query
$sql = "SELECT p.ref, p.label, ";
$sql.= "p.price as selling_price, ";
$sql.= "p.cost_price, ";
$sql.= "((p.price - p.cost_price) / p.price * 100) as margin_percent ";
$sql.= "FROM ".MAIN_DB_PREFIX."product as p ";
$sql.= "WHERE p.tosell = 1 ";
$sql.= "AND p.cost_price > 0 ";
$sql.= "ORDER BY margin_percent DESC";

$resql = $db->query($sql);
if ($resql) {
    print '<table class="noborder centpercent">';
    print '<tr class="liste_titre">';
    print '<td>'.$langs->trans("Ref").'</td>';
    print '<td>'.$langs->trans("Label").'</td>';
    print '<td class="right">'.$langs->trans("SellingPrice").'</td>';
    print '<td class="right">'.$langs->trans("CostPrice").'</td>';
    print '<td class="right">'.$langs->trans("Margin").' (%)</td>';
    print '</tr>';

    while ($obj = $db->fetch_object($resql)) {
        print '<tr class="oddeven">';
        print '<td>'.$obj->ref.'</td>';
        print '<td>'.$obj->label.'</td>';
        print '<td class="right">'.price($obj->selling_price).'</td>';
        print '<td class="right">'.price($obj->cost_price).'</td>';
        print '<td class="right">'.price($obj->margin_percent).'%</td>';
        print '</tr>';
    }
    print '</table>';
    
    $db->free($resql);
} else {
    dol_print_error($db);
}

llxFooter();
$db->close();