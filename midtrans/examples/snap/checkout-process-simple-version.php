<?php
// This is just for very basic implementation reference, in production, you should validate the incoming requests and implement your backend more securely.
// Please refer to this docs for snap popup:
// https://docs.midtrans.com/en/snap/integration-guide?id=integration-steps-overview

namespace Midtrans;

require_once dirname(__FILE__) . '/../../Midtrans.php';
include "../../../assets/partials/_functions.php";
// require 'C:\xampp\htdocs\pesankereta\assets\partials/_functions.php';
$conn = db_connect();    

if(!$conn) 
    die("Connection Failed");

if(isset($_GET["pnr"]))
    {
        $pnr = $_GET["pnr"];

        $route_id = get_from_table($conn, "bookings", "booking_id", $pnr, "route_id");

        $customer_id = get_from_table($conn, "bookings", "booking_id", $pnr, "customer_id");

        $customer_name = get_from_table($conn, "customers", "customer_id", $customer_id, "customer_name");

        $customer_phone = get_from_table($conn, "customers", "customer_id", $customer_id, "customer_phone");
        
        $customer_route = get_from_table($conn, "bookings", "booking_id", $pnr, "customer_route");

        $booked_amount = get_from_table($conn, "bookings", "booking_id", $pnr, "booked_amount");

        $booked_seat = get_from_table($conn, "bookings", "booking_id", $pnr, "booked_seat");

        $s_status = get_from_table($conn, "bookings", "booking_id", $pnr, "status");

        $booked_timing = get_from_table($conn, "bookings", "booking_id", $pnr, "booking_created");

        $route_cities = get_from_table($conn, "routes", "route_id", $route_id, "route_cities");

        $dep_date = get_from_table($conn, "routes", "route_id", $route_id, "route_dep_date");

        $dep_time = get_from_table($conn, "routes", "route_id", $route_id, "route_dep_time");

        $bus_no = get_from_table($conn, "routes", "route_id", $route_id, "kereta_no");

}
// Set Your server key
// can find in Merchant Portal -> Settings -> Access keys
Config::$serverKey = 'SB-Mid-server-4iOw94sCjH9wZ0dMs0KxmgzY';
Config::$clientKey = 'SB-Mid-client-k_WUlcqWzAscht6z';

// non-relevant function only used for demo/example purpose
printExampleWarningMessage();

// Uncomment for production environment
// Config::$isProduction = true;
Config::$isSanitized = Config::$is3ds = true;

// Required
$transaction_details = array(
    'order_id' => $pnr,
    'gross_amount' => $booked_amount, // no decimal allowed for creditcard
);
// Optional
$item_details = array (
    array(
        'id' => 'a1',
        'price' => "$booked_amount.000",
        'quantity' => 1,
        'name' => "$route_cities"
    ),
  );
// Optional
$customer_details = array(
    'first_name'    => "$customer_name",
    'phone'         => "$customer_phone",
);
// Fill transaction details
$transaction = array(
    'transaction_details' => $transaction_details,
    'customer_details' => $customer_details,
    'item_details' => $item_details,
);

$snap_token = '';
try {
    $snap_token = Snap::getSnapToken($transaction);
}
catch (\Exception $e) {
    echo $e->getMessage();
}

echo nl2br("\nsnapToken = ".$snap_token);

function printExampleWarningMessage() {
    if (strpos(Config::$serverKey, 'your ') != false ) {
        echo "<code>";
        echo "<h4>Please set your server key from sandbox</h4>";
        echo "In file: " . __FILE__;
        echo "<br>";
        echo "<br>";
        echo htmlspecialchars('Config::$serverKey = \'<your server key>\';');
        die();
    } 
}

?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
        <script src="//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script>
        <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
        <title>Resi Tiket</title>
        <style>
            li{
                list-style-type: none;
                padding-left: 0;
            }
        </style>
    </head>
    <body>
        <br>
        <button style="margin-left: 10px;" class="btn btn-success" onclick="window.history.back()">Back To Home</button>
        <div class="container">
            <div class="row">
                <div class="well col-xs-10 col-sm-10 col-md-6 col-xs-offset-1 col-sm-offset-1 col-md-offset-3">
                    <div class="row">
                        <div class="col-xs-6 col-sm-6 col-md-6">
                            <address>
                                <strong>TKTI</strong>
                                <br>
                                Tiket Kereta Teknik Industri
                            </address>
                        </div>
                        <div class="col-xs-6 col-sm-6 col-md-6 text-right">
                            <p>
                                <em><b>Booked On:</b> <br><?php echo $booked_timing; ?></em>
                            </p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="text-center">
                            <h5><b>PNR CODE:</b> <?php echo $pnr; ?></h5>
                            <h3>Amount: Rp. <?php echo $booked_amount; ?></h3>
                        </div>
                        <table class="table table-hover table-striped">
                    <!-- <thead>
                        <tr>
                            <td>   </td>
                            <td>   </td>
                            <th></th>
                           
                            <th class="text-center"></th>
            
                        </tr>
                    </thead> -->
                    <tbody>
                        <tr>                            
                            <td class="col-md-6"><em><b>Customer Name:</b></em></h4></td>
                           
                            <td class="col-md-6 text-center"><?php echo $customer_name; ?></td>
                        </tr>
                        <tr>
                            <td class="col-md-6"><em><b>Contact Details:</b></em></h4></td>
                            <td class="col-md-6 text-center"><?php echo $customer_phone; ?></td>
                        </tr>
                        <tr>
                            <td class="col-md-6"><em><b>Route:</b></em></h4></td>
                            <td class="col-md-6 text-center"><?php echo $customer_route; ?></td>
                        </tr>
                        <tr>
                            <td class="col-md-6"><em><b>Train Number:</b></em></h4></td>
                            <td class="col-md-6 text-center"><?php echo $bus_no; ?></td>
                        </tr>
                        <tr>
                            <td class="col-md-6"><em><b>Seat Number:</b></em></h4></td>
                            <td class="col-md-6 text-center"><?php echo $booked_seat; ?></td>
                        </tr>
                        <tr>
                            <td class="col-md-6"><em><b>Departure Date:</b></em></h4></td>
                            <td class="col-md-6 text-center"><?php echo $dep_date; ?></td>
                           
                        </tr>
                        <tr>
                            <td class="col-md-6"><em><b>Departure Time:</b></em></h4></td>
                            <td class="col-md-6 text-center"><?php echo $dep_time; ?></td>   
                        </tr>
                    </tbody>
                </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="text-center" style="margin-bottom:40px;">
            <button style="margin-right: 45rem;" onclick="window.print()" class="btn btn-primary">Print</button>
            <button class="btn btn-danger" id="pay-button">Bayar!</button>
        </div>
        <!-- TODO: Remove ".sandbox" from script src URL for production environment. Also input your client key in "data-client-key" -->
        <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="<?php echo Config::$clientKey;?>"></script>
        <script type="text/javascript">
            document.getElementById('pay-button').onclick = function(){
                // SnapToken acquired from previous step
                snap.pay('<?php echo $snap_token?>');
                <?php
                    mysqli_query($conn, "UPDATE `bookings` SET `status`='1' WHERE `booking_id`='$pnr'");
                ?>
            };
        </script>
    </body>
</html>
