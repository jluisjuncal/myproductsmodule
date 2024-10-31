<?php
require '../../../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/product.lib.php';
require_once DOL_DOCUMENT_ROOT.'/categories/class/categorie.class.php';

$langs->load("products");
$langs->load("myproductsmodule@myproductsmodule");

// Access control
if (!$user->rights->produit->lire) accessforbidden();

// Parameters
$limit = GETPOST('limit') ? GETPOST('limit', 'int') : 50;
$sortfield = GETPOST('sortfield', 'aZ09comma');
$sortorder = GETPOST('sortorder', 'aZ09comma');
$category = GETPOST('category', 'int');
$status = GETPOST('status', 'int');

if (!$sortfield) $sortfield = 'total_qty';
if (!$sortorder) $sortorder = 'DESC';

$title = $langs->trans("TopSellingProducts");

// Header
llxHeader('', $title);
print_fiche_titre($title);

// Filter form
print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
print '<input type="hidden" name="token" value="'.newToken().'">';
print '<input type="hidden" name="sortfield" value="'.$sortfield.'">';
print '<input type="hidden" name="sortorder" value="'.$sortorder.'">';

print '<div class="inline-block marginbottomonly">';

// Status filter
print $langs->trans("Status"). ': ';
print '<select class="flat" name="status">';
print '<option value="-1"'.($status == -1 ? ' selected' : '').'>'.$langs->trans("All").'</option>';
print '<option value="1"'.($status == 1 ? ' selected' : '').'>'.$langs->trans("OnSell").'</option>';
print '<option value="0"'.($status == 0 ? ' selected' : '').'>'.$langs->trans("NotOnSell").'</option>';
print '</select>';

// Category filter
print ' '.$langs->trans("Category"). ': ';
print '<select class="flat" name="category">';
print '<option value="0">'.$langs->trans("All").'</option>';
$sql = "SELECT rowid, label FROM ".MAIN_DB_PREFIX."categorie WHERE type = 0";
$resql = $db->query($sql);
if ($resql) {
    while ($obj = $db->fetch_object($resql)) {
        print '<option value="'.$obj->rowid.'"'.($category == $obj->rowid ? ' selected' : '').'>'.$obj->label.'</option>';
    }
}
print '</select>';

print ' <input type="submit" class="button" value="'.$langs->trans("Filter").'">';
print '</div>';
print '</form>';

// Build SQL Query
$sql = "SELECT p.ref, p.label, p.description, p.tosell, ";
$sql.= "COALESCE(SUM(fd.qty), 0) as total_qty, ";
$sql.= "COALESCE(SUM(fd.total_ht), 0) as total_amount ";
$sql.= "FROM ".MAIN_DB_PREFIX."product as p ";
$sql.= "LEFT JOIN ".MAIN_DB_PREFIX."facturedet as fd ON fd.fk_product = p.rowid ";
$sql.= "LEFT JOIN ".MAIN_DB_PREFIX."facture as f ON f.rowid = fd.fk_facture ";

// Category filter
if ($category > 0) {
    $sql.= "LEFT JOIN ".MAIN_DB_PREFIX."categorie_product as cp ON cp.fk_product = p.rowid ";
}

$sql.= "WHERE 1=1 ";

if ($status >= 0) {
    $sql.= "AND p.tosell = " . $status . " ";
}
if ($category > 0) {
    $sql.= "AND cp.fk_categorie = " . $category . " ";
}

$sql.= "GROUP BY p.rowid, p.ref, p.label, p.description, p.tosell ";
$sql.= "ORDER BY " . $sortfield . " " . $sortorder;
$sql.= " LIMIT " . $limit;

$resql = $db->query($sql);
if ($resql) {
    print '<table class="noborder centpercent">';
    print '<tr class="liste_titre">';
    print_liste_field_titre("Ref", $_SERVER["PHP_SELF"], "p.ref", "", "", "", $sortfield, $sortorder);
    print_liste_field_titre("Label", $_SERVER["PHP_SELF"], "p.label", "", "", "", $sortfield, $sortorder);
    print_liste_field_titre("QuantitySold", $_SERVER["PHP_SELF"], "total_qty", "", "", 'class="right"', $sortfield, $sortorder);
    print_liste_field_titre("TotalHT", $_SERVER["PHP_SELF"], "total_amount", "", "", 'class="right"', $sortfield, $sortorder);
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