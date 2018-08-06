<?php
include('connect.php');
if($_POST['vote']=='dontknow'){
        $strSQL = mysqli_query($connection,"update crimelog set dontknow=dontknow+1 WHERE crime_id=".$_POST['id']." ; ");
}
if($_POST['vote']=='yes'){
        $strSQL = mysqli_query($connection,"update crimelog set yes=yes+1 WHERE crime_id=".$_POST['id']." ; ");
}
if($_POST['vote']=='no'){
        $strSQL = mysqli_query($connection,"update crimelog set no=no+1 WHERE crime_id=".$_POST['id']." ; ");
}
?>