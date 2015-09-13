<?php
//This option should be provided based on the devices present in the current environment only
    require_once ("Includes/header.php");

    if(!logged_on())
    {
        header ("Location: /index.php");
    }

    $Uid = $_SESSION['userid'];

    $query = "SELECT * FROM riot4.settings WHERE User_ID=?";
    $params = array($Uid);
    $statement_count = sqlsrv_query($conn,$query,$params);

    $No = 0;
    while($row = sqlsrv_fetch_array($statement_count))
    {
        $No = $No+1;
    }

    if($No<5)
    {
        /* Provide drop down menu for user to addd device 
            Also check if the device does not already exist
        ----------------------------------------------------*/
        $val = 0;

        $query = "INSERT INTO riot4.settings VALUES (?,?,?)";
        $params = array($Uid,$dev,$val);
        $statement_add = sqlsrv_query($conn,$query,$params);
    }
    else
    {
        echo "<script>popup('Max Devices reached')</script>";
    }
?>