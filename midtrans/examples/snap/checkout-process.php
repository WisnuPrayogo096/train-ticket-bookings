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

        $booked_timing = get_from_table($conn, "bookings", "booking_id", $pnr, "booking_created");

        $dep_date = get_from_table($conn, "routes", "route_id", $route_id, "route_dep_date");

        $dep_time = get_from_table($conn, "routes", "route_id", $route_id, "route_dep_time");

        $bus_no = get_from_table($conn, "routes", "route_id", $route_id, "bus_no");

}
// Set Your server key
// can find in Merchant Portal -> Settings -> Access keys
Config::$serverKey = 'SB-Mid-server-4iOw94sCjH9wZ0dMs0KxmgzY';
Config::$clientKey = 'SB-Mid-client-k_WUlcqWzAscht6z';

// non-relevant function only used for demo/example purpose
printExampleWarningMessage();

// Uncomment for production environment
// Config::$isProduction = true;

// Enable sanitization
Config::$isSanitized = true;

// Enable 3D-Secure
Config::$is3ds = true;

// Uncomment for append and override notification URL
// Config::$appendNotifUrl = "https://example.com";
// Config::$overrideNotifUrl = "https://example.com";

// Required
$transaction_details = array(
    'order_id' => $pnr,
    'gross_amount' => $booked_amount, // no decimal allowed for creditcard
);

// Optional
$item1_details = array(
    'id' => 'a1',
    'price' => 18000,
    'quantity' => 3,
    'name' => "Apple"
);

// Optional
$item_details = array ($item1_details);


// Optional
$customer_details = array(
    'first_name'    => "$customer_name",
    'phone'         => "$customer_phone",
);

// Optional, remove this to display all available payment methods
$enable_payments = array('credit_card','cimb_clicks','mandiri_clickpay','echannel');

// Fill transaction details
$transaction = array(
    'enabled_payments' => $enable_payments,
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

echo "snapToken = ".$snap_token;

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
    <body>
        <button id="pay-button">Pay!</button>
        <pre><div id="result-json">JSON result will appear here after payment:<br></div></pre> 

        <!-- TODO: Remove ".sandbox" from script src URL for production environment. Also input your client key in "data-client-key" -->
        <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="<?php echo Config::$clientKey;?>"></script>
        <script type="text/javascript">
            document.getElementById('pay-button').onclick = function(){
                // SnapToken acquired from previous step
                snap.pay('<?php echo $snap_token?>', {
                    // Optional
                    onSuccess: function(result){
                        /* You may add your own js here, this is just example */ document.getElementById('result-json').innerHTML += JSON.stringify(result, null, 2);
                    },
                    // Optional
                    onPending: function(result){
                        /* You may add your own js here, this is just example */ document.getElementById('result-json').innerHTML += JSON.stringify(result, null, 2);
                    },
                    // Optional
                    onError: function(result){
                        /* You may add your own js here, this is just example */ document.getElementById('result-json').innerHTML += JSON.stringify(result, null, 2);
                    }
                });
            };
        </script>
    </body>
</html>
