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

<?php


	
	
	
	
	
	// Create connection to Oracle
	$c = oci_connect("PWIN175", "PWIN175", "ORABLUESTAR");

	
	$query = 'SELECT * FROM tbl_AdminData ORDER BY OrderNum,Pickslip Asc';

	 $s = oci_parse($c, $query);
	oci_execute($s);
	 print '<table border="1">';
		 print '<tr>';
		   print '<th>Cust</th>';
		   print '<th>Parent</th>';
		   print '<th>Cost Center</th>';
		   print '<th>Order</th>';
		   print '<th>OW Num</th>';
		   print '<th>Cust Ref</th>';
		   print '<th>Pickslip</th>';
		   print '<th>Desp Date</th>';
		   print '<th>Stock</th>';
		   print '<th>Fee Type</th>';
		   print '<th>Item</th>';
		   print '<th>Description</th>';
		   print '<th>Qty</th>';
		   print '<th>UOI</th>';
		   
		   print '<th>UnitPrice</th>';
		   print '<th>OWUnitPrice</th>';
		   print '<th>DExcl</th>';
		   print '<th>UOI</th>';
		   print '<th>Excl_Total</th>';
		   print '<th>DIncl</th>';
		   print '<th>Incl_Total</th>';
		   print '<th>ReportingPrice</th>';
		   
		   print '<th>Address</th>';
		   print '<th>Address2</th>';
		   print '<th>Suburb</th>';
		   print '<th>State</th>';
		   print '<th>Postcode</th>';
		   print '<th>DeliverTo</th>';
		   print '<th>AttentionTo</th>';
		   print '<th>Weight</th>';
		   
		   print '<th>Packages</th>';
		   print '<th>OrderSource</th>';
		   print '<th>Pallet/Shelf Space</th>';
		   print '<th>Locn</th>';
		   print '<th>AvailSOH</th>';
		   
		   print '<th>CountOfStocks</th>';
		   print '<th>Email</th>';
		   print '<th>Brand</th>';
		  
		   
		 print '</tr>';
  while ($row = oci_fetch_array($s, OCI_RETURN_NULLS+OCI_ASSOC)) {
   print '<tr>';
    foreach ($row as $item) {
      print '<td>'.($item?htmlentities($item):'&nbsp;').'</td>';
    }
    print '</tr>';
  }
  print '</table>';


?>