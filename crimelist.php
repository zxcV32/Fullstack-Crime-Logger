<?php


include('connect.php');

        $strSQL = mysqli_query($connection,"select title,latitude,longitude from crimelog");
    $data = array ();

while ($row=mysqli_fetch_row($strSQL)){   
           $rowdata=array(
            "label" => $row[0],
            "lat" => $row[1],
            "long" => $row[2],
           );
    array_push($data,$rowdata);
      }


$myJSON = json_encode($data);
echo $myJSON;

?>
