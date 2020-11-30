<?php
// eCommerce Employee Portal
// Written by Danish Siddiqui
// Last Updated: 11/27/2020
// edit.php
// helper function that edits fields. 


session_start();
require_once('db-connect.php');

$id = $_POST["id"];
$text = $_POST["text"];
$column_name = $_POST["column_name"];

if($column_name=="fulfillmentCost")
{


	$result = mysqli_query($conn, "select * from ".$_POST["table"]." where id='".$id."'");
	$rows = mysqli_num_rows($result);



	if($rows > 0)
	{
		while($row = mysqli_fetch_array($result))
		{
			$text = $text * (1 - ($row['giftCardDiscount']/100));

			echo '<br />'.$text;
			echo '<br />'.$row['giftCardDiscount'];

			$profit = $row['total']-$row['tax']-$row['fvf']-$row['paypalfee']-$text;
			$margin = ($profit/$row['total'])*100.00;
			$margin = number_format((float)$margin, 2, '.', '');
			$profit = number_format((float)$profit, 2, '.', '');

			$sql = "UPDATE ".$_POST["table"]." SET profit='".$profit."' WHERE id='".$id."'";


			if(mysqli_query($conn, $sql))
			{}

			$sql = "UPDATE ".$_POST["table"]." SET profitMargin='".$margin."' WHERE id='".$id."'";

			if(mysqli_query($conn, $sql))
			{}
		}
	}

}
if($column_name=="status")
{
	// then update fulfillment time.
	// then update fulfilled by.

	$sql = "UPDATE ".$_POST["table"]." SET fulfilledBy='".$_SESSION['name']."' WHERE id='".$id."'";

	if(mysqli_query($conn, $sql))
	{}

	date_default_timezone_set('America/New_York');
	$date = date('Y-m-d H:i:s', time());

	$sql = "UPDATE ".$_POST["table"]." SET fulfillmentTime='".$date."' WHERE id='".$id."'";

	if(mysqli_query($conn, $sql))
	{}

	// in any case.


}
$sql = "UPDATE ".$_POST["table"]." SET ".$column_name."='".$text."' WHERE id='".$id."'";
echo '<br />'.$sql;
if(mysqli_query($conn, $sql))
{
	echo 'Data Updated';
}

?>
