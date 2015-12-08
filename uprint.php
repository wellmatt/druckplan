<?php
// error_reporting(-1);
// ini_set('display_errors', 1);
if ($_REQUEST["post"])
{
    $now = date('c');
    $xml = "<status-update><order-number>{$_REQUEST["ord_number"]}</order-number><datetime>{$now}</datetime><new-status>ready_for_dispatch</new-status></status-update>";
    $token = "a44d65770e9d21fd0fa4a85z61e7f237b3229c9e";
    $url = 'https://spapi.unitedprint.com/spapi/v1/xml/order/status?token='.$token;
    $ch = curl_init();
    curl_setopt( $ch, CURLOPT_URL, $url );
    curl_setopt( $ch, CURLOPT_POST, true );
    curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml'));
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
    curl_setopt( $ch, CURLOPT_POSTFIELDS, $xml );
    $result = curl_exec($ch);
    curl_close($ch);
    echo '<pre>'.$result.'</pre>';
    echo '</br></br></br><a href="uprint.php"><b><u>Neu</u></b></a>';
} else {
    ?>
    
    <html>
        <head>
        </head>
        <body>
            <form method="post" action=<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>>
                Enter Order Number: <input type="text" name="ord_number">
            </form>
        </body>
    </html>
    
    <?php
}

