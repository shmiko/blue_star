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
  $cust = $_POST['analysis'];
  // Create connection to Oracle
	$c = oci_connect("PWIN175", "PWIN175", "ORABLUESTAR");

	// Use bind variable to improve resuability, and to remove SQL Injection attacks.
	$query = "SELECT    s.SH_CUST                AS "Customer",
			  r.RM_PARENT              AS "Parent",
	      i.IM_XX_COST_CENTRE01    AS "CostCentre",
			  s.SH_ORDER               AS "Order",
			  s.SH_SPARE_STR_5         AS "OrderwareNum",
			  s.SH_CUST_REF            AS "CustomerRef",
			  t.ST_PICK                AS "Pickslip",
			  d.SD_XX_PICKLIST_NUM     AS "PickNum",
			  t.ST_PSLIP               AS "DespatchNote",
			  --NULL AS "DespatchDate",
			  substr(To_Char(t.ST_DESP_DATE),0,10)            AS "DespatchDate",
	  CASE    WHEN d.SD_STOCK IS NOT NULL THEN "Stock"
			  ELSE NULL
			  END                      AS "FeeType",
			  d.SD_STOCK               AS "Item",
			  d.SD_DESC                AS "Description",
			  d.SD_QTY_DESP           AS "Qty",
			  d.SD_QTY_UNIT            AS "UOI",
			  /* We need to get a 3 tiered looup for the stockunit prices, fist get th eprice from thE BATCH if company owned otherwise get the unit price from the sd sell price otherwise get it from the ow xx */
	   CASE   WHEN d.SD_STOCK IS NOT NULL AND i.IM_CUST <> "TABCORP" AND i.IM_OWNED_BY = 0 THEN d.SD_SELL_PRICE --company owned
			      WHEN d.SD_STOCK IS NOT NULL AND i.IM_CUST <> "TABCORP" AND i.IM_OWNED_BY = 1 THEN n.NI_SELL_VALUE/n.NI_NX_QUANTITY
            ELSE NULL
			      END                        AS "UnitPrice",

		 CASE   WHEN d.SD_STOCK IS NOT NULL AND i.IM_CUST <> "TABCORP" THEN To_Number(i.IM_REPORTING_PRICE)
			 ELSE NULL
			  END                        AS "OWUnitPrice",
      CASE   WHEN d.SD_STOCK IS NOT NULL AND i.IM_CUST <> "TABCORP" AND i.IM_OWNED_BY = 0 THEN d.SD_SELL_PRICE * d.SD_QTY_DESP--customer owned
			      WHEN d.SD_STOCK IS NOT NULL AND i.IM_CUST <> "TABCORP" AND i.IM_OWNED_BY = 1 THEN (n.NI_SELL_VALUE/n.NI_NX_QUANTITY) * d.SD_QTY_DESP
           ELSE NULL
			      END          AS "DExcl",

	   CASE   WHEN d.SD_STOCK IS NOT NULL AND i.IM_CUST <> "TABCORP" THEN To_Number(i.IM_REPORTING_PRICE)
			  ELSE NULL
			  END                       AS "Excl_Total",
	  CASE     WHEN d.SD_STOCK IS NOT NULL AND i.IM_CUST <> "TABCORP" AND i.IM_OWNED_BY = 0 THEN (d.SD_SELL_PRICE * d.SD_QTY_DESP) * 1.1--customer owned
			      WHEN d.SD_STOCK IS NOT NULL AND i.IM_CUST <> "TABCORP" AND i.IM_OWNED_BY = 1  THEN  ((n.NI_SELL_VALUE/n.NI_NX_QUANTITY) * d.SD_QTY_DESP) * 1.1
            ELSE NULL
			      END          AS "DIncl",
	   CASE   WHEN d.SD_STOCK IS NOT NULL AND i.IM_CUST <> "TABCORP" AND i.IM_OWNED_BY = 0 THEN (d.SD_SELL_PRICE * d.SD_QTY_DESP) * 1.1--customer owned
			      WHEN d.SD_STOCK IS NOT NULL AND i.IM_CUST <> "TABCORP" AND i.IM_OWNED_BY = 1 THEN  ((n.NI_SELL_VALUE/n.NI_NX_QUANTITY) * d.SD_QTY_DESP) * 1.1
            ELSE NULL
			      END          AS "Incl_Total",
	  CASE    WHEN d.SD_STOCK IS NOT NULL AND i.IM_CUST <> "TABCORP" THEN To_Number(i.IM_REPORTING_PRICE)
			  ELSE NULL
			  END                    AS "ReportingPrice",
			  s.SH_ADDRESS             AS "Address",
			  s.SH_SUBURB              AS "Address2",
			  s.SH_CITY                AS "Suburb",
			  s.SH_STATE               AS "State",
			  s.SH_POST_CODE           AS "Postcode",
			  s.SH_NOTE_1              AS "DeliverTo",
			  s.SH_NOTE_2              AS "AttentionTo" ,
			  t.ST_WEIGHT              AS "Weight",
			  t.ST_PACKAGES            AS "Packages",
			  s.SH_SPARE_DBL_9         AS "OrderSource",
			  NULL AS "Pallet/Shelf Space", /*Pallet/Space*/
				NULL AS "Locn", /*Locn*/
				0 AS "AvailSOH",/*Avail SOH*/
				0 AS "CountOfStocks",
        CASE  WHEN regexp_substr(s.SH_SPARE_STR_3,"[a-z]+", 1, 2) IS NOT NULL THEN  s.SH_SPARE_STR_3 || "@" || s.SH_SPARE_STR_1
			        ELSE ''
			        END AS Email,
              i.IM_BRAND AS Brand

	FROM      PWIN175.SD d
			  INNER JOIN PWIN175.SH s  ON s.SH_ORDER  = d.SD_ORDER
			  INNER JOIN PWIN175.ST t  ON t.ST_ORDER  = s.SH_ORDER
        INNER JOIN PWIN175.SL l  ON l.SL_PICK   = t.ST_PICK
			  INNER JOIN PWIN175.RM r  ON r.RM_CUST  = s.SH_CUST
			  INNER JOIN PWIN175.IM i  ON i.IM_STOCK = d.SD_STOCK
        INNER JOIN PWIN175.NI n  ON n.NI_NV_EXT_KEY = l.SL_UID
  WHERE NI_NV_EXT_TYPE = 1810105 AND NI_STRENGTH = 3 AND NI_DATE = t.ST_DESP_DATE AND NI_STOCK = d.SD_STOCK AND NI_STATUS <> 0
	AND     s.SH_STATUS <> 3
  AND (r.RM_PARENT = :EIDBV3 OR r.RM_CUST = :EIDBV3)
	AND       s.SH_ORDER = t.ST_ORDER
	AND       d.SD_STOCK NOT LIKE "COURIER"
	AND       d.SD_STOCK NOT LIKE "FEE*"
	AND       t.ST_DESP_DATE >= :EIDBV AND t.ST_DESP_DATE <= :EIDBV2
	AND       d.SD_LAST_PICK_NUM = t.ST_PICK
	GROUP BY  s.SH_CUST,
			  s.SH_NOTE_1,
			  s.SH_CAMPAIGN,
			  s.SH_SPARE_STR_4,
			  i.IM_XX_COST_CENTRE01,
			  i.IM_CUST,
			  r.RM_PARENT,
			  s.SH_ORDER,
			  t.ST_PICK,
			  d.SD_XX_PICKLIST_NUM,
			  i.IM_REPORTING_PRICE,
			  i.IM_NOMINAL_VALUE,
			  t.ST_PSLIP,
			  t.ST_DESP_DATE,
			  d.SD_QTY_ORDER,
			  d.SD_QTY_UNIT,
			  d.SD_STOCK,
			  d.SD_DESC,
			  d.SD_LINE,
			  d.SD_EXCL,
			  d.SD_INCL,
			  d.SD_SELL_PRICE,
			  d.SD_XX_OW_UNIT_PRICE,
			  d.SD_QTY_ORDER,
			  d.SD_QTY_ORDER,
			  s.SH_ADDRESS,
			  s.SH_SUBURB,
			  s.SH_CITY,
			  s.SH_STATE,
			  s.SH_POST_CODE,
			  s.SH_NOTE_1,
			  s.SH_NOTE_2,
			  t.ST_WEIGHT,
			  t.ST_PACKAGES,
			  s.SH_SPARE_DBL_9,
			  r.RM_GROUP_CUST,
			  r.RM_PARENT,
			  s.SH_SPARE_STR_5,
			  s.SH_CUST_REF,s.SH_SPARE_STR_3,s.SH_SPARE_STR_1,
			  d.SD_SELL_PRICE,
			  i.IM_OWNED_BY,
			  d.SD_QTY_DESP,
        n.NI_SELL_VALUE,
        n.NI_NX_QUANTITY,
              i.IM_BRAND";
	//$query = 'SELECT * FROM Tmp_Admin_Data_Pickslips WHERE substr(VDATEDESP,0,10) >= :EIDBV AND substr(VDATEDESP,0,10) <= :EIDBV2';
																								
				  

	$s = oci_parse($c, $query);

	$myeid = $id;
	//$myeid = '21-02-2014';
	$myeid2 = $_POST['field_2'];

	OCIBindByName($s, ":EIDBV2", $myeid2);
	OCIBindByName($s, ":EIDBV", $myeid);
	OCIBindByName($s, ":EIDBV3", $cust);
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

