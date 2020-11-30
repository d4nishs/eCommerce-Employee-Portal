<?php
    // eCommerce Employee Portal
    // Written by Danish Siddiqui
    // Last Updated: 11/27/2020
    // dash.php

    require_once('db-connect.php');
    session_start();

    $email = $_SESSION['email'];

    // Ensure the session is set
    // If session isn't set then redirect user
    if (empty($email)) {
    	header("Location: index.php", true);
    	exit();
    }
    if (isset($_POST['logout'])) {
    	session_start();

    	// destroy the session when logout is clicked
    	session_destroy();
    	header("Location: index.php", true);
    	exit();
    }

    // initialize navigation panel as global var
    $panel = '<a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
              <a href="orders.php">Orders</a>
              <form action="dash.php" method="POST">
              <button style="margin:25px;" name="logout" type="logout">Sign out</button>
              </form>';


    // Generate the Page
    // Call required functions
    function generatePage()
    {
      // primary welcoming message
      echo '<center>';
      echo 'Welcome '.$_SESSION['name'].',<br />';
      echo '<div class="stats"><div class="today row">';
      date_default_timezone_set('America/New_York');

      $date = date('y-m-d', time());
      $stop_date = date('y-m-d', strtotime($stop_date . ' +1 day'));

      echo '<h2>Today in Numbers</h2>';
      reQuery($conn,$date,$stop_date);

      echo '<div class="last7Days row">';
      echo '<h2>Last 7 days in Numbers</h2>';

      $date = date('y-m-d', time());
      $stop_date = date('y-m-d', strtotime($stop_date . ' +1 day'));
      $begin = date('y-m-d', strtotime($stop_date . ' -7 day'));

      reQuery($conn,$begin,$stop_date);

      echo '<div class="last30days row">';
      echo '<h2>Last 30 days in Numbers</h2>';

      $date = date('y-m-d', time());
      $stop_date = date('y-m-d', strtotime($stop_date . ' +1 day'));
      $begin = date('y-m-d', strtotime($stop_date . ' -30 day'));

      reQuery($conn,$begin,$stop_date);

      echo '<div class="thisMonth row">';
      echo '<h2>This Month in Numbers</h2>';

      $first_day_this_month = date('y-m-01'); // hard-coded '01' for first day
      $date = date('y-m-d', time());
      $stop_date = date('y-m-d', strtotime($stop_date . ' +1 day'));

      reQuery($conn,$first_day_this_month,$stop_date);
      echo'</div></div>';
    }


    // Function used to get data from database
    // Gets data based on a certain time frame
    function reQuery($conn,$date,$stop_date)
    {
    	$profit = 0;
    	$fulfillmentCost = 0;
    	$sales = 0;

    	$result = mysqli_query($conn, "select * from orders where fulfillmentTime between '$date' and '$stop_date' and status='fulfilled' ");
    	$rows = mysqli_num_rows($result);

    	if ($rows > 0) {
    		while ($row = mysqli_fetch_array($result)) {
    			$profit += number_format((float)($row['total']-$row['tax']-$row['fvf']-$row['paypalfee']-$row['fulfillmentCost']), 2, '.', '');
    			$sales +=  number_format((float)($row['total']-$row['tax']), 2, '.', '');
    			$fulfillmentCost +=$row['fulfillmentCost'];
    		}
    	}

    	printRow($profit, $rows, $sales, $fulfillmentCost);
    }

    // Function that displays information about sales on user dashboard
    function printRow($profit, $rows, $sales, $fulfillmentCost)
    {
    	echo '
    	<div class="profit col">
    	<u><div style="margin-bottom:2px;">Profit</div></u>
    	<div class="totalProfit">$'.$profit.'</div>
    	<div class="avgProfit">$ '.number_format((float)($profit/$rows), 2, ".", "").'</div>
    	</div>

    	<div class="margin col">
    	<u><div style="margin-bottom:2px;">Margin</div></u>
    	<div class="totalmargin">%'. (number_format((float)($profit/$sales), 3, ".", "")*100) .'</div>
    	</div>

    	<div class="fulfillment col">
    	<u><div style="margin-bottom:2px;">Cost of Goods</div></u>
    	<div class="totalCost">$'.number_format((float)($fulfillmentCost), 2, ".", "").'</div>
    	<div class="avgCost">$'.number_format((float)($fulfillmentCost/$rows), 2, ".", "").'</div>
    	</div>

    	<div class="sales col">
    	<u><div style="margin-bottom:2px;">Sales</div></u>
    	<div class="numSales">'. $rows.'</div>
    	<div class="totalSales">$'. number_format((float)($sales), 2, ".", "").'</div>
    	<div class="avgSale">'. number_format((float)($sales/$rows), 2, ".", "").'</div>
    	</div>
    	</div>';
    }

?>

<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">

<style>
body {
  font-family: "Lato", sans-serif;
}
#main
{
	transition: 0.5s;
}
.sidenav {
  height: 100%;
  width: 0;
  position: fixed;
  z-index: 1;
  top: 0;
  left: 0;
  background-color: #111;
  overflow-x: hidden;
  transition: 0.5s;
  padding-top: 60px;
}

.sidenav a {
  padding: 8px 8px 8px 32px;
  text-decoration: none;
  font-size: 25px;
  color: #818181;
  display: block;
  transition: 0.3s;
}

.sidenav a:hover {
  color: #f1f1f1;
}

.sidenav .closebtn {
  position: absolute;
  top: 0;
  right: 25px;
  font-size: 36px;
  margin-left: 50px;
}

@media screen and (max-height: 450px) {
  .sidenav {padding-top: 15px;}
  .sidenav a {font-size: 18px;}
}
.competitors
{
	margin-top: 3%;
	font-size: 1.5vw;
}
.col
{
	display: inline-block;
	padding-left: 25px;
	margin: 0, auto;
	vertical-align: middle;
	width: 15%;
	font-size:1.5vw;
}
.row
{
	text-align: center;
	margin: 3%;
}
</style>
</head>
<body>

<div id="mySidenav" class="sidenav" >
</div>

<div id ="main">
<?php generatePage();?>
</div>
</body>
</html>

<script>
var panel = <?php echo json_encode($panel); ?>;
closeNav();
function openNav() {
  document.getElementById("mySidenav").style.width = "250px";
  document.getElementById("mySidenav").innerHTML = panel;
  document.getElementById("main").style.marginLeft = "250px";
}
function closeNav() {
  document.getElementById("mySidenav").style.width = "32.5px";
  document.getElementById("mySidenav").innerHTML = '<span style="font-size:30px;cursor:pointer;color:#818181" onclick="openNav()">&#9776;</span>';
  document.getElementById("main").style.marginLeft = "32.3px";
}
</script>
