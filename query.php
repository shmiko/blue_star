<h2>Bluestar EOM SQL </h2>
<?php




// Create connection to Oracle
//$conn = oci_connect("PWIN175", "PWIN175", "//sdbudc04.bspga.com.au/sdbudc04.bspga.com.au");
$conn = oci_connect("PWIN175", "PWIN175", "ORABLUESTAR");
		
$query = 'SELECT * FROM tbl_AdminData ORDER BY OrderNum,Pickslip Asc';
//$query = 'SELECT * FROM Tmp_Admin_Data_BreakPrices';
$stid = oci_parse($conn, $query);
$r = oci_execute($stid);

// Fetch each row in an associative array
print '<table border="1">';
while ($row = oci_fetch_array($stid, OCI_RETURN_NULLS+OCI_ASSOC)) {
   print '<tr>';
   foreach ($row as $item) {
       print '<td>'.($item !== null ? htmlentities($item, ENT_QUOTES) : '&nbsp').'</td>';
   }
   print '</tr>';
}
print '</table>';

?>
