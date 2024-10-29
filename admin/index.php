<?php
require '../../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/admin.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/product.lib.php';

$langs->load("products");
$langs->load("myproductsmodule@myproductsmodule");

// Access control
if (!$user->rights->produit->lire) accessforbidden();

$title = $langs->trans("ProductAnalysisDashboard");

// Header
llxHeader('', $title);
print_fiche_titre($title);

// Display summary boxes
print '<div class="fichecenter">';
print '<div class="fichethirdleft">';

// Top 5 selling products
print '<div class="div-table-responsive-no-min">';
print '<table class="noborder centpercent">';
print '<tr class="liste_titre">';
print '<td colspan="3">'.$langs->trans("Top5SellingProducts").'</td>';
print '</tr>';

$sql = "SELECT p.ref, p.label, SUM(fd.qty) as total_qty ";
$sql.= "FROM ".MAIN_DB_PREFIX."product as p ";
$sql.= "LEFT JOIN ".MAIN_DB_PREFIX."facturedet as fd ON fd.fk_product = p.rowid ";
$sql.= "LEFT JOIN ".MAIN_DB_PREFIX."facture as f ON f.rowid = fd.fk_facture ";
$sql.= "WHERE f.datef >= DATE_SUB(NOW(), INTERVAL 30 DAY) ";
$sql.= "GROUP BY p.rowid ";
$sql.= "ORDER BY total_qty DESC ";
$sql.= "LIMIT 5";

$resql = $db->query($sql);
if ($resql) {
    while ($obj = $db->fetch_object($resql)) {
        print '<tr class="oddeven">';
        print '<td>'.$obj->ref.'</td>';
        print '<td>'.$obj->label.'</td>';
        print '<td class="right">'.price($obj->total_qty).'</td>';
        print '</tr>';
    }
}
print '</table>';
print '</div>';

print '</div>';
print '<div class="fichetwothirdright">';

// Low stock alerts
print '<div class="div-table-responsive-no-min">';
print '<table class="noborder centpercent">';
print '<tr class="liste_titre">';
print '<td colspan="4">'.$langs->trans("LowStockAlerts").'</td>';
print '</tr>';

$sql = "SELECT p.ref, p.label, p.stock, p.seuil_stock_alerte ";
$sql.= "FROM ".MAIN_DB_PREFIX."product as p ";
$sql.= "WHERE p.stock <= p.seuil_stock_alerte ";
$sql.= "AND p.tosell = 1 ";
$sql.= "ORDER BY p.stock ASC ";
$sql.= "LIMIT 5";

$resql = $db->query($sql);
if ($resql) {
    while ($obj = $db->fetch_object($resql)) {
        print '<tr class="oddeven">';
        print '<td>'.$obj->ref.'</td>';
        print '<td>'.$obj->label.'</td>';
        print '<td class="right">'.price($obj->stock).'</td>';
        print '<td class="right">'.price($obj->seuil_stock_alerte).'</td>';
        print '</tr>';
    }
}
print '</table>';
print '</div>';

print '</div>';
print '</div>';

llxFooter();
$db->close();