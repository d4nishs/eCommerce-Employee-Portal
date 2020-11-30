<?php

// eCommerce Employee Portal
// Written by Danish Siddiqui
// Last Updated: 11/27/2020
// orders.php

// ensure errors are off
// error_reporting(E_ALL);
// ini_set("display_errors","On");

session_start();
$email = $_SESSION['email'];

if(empty($email)){
	header("Location: index.php", true);
	exit();
}
if(isset($_POST['logout'])){
	session_start();
	// destroy the session
	session_destroy();
	header("Location: index.php", true);
	exit();
}

$panel = '<a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
					<a href="orders.php">Orders</a>
					<form action="dash.php" method="POST">
					<button style="margin:25px;" name="logout" type="logout">Sign out</button>
					</form>';

?>

<!DOCTYPE html>
<html>
<head>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
body {
  font-family: "Lato", sans-serif;
}
p:empty:not(:focus)::before {
  content: attr(data-placeholder);
  color: gray;
  font-size: 1.25vw;
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
#searchBar
{
  width: 85%; /* Full-width */
  font-size: 16px; /* Increase font-size */
  padding: 12px 20px 12px 10px; /* Add some padding */
  border: 1px solid #ddd; /* Add a grey border */
  margin-left : 10px;
  display: inline-block;
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

#dropdown
{
	width: 5%;
	font-size: 16px; /* Increase font-size */
	padding: 12px 0px 12px 0px; /* Add some padding */
	border: 1px solid #ddd; /* Add a grey border */
	display: inline-block;
	margin: 0 auto;
}

@media screen and (max-height: 450px) {
  .sidenav {padding-top: 15px;}
  .sidenav a {font-size: 18px;}
}

#queryDiv
{
	margin : 0, auto;
}

.results
{
	display: block;
	margin: 0 auto;
	width: 90%;
}
.box-large
{
	display: inline-block;
	padding-left: 25px;
	margin: 0, auto;
	vertical-align: middle;
	width: 10%;
	font-size: 1.25vw;
}
.box-small
{
	display: inline-block;
	padding-left: 25px;
	margin: 0, auto;
	vertical-align: middle;
	width: 5%;
	font-size:1.25vw;
}
#shipment_details > p
{
	margin: 0px;
	padding : 0px;
	font-size:1.25vw;
}

#loading
{
 display: inline-block;
 text-align:center;
 background: url('loader.gif') no-repeat center;
 height: 25px;
 padding: 200px;
}
.edit-small
{
	height: 20px;
	width: 20px;
	background: url("editicon.png");
	background-repeat: no-repeat;
	background-size: contain;
	padding: 1px;
	margin: 1px;
	font-size:1.25vw;
}
.notes
{
	height: 25px;
	max-height: 50px;
	font-size:1.25vw;
	text-align: center;
	display: block;
	width: 100%;
}
.toDo
{
	height: 25px;
	max-height: 50px;
	font-size:1.25vw;
	display: block;
	width: 100%;
}
hr
{
	margin: 20px;
}
#closeCase
{
	background-color: #4CAF50; /* Green */
	border: none;
	color: white;
	padding: 15px 32px;
	text-align: center;
	text-decoration: none;
	font-size: 16px;
	margin: 4px 2px;
	cursor: pointer;
}
#openCase
{
	background-color: #ffface; /* Green */
	border: none;f
	color: black;
	padding: 15px 32px;
	text-align: center;
	text-decoration: none;
	font-size: 16px;
	margin: 4px 2px;
	cursor: pointer;
}
.item
{
	width: 100%;
	font-size:1.25vw;
}

</style>
</head>
<body>

<div id="mySidenav" class="sidenav" >
</div>

<div id ="main">
	<center>
	<div id="queryDiv">
		<select id="dropdown" onchange="queryOrders()">
		  <option value="all">All</option>
		  <option value="fulfilled">Fulfilled</option>
		  <option value="pending">Pending</option>
		  <option value="cancelled">Cancelled</option>
		  <option value="returned">Returns</option>
		  <option value="cases">Cases</option>
		</select>

		<input type="text" id="searchBar" placeholder="Search for values.." contenteditable="true">
		<form action="getOrders.php" method="POST" enctype="multipart/form-data">
			<input type="file" name="csvfile" required="required" />
			<input name="submit" type="submit" value="upload" />
		</form>
	</div>
	</center>
	<center><div id="result" class="result"></div></center>
	<div id="results" class="results"></div>

</div>

<script>

var panel = <?php echo json_encode($panel); ?>;

$(document).ajaxStart(function() {
  $("#loading").show();
});

$(document).ajaxStop(function() {
  $("#loading").hide();
  $("#st-tree-container").show();
});

var searchBar = document.getElementById("searchBar");
searchBar.addEventListener("keypress", function(event) {
    if (event.keyCode == 13)
        queryOrders();
});

queryOrders();

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

function queryOrders()
{
	$('#results').html('<center><div id="loading" style="" ></div></center>');
	// build the query, get the 3 values.
	// the 3 values are : value of dropdown
	// value of priviledge.
	// value of search bar.

	var query = "";

	// setting status
	query+="status="+document.getElementById("dropdown").options[document.getElementById("dropdown").selectedIndex].value;

	// setting search bar value
	query+="&search="+encodeURIComponent(document.getElementById("searchBar").value);

	console.log(query);

	$.ajax({
            url:"getOrders.php",
            method:"POST",
            data: query,
			success:function(data){
				$('#results').html(data);
			}
    })


}

function edit_data(id, text, column_name)
{
	var table = "orders";
	$.ajax({
		url:"edit.php",
		method:"POST",
		data:{table:table, id:id, text:text, column_name:column_name},
		dataType:"text",
		success:function(data){
			//alert(data);
			$('#result').html("<div class='alert alert-success'>"+data+"</div>");
		}
	});
}

function update_profit(id, updatedFulfillmentCost)
{
	edit_data(id,updatedFulfillmentCost, "fulfillmentCost");
}

$(document).on('blur', '.shippingFirstName', function(){
	var id = $(this).data("id1");
	var shippingFirstName = $(this).text();
	edit_data(id, shippingFirstName, "shippingFirstName");
});
$(document).on('blur', '.shippingLastName', function(){
	var id = $(this).data("id2");
	var shippingLastName = $(this).text();
	edit_data(id,shippingLastName, "shippingLastName");
});
$(document).on('blur', '.street1', function(){
	var id = $(this).data("id3");
	var street1 = $(this).text();
	edit_data(id, street1, "street1");
});
$(document).on('blur', '.street2', function(){
	var id = $(this).data("id4");
	var street2 = $(this).text();
	edit_data(id,street2, "street2");
});
$(document).on('blur', '.city', function(){
	var id = $(this).data("id5");
	var city = $(this).text();
	edit_data(id,city, "city");
});
$(document).on('blur', '.state', function(){
	var id = $(this).data("id6");
	var state = $(this).text();
	edit_data(id,state, "state");
});

$(document).on('blur', '.zip', function(){
	var id = $(this).data("id7");
	var zip = $(this).text();
	edit_data(id,zip, "zip");
});

$(document).on('blur', '.phone', function(){
	var id = $(this).data("id8");
	var phone = $(this).text();
	edit_data(id,phone, "phone");
});

$(document).on('blur', '.fulfillmentOrderNumber', function(){
	var id = $(this).data("id17");
	var fulfillmentOrderNumber = $(this).text();
	edit_data(id,fulfillmentOrderNumber, "fulfillmentOrderNumber");
});

$(document).on('blur', '.fulfillmentCost', function(){
	var id = $(this).data("id18");
	var fulfillmentCost = $(this).text();
	update_profit(id, fulfillmentCost);
});

$(document).on('blur', '.status', function(){
	var id = $(this).data("id19");
	var status = $(this).text();
	edit_data(id,status, "status");
});

</script>

</body>
</html>
