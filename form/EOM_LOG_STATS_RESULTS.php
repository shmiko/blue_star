<style>
table,th,td,tr
{
width:400px;
border:1px solid black;
background-color: #ffffff;
border-spacing:0px;
border-collapse:collapse;
}
</style>

<h2>EOM Logistics Statistics</h2>
You chose the following criteria:<p>
From Date was set to <?php echo $_POST['field_1'] ?><p>
To Date was set to <?php echo $_POST['field_2'] ?><p>

Customer was set to <?php echo $_POST['analysis'] ?><p>
Warehouse was set to <?php echo $_POST['warehouse'] ?><p>
<?php


If ($_POST['export'] == 'csv') { 
	echo 'run to screen';
	//echo 'run to file';
  //echo 'run to file';
  // Create connection to Oracle
	//$c = oci_connect("PWIN175", "PWIN175", "ORABLUESTAR");
	$c = oci_pconnect("PWIN175", "PWIN175", "//sdbudc04.bspga.com.au/orabluestar.bspga.com.au");
	// Use bind variable to improve resuability, and to remove SQL Injection attacks.
	$query = 'SELECT * FROM Tmp_Admin_Data_Pickslips WHERE substr(VDATEDESP,0,10) >= :EIDBV AND substr(VDATEDESP,0,10) <= :EIDBV2';
																								
			//$sql = file_get_contents('EOM_STATS_ROLLUP.sql');
			//$sql = file_get_contents('EOM_STATS_ROLLUP.sql');
	$sql = file_get_contents('test_run_sql.sql');
$block= <<<_SQL
BEGIN
$sql
END;
_SQL;
	 $s = oci_parse($c, $block);
			  

	$id = $_POST['customer'];
	$myeid3 = $id;
	//$myeid = '21-02-2014';
	$myeid4 = $_POST['warehouse'];
	$myeid = $_POST['field_1'];
	$myeid2 = $_POST['field_2'];

	OCIBindByName($s, ":EIDBV", $myeid);
	OCIBindByName($s, ":EIDBV2", $myeid2);

	OCIBindByName($s, ":EIDBV3", $myeid3);
	OCIBindByName($s, ":EIDBV4", $myeid4);

	oci_execute($s);

	 // Fetch the results in an associative array
	 // print '<p>From date is ' . $myeid . '</p>';
	//print '<p>To date is ' . $myeid2 . '</p>';
	print '<table>';
	print '<tr>';
	print '<th>Customer</th>';
    print '<th>Warehouse</th>';  
    print '<th>Total</th>';
    print '<th>Type</th>';
	print '</tr>';
	while ($row = oci_fetch_array($s, OCI_RETURN_NULLS+OCI_ASSOC)) {
	print '<tr>';
	foreach ($row as $item) {
		
	  print '<td>'.($item?htmlentities($item):'&nbsp;').'</td>';
	  
	}
	print '</tr>';
	}
	print '</table>';
	

       

// Close the Oracle connection
oci_close($c);


}
else 
{
	
	//$id = $_POST['field_1'];

	// Create connection to Oracle
	$c = oci_connect("PWIN175", "PWIN175", "ORABLUESTAR");

	// Use bind variable to improve resuability, and to remove SQL Injection attacks.
	$query = 'SELECT * FROM Tmp_Admin_Data_Pickslips WHERE substr(VDATEDESP,0,10) >= :EIDBV AND substr(VDATEDESP,0,10) <= :EIDBV2';
	//$sql = file_get_contents('EOM_STATS_ROLLUP.sql');
	$sql = file_get_contents('test_run_sql.sql');
$block= <<<_SQL
BEGIN
$sql
END;
_SQL;
	 $s = oci_parse($c, $block);																							
				  

	//$s = oci_parse($c, $query);

	//$s = oci_parse($c, $query);
	$id = $_POST['customer'];
	$myeid3 = $id;
	//$myeid = '21-02-2014';
	$myeid4 = $_POST['warehouse'];
	$myeid = $_POST['field_1'];
	$myeid2 = $_POST['field_2'];

	OCIBindByName($s, ":EIDBV", $myeid);
	OCIBindByName($s, ":EIDBV2", $myeid2);

	OCIBindByName($s, ":EIDBV3", $myeid3);
	OCIBindByName($s, ":EIDBV4", $myeid4);

	oci_execute($s);

	// Fetch the results in an associative array
	// print '<p>From date is ' . $myeid . '</p>';
	//print '<p>To date is ' . $myeid2 . '</p>';
	print '<table>';
	print '<tr>';
	print '<th>Customer</th>';
	print '<th>Warehouse</th>';  
	print '<th>Total</th>';
	print '<th>Type</th>';
	print '</tr>';
	while ($row = oci_fetch_array($s, OCI_RETURN_NULLS+OCI_ASSOC)) {
	print '<tr>';
	foreach ($row as $item) {
		
	  print '<td>'.($item?htmlentities($item):'&nbsp;').'</td>';
	  
	}
	print '</tr>';
	}
	print '</table>';
	$file = $_POST['customer'];

	$filename = $file."_".date("Y-m-d_H-i",time());

	header("Content-type: vnd.ms-excel");
	header("Content-disposition: attachment; filename=".$filename.".html");
	//header("Content-disposition: attachment; filename=".$_POST['Analysis'].".csv");
	header("Pragma: no-cache");
	header("Expires: 0");
	// Close the Oracle connection
oci_close($c);
}

?>
	





<?php

?>

