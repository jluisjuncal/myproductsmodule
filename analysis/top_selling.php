<?php
require '../../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/product.lib.php';

$langs->load("products");
$langs->load("myproductsmodule@myproductsmodule");

// Access control
if (!$user->rights->produit->lire) accessforbidden();

// Parameters
$limit = GETPOST('limit') ? GETPOST('limit', 'int') : 50;

$title = $langs->trans("TopSellingProducts");

// Header
llxHeader('', $title);
print_fiche_titre($title);

// SQL Query
$sql = "SELECT p.ref, p.label, p.description, ";
$sql.= "SUM(fd.qty) as total_qty, ";
$sql.= "SUM(fd.total_ht) as total_amount ";
$sql.= "FROM ".MAIN_DB_PREFIX."product as p ";
$sql.= "LEFT JOIN ".MAIN_DB_PREFIX."facturedet as fd ON fd.fk_product = p.rowid ";
$sql.= "LEFT JOIN ".MAIN_DB_PREFIX."facture as f ON f.rowid = fd.fk_facture ";
$sql.= "WHERE f.datef >= DATE_SUB(NOW(), INTERVAL 12 MONTH) ";
$sql.= "GROUP BY p.rowid ";
$sql.= "ORDER BY total_qty DESC ";
$sql.= "LIMIT " . $limit;

$resql = $db->query($sql);
if ($resql) {
    print '<table class="noborder centpercent">';
    print '<tr class="liste_titre">';
    print '<td>'.$langs->trans("Ref").'</td>';
    print '<td>'.$langs->trans("Label").'</td>';
    print '<td class="right">'.$langs->trans("QuantitySold").'</td>';
    print '<td class="right">'.$langs->trans("TotalHT").'</td>';
    print '</tr>';

    while ($obj = $db->fetch_object($resql)) {
        print '<tr class="oddeven">';
        print '<td>'.$obj->ref.'</td>';
        print '<td>'.$obj->label.'</td>';
        print '<td class="right">'.price($obj->total_qty).'</td>';
        print '<td class="right">'.price($obj->total_amount).'</td>';
        print '</tr>';
    }
    print '</table>';
    
    $db->free($resql);
} else {
    dol_print_error($db);
}

llxFooter();
$db->close();