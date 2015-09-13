<?php
    require_once ("Includes/header.php");
    
    if(!logged_on()||ENV_logged_on())
    {
        header ("Location: /index.php");
    }

    if (isset($_POST['Dev_Login']))
    {
        $Envir_ID = $_POST['Chip_ID'];
        $password = $_POST['OTP'];
        $valid = FALSE;

        $query = "SELECT * FROM riot4.ENV WHERE id = ?";
            
        $params = array($Envir_ID);
        $statement = sqlsrv_query($conn,$query,$params);

        if(sqlsrv_has_rows($statement))
        {
            $row = sqlsrv_fetch_array($statement);

            if($row['OTP']==$password)
            {
                $_SESSION['ENV_OTP'] = $row['OTP'];
                $_SESSION['MID'] = $row['id'];
                $_SESSION['ROOT'] = $row['root'];

                if($row['root']==NULL)
                {
                    $query_update = "UPDATE riot4.ENV SET root = ? WHERE id = ?";

                    $params = array($_SESSION['userid'],$Envir_ID);
                    $statement_update = sqlsrv_query($conn,$query_update,$params);

                    $_SESSION['ROOT'] = $_SESSION['userid'];

				    echo "<script>popup('You have been set as root for the environment')</script>";
                    header ("Location: /env_settings.php");
                }

                if($_SESSION['ROOT']==$_SESSION['userid'])
                {
                    /* Change ENV values to that of root
                    -------------------------------------------*/
                    $query = "SELECT * from riot4.settings WHERE User_ID = ?";
                    $params = array($_SESSION['userid']);
                    $statement = sqlsrv_query($conn, $query, $params);

				    if(sqlsrv_has_rows($statement))
				    {
					    while($row = sqlsrv_fetch_array($statement))
					    {
						    $query_env_update = "UPDATE riot4.ENV_settings SET Value  = ? WHERE id = ? AND Device = ?";
						    $params = array($row['Value'],$Envir_ID,$row['Device']);
						    $statement_env_update = sqlsrv_query($conn, $query_env_update,$params);
					    }
				    }

				    echo "<script>popup('You are admin for the environment')</script>";
                    header ("Location: /env_settings.php");
                }
                else if(get_count($_SESSION['MID'])==0)
                {
                    /* Change ENV values to that of first user
                    -------------------------------------------*/
				    $query = "SELECT * from riot4.settings WHERE User_ID = ?";
                    $params = array($_SESSION['userid']);
                    $statement = sqlsrv_query($conn, $query, $params);

				    if(sqlsrv_has_rows($statement))
				    {
					    while($row = sqlsrv_fetch_array($statement))
					    {
						    $query_env_update = "UPDATE riot4.ENV_settings SET Value  = ? WHERE id = ? AND Device = ? AND LOCK=0";
						    $params = array($row['Value'],$Envir_ID,$row['Device']);
						    $statement_env_update = sqlsrv_query($conn, $query_env_update,$params);
					    }
				    }

				    echo "<script>popup('You are 1st user in the environment')</script>";
                    header ("Location: /env_settings.php");
                }
            }
            else
            {
                $msg = "Environment_ID/OTP combination is incorrect.<br><br>";
                $valid = TRUE;
            }
        }
        else
        {
            $msg = "Environment does not exist.<br><br>";
            $valid = TRUE;
        }
    }
?>

<link rel="stylesheet" type="text/css" href="/Styles/Logon.css">
	<div id="main">
		<h2>Log on</h2>
			<form action="dev_logon.php" method="post">
				<fieldset>
				<legend>Environment Login</legend>
				<ol>
					<li>
						<label for="username">Chip ID:</label> 
						<input type="text" name="Chip_ID" value="" id="Chip_ID" />
					</li>
					<li>
						<label for="password">OTP:</label>
						<input type="password" name="OTP" value="" id="OTP" />
					</li>
				</ol>

<?php
    if (isset($_POST['Dev_Login']) && $valid)
    {
        echo $msg;
    }
?>

				<input type="submit" id="Dev_Login" name="Dev_Login" value="Dev_Login" />
			</fieldset>
		</form>
	</div>

</center>
</div> <!-- End of outer-wrapper which opens in header.php -->

<?php 
    require_once ("Includes/footer.php");
 ?>