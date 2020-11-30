<?php

// eCommerce Employee Portal
// Written by Danish Siddiqui
// Last Updated: 11/27/2020
// getOrders.php

// ensure errors are off:
// error_reporting(E_ALL);
// ini_set("display_errors","On");

session_start();
require_once('db-connect.php');

// enters uploaded CSV into the database.
class CsvEntry
{
  public $uploadDate;
  public $conn;

  // takes file as input.
  // inserts file contents to database.
  function sendToDatabase($file)
  {
    $handle = fopen($file,"r");

    $t = 0;
    while(($content = fgetcsv($handle,1000,",")) !== false)
    {
      $c = $content;
      if($t > 0 && $t < 25)
      {
        $net = substr($c[16],1) * $c[15];

        $query =  "insert into orders (itemId, username, salesRecord, buyerEmail, shippingFirstName, shippingLastName, street1, street2, city, state, zip, quantity, total, tax,
        paypalfee, fvf, fulfillmentCost, fulfillmentOrderNumber, fulfillmentSource, shipping, gspReference, status, profit, profitMargin, retrievalTime, phone, promotedListing,
        orderNumber, listedBy) VALUES ('".$c[12]."', '".$c[1]."', '".$c[0]."' , '".$c[4]."' , '".$c[2]."' , '' , '".$c[5]."' , '".$c[6]."','".$c[7]."' ,
        '".$c[8]."' , '".$c[9]."' , '".$c[15]."' , '".$net."' , '".$c[18]."' , 0 , 0 , 0 , '' ,";
        $query.="'N/A', '".$c[17]."' , '' , 'pending' , 0, 0, '".$c[25]."', '".$c[43]."' , 'No', '', '".$c[33]."')";

        if ($this->conn->query($query) === TRUE) {}
        else {
          echo json_encode("Error updating record: " . $this->conn->error);
        }

      }
      $t++;
    }
  }
}

// Takes order information from database
// Displays it on screen
class OrdersDisplay
{
  public $transactions;
  public $conn;

  // Executes query to database
  function getTransactions($email)
  {
    // Import Connection

    $result = mysqli_query($this->conn, $this->buildQuery());
    $rows = mysqli_num_rows($result);
    echo "<br />".$rows;

    if($rows > 0)
    {
      $this->transactions = $result;
    }
  }

  // Displays transactions on screen
  function displayTransactions()
  {
    $output = "";
    while($row = mysqli_fetch_array($this->transactions))
    {
      $output.='<div class="item">';

      $output.='
      <div class="box-small" id="picture_sales_record">
				<center>

				<a href="https://www.ebay.com/itm/'.$row["itemId"].' " target="_blank">
					<img style="padding: 2.5px;" src="'.$row["image"].'"></img><br />
					'.$row["salesRecord"].'
				</a>
				<p style="font-size:14px;">Listed By:</p>
				<p style="font-size:14px;">'.$row['listedBy'].'</p>
				</center>
			</div>
      ';

      $output.=
      '<div class="box-large" id="shipment_details">
				<p style="margin:0px;padding:0px;">'.$row["username"].'</p>
				<p class="shippingFirstName" style="display:inline;display:inline;" data-id1="'.$row['id'].'" contenteditable> '.$row["shippingFirstName"].' </p>
				<p class="shippingLastName" style="display:inline;" data-id2="'.$row['id'].'" contenteditable> '.$row["shippingLastName"].' </p>
				<p class="street1" data-id3="'.$row['id'].'" contenteditable>'.$row["street1"].' </p>
				<p class="street2" data-id4="'.$row['id'].'" contenteditable>'.$row["street2"].' </p>
				<p class="city" style="display:inline;" data-id5="'.$row['id'].'" contenteditable>'.$row["city"].'</p>
				<p class="state" style="display:inline;" data-id6="'.$row['id'].'" contenteditable> '.$row["state"].'</p>
				<p class="zip" data-id7="'.$row['id'].'" contenteditable> '.$row["zip"].' </p>
				<p class="phone" data-id8="'.$row['id'].'" contenteditable> '.$row["phone"].' </p>
			</div>
      ';

      $output.='
      <div class="box-large" id="sale_info">
				<p class="quantity" data-id10="'.$row['id'].'"><center>Quantity Ordered: '.$row['quantity'].'</center></p>
				<p class="total" data-id11="'.$row['id'].'"><center>Projected Cost: '.number_format(((float)($row['srcPrice']*($row['quantity']/$row['lot']))), 2, '.', '').'</center></p>
			</div>';

      $output.='
      <div class="box-small" id="profit_info">
				<center>
					<p class="profit" data-id12="'.$row['id'].'">$'.$row['profit'].'</p>
					<p class="profitMargin" data-id13="'.$row['id'].'">%'.$row['profitMargin'].'</p>
				</center>
			</div>
			';

      $output.='
      <div class="box-large" id="retrieval_info">
				<center>
				    <p class="itemId"  data-id14="'.$row['id'].'">'.$row['itemId'].'</p>
					  <p class="retrievalTime" data-id15="'.$row['id'].'">'.(new DateTime($row['retrievalTime']))->format("D M j, Y H:i").'</p>'.
            '<p class="fulfilledBy" data-placeholder="Fulfilled By" data-id25="'.$row['id'].'">'.$row['fulfilledBy'].'</p>
        </center>
			</div>';

      $output.='
			<div class="box-large" id="fulfillment_info">
				<center>
					<p class="fulfillmentTime" data-id16="'.$row['id'].'" data-placeholder="Fulfillment Time">'.(new DateTime($row['fulfillmentTime']))->format("D M j, Y H:i").'</p>
					<p class="fulfillmentOrderNumber" data-placeholder="Order Number"  data-id17="'.$row['id'].'" contenteditable>'.$row['fulfillmentOrderNumber'].'</p>
					<p class="fulfillmentCost" data-placeholder="Fulfillment Cost"  data-id18="'.$row['id'].'" contenteditable>'.$row['fulfillmentCost'].'</p>
          <p class="giftCardDiscount" data-placeholder="Gift Card Discount"  data-id23="'.$row['id'].'" contenteditable>'.$row['giftCardDiscount'].'</p>
          <p class="status" data-placeholder="Status" data-id19="'.$row['id'].'" contenteditable>'.$row['status'].'</p>
				</center>
      </div>';

      $output.='
      <div class="box-large" id="fulfillmentSource">
				<a href="'.$row["fulfillmentSource"].'" target="_blank">
					<p class="fulfillmentSource"  data-id21="'.$row['id'].'" contenteditable>'.$row['fulfillmentSource'].'</p>
				</a>
			</div><hr />
			';

      $output.='</div>';
    }
    echo $output;
  }

  // build search query for search filter:
  function buildQuery()
  {
    // base query:
    $query = "select
              orders.*,
              items.image,
              items.srcStock,
              items.srcPrice,
              items.lot
              from orders Left Join items on orders.itemId = items.itemId where salesRecord > 630";


    // accounting for order status filter:

    if($_POST["status"]!="all")
  	{
  		if($_POST["status"]=="cases")
  		{
  			$query.=" and caseStatus='open'";
  		}
  		else
  		{
  			$query.=" and status='".$_POST["status"]."'";
  		}
  	}

    // accounting for words in search bar:
    if(isset($_POST["search"]) && !empty($_POST["search"]))
  	{
      $count = 0;
      $words = explode(" ", $_POST["search"]);

      foreach($words as $word)
  		{
        $word = trim($word);

        if($count < 1)
        {
          $query.=" and (";
          $query.="(orders.itemId like '%".$word."%' or
                  orders.username like '%".$word."%' or
                  orders.fulfillmentSource like '%".$word."%' or
                  orders.salesRecord like '%".$word."%' or
                  orders.buyerEmail like '%".$word."%' or
                  orders.fulfillmentOrderNumber like '%".$word."%' or
                  orders.shippingFirstName like '%".$word."%' or
                  orders.shippingLastName like '%".$word."%' or
                  orders.fulfilledBy like '%".$word."%' or
                  orders.listedBy like '%".$word."%')";

        }
        else
        {
          $query.=" or (orders.itemId like '%".$word."%' or
                    orders.fulfillmentSource like '%".$word."%' or
                    orders.username like '%".$word."%' or
                    orders.salesRecord like '%".$word."%' or
                    orders.buyerEmail like '%".$word."%' or
                    orders.fulfillmentOrderNumber like '%".$word."%' or
                    orders.shippingFirstName like '%".$word."%' or
                    orders.shippingLastName like '%".$word."%' or
                    orders.fulfilledBy like '%".$word."%' or
                    orders.listedBy like '%".$word."%')";
        }

        echo "<br />".$word;

        $count++;
      }

      $query.=")";
    }

    echo "<br />".$query;
    $query .=" order by salesRecord DESC LIMIT 0, 150";
    return $query;
  }

}

// If upload is set, then handle the upload.
if(isset($_POST['submit']))
{
  $insertion = new CsvEntry();
  $insertion->conn = $conn;

  $file = $_FILES['csvfile']['tmp_name'];
  $insertion->sendToDatabase($file);

  header("Location: orders.php", true);
  exit();
}
// If upload isn't set then display orders.
else
{
  $orders = new OrdersDisplay();
  $orders->conn = $conn;
  $orders->getTransactions($_SESSION['userEmail']);
  $orders->displayTransactions();
}


 ?>
