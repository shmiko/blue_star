<?php

if (!isset($_GET['id'])) {
  echo 'No id passed';
}
else {
	$id = $_GET['id'];

	// Create connection to Oracle
	$c = oci_connect("PWIN175", "PWIN175", "ORABLUESTAR");

	
	$query = 'SELECT * FROM Tmp_Admin_Data_Pickslips WHERE substr(VDATEDESP,0,10) = :id';

	 $s = oci_parse($c, $query);
	oci_bind_by_name($s, ":id", $id);
	oci_execute($s);
	print '<p>$myeid is ' . $id . '</p>';
  print "<table border='1'>".PHP_EOL;
  while ($row = oci_fetch_array($s, OCI_ASSOC+OCI_RETURN_NULLS)) {
	print "<tr>".PHP_EOL;
	foreach ($row as $item) {
	  print "  <td>".($item?htmlentities($item):"&nbsp;")."</td>".PHP_EOL;
	}
	print "</tr>".PHP_EOL;
  }
  print "</table>".PHP_EOL;
}

?>