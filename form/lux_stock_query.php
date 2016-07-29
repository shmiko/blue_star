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

  echo "<table border='1'>".PHP_EOL;
  while ($row = oci_fetch_array($s, OCI_ASSOC+OCI_RETURN_NULLS)) {
	echo "<tr>".PHP_EOL;
	foreach ($row as $item) {
	  echo "  <td>".($item?htmlentities($item):"&nbsp;")."</td>".PHP_EOL;
	}
	echo "</tr>".PHP_EOL;
  }
  echo "</table>".PHP_EOL;
}

?>