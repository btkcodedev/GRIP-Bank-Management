<?php
$l = "localhost:3308";
$u = "root";
$p = "";
$d = "internship";
$con=mysqli_connect($l,$u,$p,$d);
if(!$con)
{
    die("Not connected: ".mysqli_connect_error());
}
session_start();
$query = "select name,balance,accno from customers";
$results=mysqli_query($con,$query);
?>
<?php
if(isset($_POST['transact']))
{
    $a=$_POST['amount']; //transaction amount
    $b=$_POST['tagvalue']; //transaction to
    $c=$_POST['rad']; // transaction from
    $transac1 = "UPDATE customers SET balance=balance-'$a' WHERE accno='$c'";
    $transac2 = "UPDATE customers SET balance=balance+'$a' WHERE accno='$b'";
    $update = "INSERT INTO transactions VALUES ('$c','$b','$a')";
    $re1=mysqli_query($con,$transac1);
    $re2=mysqli_query($con,$transac2);
    $re3=mysqli_query($con,$update);
    header('Location: index.php');
}
?>
<?php
if(isset($_POST['submit_truncate']))
{
    mysqli_query($con, 'TRUNCATE TABLE `transactions`;');
    header('Location: index.php');
}
?>
<!DOCTYPE HTML>
<html>
    <head>
        <title>
            Banking management
        </title>
        <style type="text/css">
        .test {
        display: none;
        }
        </style>
        <link rel="stylesheet" href="style.css" type="text/css">
        <link href="https://fonts.googleapis.com/css2?family=Open+Sans&display=swap" rel="stylesheet">
    </head>
    <body>
    <section class="hero">
        <div class="top">
            <span class="textcenter">Bank Admin Portal</span>

        </div>
    </section>
    <div class="split left" >
        <span class="textcenter"><u>Statistics</u></span>
        <form method="POST" action="index.php">
        <table class="lefttable">
            <tr>
                <?php
                $stat1 = "select COUNT(*) as id from customers";
                $res1=mysqli_query($con,$stat1);
                $ans1=mysqli_fetch_assoc($res1);
                echo "<td>";
                echo "Total Customers : ".$ans1['id'];
                echo "</td>";
                ?>
            </tr>
            <tr>
                <?php
                $stat2 = "select SUM(balance) as id from customers";
                $res2=mysqli_query($con,$stat2);
                $ans2=mysqli_fetch_assoc($res2);
                echo "<td>";
                echo "Amount in bank : ".$ans2['id'];
                echo "</td>";
                ?>
            </tr>
            <tr>
                <?php
                $stat3 = "select count(*) as id from transactions";
                $res3=mysqli_query($con,$stat3);
                $ans3=mysqli_fetch_assoc($res3);
                echo "<td>";
                echo "Transaction count : ".$ans3['id'];
                echo "</td>"
                ?>
            </tr>
        </table>
        <input name="submit_truncate" style="margin-left:20px;margin-top:10px;" type="submit" value="Clear Transaction Logs"></input>
        </form>
        <span class="textcenter"><u>Transaction Log</u></span>
        <div style="height:170px;overflow-y:scroll; ">
        <table class="lefttable" >
            <tr>
            <th>AccFrom ||</th>
            <th>AccTo ||</th>
            <th> Amount</th>
            </tr>
            <?php
                $trac1 = "select * from transactions";
                foreach ($con->query($trac1) as $resume1) {
                echo "<tr>";
                echo "<td> $resume1[trfrom] </td>";
                echo "<td> $resume1[trto] </td>";
                echo "<td> $resume1[amount] </td>";
                echo "</tr>";
                }
                ?>
        </table>
        </div>
    </div>
    <div class="split right" style="overflow:auto;">
        <span class="textmain"><u>Customers</u></span>
        <table class="righttable" style="margin-left:20px;">
            <tr class="content">
                <th>Acc No:</th>
                <th>Name</th>
                <th>Balance</th>
                <th>View</th>
            </tr>
            <?php
            $count = mysqli_num_rows($results);
            if($count==0)
            {
                echo "No Details in table";
            }
            else
            {
                while($row = mysqli_fetch_assoc($results))
                {
                    echo "<tr>";
                    echo "<td> ".$row['accno']."</td>";
                    echo "<td> ".$row['name']."</td>";
                    echo "<td>".$row['balance']."</td>";
                    echo "<td>
                    <input type=\"button\" onclick=display_detail($row[accno]) value=\"Open/Close\">
                    </input>
            <td>
            <tr>";
                }
            }
            ?>
        </table>
    </div>
    <div class="split right2" style="overflow:auto;" id="myDIV">
        <span class="textmain" style="margin-left:20px;"><u>Details</u></span>
        <table style="margin-left:40px;">
        <?php
        $count="SELECT * FROM customers";
        foreach ($con->query($count) as $row) {
        $p=$row['accno'];
        echo "
        <tr>
            <td>
                <form method=\"POST\" action=\"index.php\">
                <div class=\"test\" id=$row[accno] > 
                    <fieldset>
                        <input style=\"float:right;\" type=\"button\" onclick=display_detail($row[accno]) value=\"X\"></input>
                        <b>Account Number</b>: 
                        <input name=\"rad\" type=\"radio\" value=$p required>$p
                        </input>
                        <br>
                        <b>Name</b>: $row[name] <br>
                        <b>Balance</b>: $row[balance] <br>
                        <b>Email </b>: $row[email] <br>";
                        $cnt="select accno from customers where accno!=$row[accno]";
                        echo "Account for transaction : 
                        <select name=\"tagvalue\">";
                        foreach ($con->query($cnt) as $r) 
                        {
                            echo "<option>$r[accno]</option>";
                        }
                        $_SESSION['accno']= $cnt;
                        ?>
                        </select> 
                        Amount : <input id="id" name="amount" style="width:60px;" type="text"></input>
                        <input name="transact" type="submit" value="Submit"></input>
                    </fieldset>
                </div>
                </form>
            </td>
        </tr>
        <?php
        }
        ?>
        </table>
    </div>
    </section>
    <script>
    function display_detail(id){
    if( document.getElementById(id).style.display == 'inline' ){
    document.getElementById(id).style.display = 'none';
    }else {
    document.getElementById(id).style.backgroundColor = '#ffff00'; 
    document.getElementById(id).style.display = 'inline'; 
    }
    } 
    </script>
    </body>
</html>