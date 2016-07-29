<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
	<div id="top">
		<img src="BSPG252x61.gif" alt="Blue Star Group - Australia Intranet Site" />
	</div>
	<head>
		
		<title>IQ Notification Control</title>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8"><link href="style.css" rel="stylesheet" type="text/css">

	</head>


	<div id="mainForm">

		<div id="formHeader">
				<h2 class="formInfo">IQ NOTIFY</h2>
				<p class="formInfo">IQ Notification Control</p>
		</div>

		<BR/>
		
			<form method=post enctype=multipart/form-data action="YN_XR_QUERY_2.php" method="POST" ><ul class=mainForm id="mainForm_1">
				
				<label class="formFieldQuestion">Customer<a class=info href=#><img src=imgs/tip_small.png border=0><span class=infobox>Enter the Customer</span></a></label><input name="customer" type="text" size="10" maxlength="10" value="LUXOTTICA"></li>
				<label class="formFieldQuestion">Report<a class=info href=#><img src=imgs/tip_small.png border=0><span class=infobox>Enter the Report</span></a></label><input name="report" type="text" size="20" maxlength="20" value="DPS_REPLEN2_PRO_DN"></li>
				
				<table>
				<tr>
					  <td><label class="formFieldQuestion">Customer List<a class=info href=#><img src=imgs/tip_small.png border=0><span class=infobox>Select the Customer Name</span></a></label></td><BR/>
				</tr>
				<tr>
						<td>
						  <select name="unit">
							  <?php 
								 
								    $c = oci_connect("PWIN175", "PWIN175", "ORABLUESTAR");
									$query = 'SELECT DISTINCT IM_CUST FROM PWIN175.IM';
									$s = oci_parse($c, $query );
									oci_execute($s);
								  while ($row = oci_fetch_array($s, OCI_RETURN_NULLS+OCI_ASSOC))
								  {
									  echo "<option value=\"unit1\">" . $row['IM_CUST'] . "</option></li>";
								  }
							  ?>
						  </select></p>
						</td></p>
					</tr>
				</table><BR/>
				<li class="mainForm">
					<input id="saveForm" class="mainForm" type="submit" value="Go" >
				</li>
				
			</form>
			
		</ul></div>

	</body>
</html>
	
	
	