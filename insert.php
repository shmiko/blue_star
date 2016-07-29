<?php

// Create connection to Oracle
//$conn = oci_connect("PWIN175", "PWIN175", "//sdbhom02.bspga.com.au/orahom02.bspga.com.au");
$conn = oci_connect("PWIN175", "PWIN175", "orahom02");
//$query = 'SELECT * FROM RY';
$query = 'INSERT INTO Tmp_Admin_Data_BreakPrices
	SELECT II_STOCK,II_CUST,II_BREAK_LCL
	FROM II INNER JOIN IM ON IM_STOCK = II_STOCK
	AND II_BREAK_LCL > 0.000001
	WHERE IM_CUST= :cust;';
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
