<?

//*** Connection to Oracle ***//

$conn = oci_connect("PWIN175", "PWIN175", "ORABLUESTAR");

 

//*** Insert Data Command ***//
$query = 'SELECT * FROM Tmp_Admin_Data_Pickslips';

 

//*** Define Variable $objParse and $objExecute ***//

$objParse = oci_parse($conn, $query);

$objExecute = oci_execute($objParse, OCI_DEFAULT);

if($objExecute)

{

   //***  oci_commit($conn); Commit Transaction ***//

    echo "Save completed.";

}

else

{

   //*** oci_rollback($conn);  RollBack Transaction ***//

    $m = oci_error($objParse);

    echo "Error Save [".$m['message']."]";

}

 

//*** Close Connection to Oracle ***//

oci_close($conn);

?>
