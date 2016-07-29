<?php

// Create connection to Oracle
//$conn = oci_pconnect("PWIN175", "PWIN175", "//sdbhom02.bspga.com.au/orahom02.bspga.com.au");
$conn = oci_connect("PWIN175", "PWIN175", "orahom02");
if (!$conn) {
   $m = oci_error();
   echo $m['message'], "\n";
   exit;
}
else {
   print "Connected to Oracle!";
}

// Close the Oracle connection
oci_close($conn);

?>
