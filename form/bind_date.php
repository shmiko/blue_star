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

<h2>Lux Stock via SQL</h2>
You chose the following criteria:<p>
From Date was set to <?php echo $_POST['field_1'] ?><p>
To Date was set to <?php echo $_POST['field_2'] ?><p>

Customer was set to <?php echo $_POST['analysis'] ?><p>

<?php


If ($_POST['export'] == 'csv') { 
	echo 'run to screen';
	//echo 'run to file';
  //echo 'run to file';
  $id = $_POST['field_1'];
  // Create connection to Oracle
	//$c = oci_connect("PWIN175", "PWIN175", "ORABLUESTAR");
	$c = oci_pconnect("PWIN175", "PWIN175", "//sdbudc04.bspga.com.au/orabluestar.bspga.com.au");
	// Use bind variable to improve resuability, and to remove SQL Injection attacks.
	$query = 'SELECT * FROM Tmp_Admin_Data_Pickslips WHERE substr(VDATEDESP,0,10) >= :EIDBV AND substr(VDATEDESP,0,10) <= :EIDBV2';
																								
				  

	$s = oci_parse($c, $query);

	$myeid = $id;
	//$myeid = '21-02-2014';
	$myeid2 = $_POST['field_2'];

	OCIBindByName($s, ":EIDBV2", $myeid2);
	OCIBindByName($s, ":EIDBV", $myeid);

	oci_execute($s);

	 // Fetch the results in an associative array
	 // print '<p>From date is ' . $myeid . '</p>';
	//print '<p>To date is ' . $myeid2 . '</p>';
	print '<table>';
	print '<tr>';
	print '<th>Pick Num</th>';
	print '<th>Desp Num</th>';
	print '<th>Date</th>';
	print '<th>Num of Packs</th>';
	print '<th>Weight</th>';
	print '<th>SW Pallets</th>';
	print '<th>Pallets</th>';
	print '<th>Cartons</th>';
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
	
	$id = $_POST['field_1'];

	// Create connection to Oracle
	$c = oci_connect("PWIN175", "PWIN175", "ORABLUESTAR");

	// Use bind variable to improve resuability, and to remove SQL Injection attacks.
	$query = 'SELECT * FROM Tmp_Admin_Data_Pickslips WHERE substr(VDATEDESP,0,10) >= :EIDBV AND substr(VDATEDESP,0,10) <= :EIDBV2';
																								
				  

	$s = oci_parse($c, $query);

	$myeid = $id;
	//$myeid = '21-02-2014';
	$myeid2 = $_POST['field_2'];

	OCIBindByName($s, ":EIDBV2", $myeid2);
	OCIBindByName($s, ":EIDBV", $myeid);

	oci_execute($s);

	// Fetch the results in an associative array
	// print '<p>From date is ' . $myeid . '</p>';
	//print '<p>To date is ' . $myeid2 . '</p>';
	print '<table>';
	print '<tr>';
	print '<th>Pick Num</th>';
	print '<th>Desp Num</th>';
	print '<th>Date</th>';
	print '<th>Num of Packs</th>';
	print '<th>Weight</th>';
	print '<th>SW Pallets</th>';
	print '<th>Pallets</th>';
	print '<th>Cartons</th>';
	print '</tr>';
	while ($row = oci_fetch_array($s, OCI_RETURN_NULLS+OCI_ASSOC)) {
	print '<tr>';
	foreach ($row as $item) {
		
	  print '<td>'.($item?htmlentities($item):'&nbsp;').'</td>';
	  
	}
	print '</tr>';
	}
	print '</table>';
	$file = $_POST['analysis'];

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

