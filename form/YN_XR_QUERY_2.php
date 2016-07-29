<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
	<html>
		
		<div id="top">
			<img src="BSPG252x61.gif" alt="Blue Star Group - Australia Intranet Site" align="middle"/>
		</div>
		
	<head>
		
		<title>IQ Notification Control</title>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8"><link href="style_notify.css" rel="stylesheet" type="text/css">
	</head>
	
	<h2>IQ Notification Control</h2>
	You chose the following criteria:<p>
	Customer was set to <?php echo $_POST['customer'] ?><p>
	Report was set to <?php echo $_POST['report'] ?><p>
<?php


	
	
	
	$cust_id = $_POST['customer'];
	$report_code = $_POST['report'];
	
	// Create connection to Oracle
	$c = oci_connect("PWIN175", "PWIN175", "ORABLUESTAR");
	
	$query = 'SELECT * FROM Tmp_Admin_Data_Pickslips WHERE substr(VDATEDESP,0,10) >= :EIDBV AND substr(VDATEDESP,0,10) <= :EIDBV2';
																								

	$sql = "SELECT  XR_USER_DESC, ".
	 " substr(y.YN_DESC, INSTR(y.YN_DESC,':') + 1, (INSTR(y.YN_DESC, '#') -1) - (INSTR(y.YN_DESC,':') + 1) + 1 ) ".
	 " FROM  PWIN175.YN y INNER JOIN PWIN175.XR x  ON substr(y.YN_DESC,0, INSTR(y.YN_DESC,':') - 1) = x.XR_CODE WHERE   y.YN_TYPE = 'EMAILLIST' AND (XR_CODE = :EIDBV OR substr(y.YN_DESC, INSTR(y.YN_DESC,':') + 1, (INSTR(y.YN_DESC, '#') -1) - (INSTR(y.YN_DESC,':') + 1) + 1 ) = :EIDBV2)";
	 
	$query = "SELECT QM_NUMBER, QM_REP FROM QM WHERE QM_NUMBER = '    336392'";
	//$query = strip_special_characters($query);
	$s = oci_parse($c, $sql );
	 
	
	OCIBindByName($s, ":EIDBV2", $cust_id);
	OCIBindByName($s, ":EIDBV", $report_code);
	 
	oci_execute($s);
	 print '<table border="1">';
		 print '<tr>';
		   print '<th>Report</th>';
		   print '<th>Customer</th>';  
		 print '</tr>';
  while ($row = oci_fetch_array($s, OCI_RETURN_NULLS+OCI_ASSOC)) {
   print '<tr>';
    foreach ($row as $item) {
      print '<td><a href="style.css">'.($item?htmlentities($item):'&nbsp;').'</td>';
    }
    print '</tr>';
  }
  print '</table>';


?>


</body>
</html>