<?php

$id = $_GET['id'];



function do_fetch($myeid, $s)
{
  // Fetch the results in an associative array
  print '<p>Date is ' . $myeid . '</p>';
  print '<table border="1">';
  while ($row = oci_fetch_array($s, OCI_RETURN_NULLS+OCI_ASSOC)) {
    print '<tr>';
    foreach ($row as $item) {
      print '<td>'.($item?htmlentities($item):'&nbsp;').'</td>';
    }
    print '</tr>';
  }
  print '</table>';
}

// Create connection to Oracle
$c = oci_connect("PWIN175", "PWIN175", "ORABLUESTAR");

// Use bind variable to improve resuability, and to remove SQL Injection attacks.
			  
$query = 'SELECT * FROM Tmp_Admin_Data_Pickslips WHERE substr(VDATEDESP,0,10) = :EIDBV';
			  

$s = oci_parse($c, $query);

//'$myeid = '21-FEB-14';
$myeid = $id;
oci_bind_by_name($s, ":EIDBV", $myeid);
oci_execute($s);
do_fetch($myeid, $s);

// Close the Oracle connection
oci_close($c);

?>