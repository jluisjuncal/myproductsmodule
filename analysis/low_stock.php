<?php
require '../../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/product.lib.php';

$langs->load("products");
$langs->load("myproductsmodule@myproductsmodule");

// Access control
if (!$user->rights->produit->lire) accessforbidden();

$title = $langs->trans("LowStockProducts");

// Header
llxHeader('', $title);
print_fiche_titre($title);

// SQL Query
$sql = "SELECT p.ref, p.label, p.description, p.stock, p.seuil_stock_alerte ";
$sql.= "FROM ".MAIN_DB_PREFIX."product as p ";
$sql.= "WHERE p.stock <= p.seuil_stock_alerte ";
$sql.= "AND p.tosell = 1 ";
$sql.= "ORDER BY p.stock ASC";

$resql = $db->query($sql);
if ($resql) {
    print '<table class="noborder centpercent">';
    print '<tr class="liste_titre">';
    print '<td>'.$langs->trans("Ref").'</td>';
    print '<td>'.$langs->trans("Label").'</td>';
    print '<td class="right">'.$langs->trans("CurrentStock").'</td>';
    print '<td class="right">'.$langs->trans("AlertThreshold").'</td>';
    print '</tr>';

    while ($obj = $db->fetch_object($resql)) {
        print '<tr class="oddeven">';
        print '<td>'.$obj->ref.'</td>';
        print '<td>'.$obj->label.'</td>';
        print '<td class="right">'.price($obj->stock).'</td>';
        print '<td class="right">'.price($obj->seuil_stock_alerte).'</td>';
        print '</tr>';
    }
    print '</table>';
    
    $db->free($resql);
} else {
    dol_print_error($db);
}

llxFooter();
$db->close();