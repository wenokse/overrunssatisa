<?php
include 'includes/session.php';
$conn = $pdo->open();

if (isset($_POST['id'])) {
    $id = $_POST['id'];

    try {
        $stmt = $conn->prepare("SELECT * FROM sales WHERE id=:id");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        $stmt2 = $conn->prepare("SELECT * FROM details LEFT JOIN products ON products.id=details.product_id WHERE sales_id=:id");
        $stmt2->execute(['id' => $id]);
        $total = 0;
        $details = '';
        $productImages = []; // Array to store product images
        $seenProducts = [];  // Array to track seen products

        foreach ($stmt2 as $row2) {
            $subtotal = ($row2['price'] * $row2['quantity']) + $row2['shipping'];
            $total += $subtotal;
            $details .= $row2['name'] . ' - ' . $row2['quantity'] . ' x ' . number_format($row2['price'], 2) . '<br>';

            // Store product image if not already seen
            if (!in_array($row2['product_id'], $seenProducts)) {
                $productImages[] = (!empty($row2['photo'])) ? 'images/' . $row2['photo'] : 'images/noimage.jpg';
                $seenProducts[] = $row2['product_id'];
            }
        }


        $status = '';
        if ($row['status'] == 2) {
            $status = "<p class='btn-sm btn-primary text-center'><i class='fa fa-bicycle'></i> On Delivery</p>";
        }

       
        $stmt3 = $conn->prepare("SELECT address FROM users WHERE id=:user_id");
        $stmt3->execute(['user_id' => $row['user_id']]);
        $user = $stmt3->fetch();

       
        function getDistance($address) {
            $distances = [
                'Bantayan, Atop-atop' => 9,
                'Bantayan, Baigad' => 7, 
                'Bantayan, Bantigue' => 3,
                'Bantayan, Baod' => 7, 
                'Bantayan, Binaobao (Poblacion)' => 1,
                'Bantayan, Guiwanon' => 2,
                'Bantayan, Hilotongan' => 8,
                'Bantayan, Kabac' => 5,
                'Bantayan, Kabangbang' => 4,
                'Bantayan, Kampingganon' => 3,
                'Bantayan, Kangkaibe' => 4,
                'Bantayan, Lipayran' => 7,
                'Bantayan, Luyongbaybay' => 2,
                'Bantayan, Mojon' => 6,
                'Bantayan, Obo-ob' => 5,
                'Bantayan, Patao' => 8,
                'Bantayan, Putian' => 6,
                'Bantayan, Sillon' => 5,
                'Bantayan, Suba (Poblacion)' => 1,
                'Bantayan, Sulangan' => 7,
                'Bantayan, Sungko' => 5,
                'Bantayan, Tamiao' => 4,
                'Bantayan, Ticad' => 7,
                'Madridejos, Bunakan' => 21,
                'Madridejos, Kangwayan' => 22,
                'Madridejos, Kaongkod' => 21,
                'Madridejos, Kodia' => 20,
                'Madridejos, Maalat' => 21,
                'Madridejos, Malbago' => 20,
                'Madridejos, Mancilang' => 19,
                'Madridejos, Pili' => 20,
                'Madridejos, Poblacion' => 20,
                'Madridejos, San Agustin' => 21,
                'Madridejos, Tabagak' => 22,
                'Madridejos, Talangnan' => 23,
                'Madridejos, Tarong' => 23,
                'Madridejos, Tugas' => 21,
                'Santa Fe, Balidbid' => 11,
                'Santa Fe, Hagdan' => 12,
                'Santa Fe, Hilantagaan' => 15,
                'Santa Fe, Kinatarkan' => 19,
                'Santa Fe, Langub' => 14,
                'Santa Fe, Maricaban' => 13,
                'Santa Fe, Okoy' => 11,
                'Santa Fe, Poblacion' => 10,
                'Santa Fe, Pooc' => 12,
                'Santa Fe, Talisay' => 10 
            ];
            return $distances[$address] ?? 5; // Default distance if address is not in array
        }

        // Calculate distance and time
        $distance = getDistance($user['address']);
        $time = round($distance / 4); // Assuming average speed of 40 km/h

        $response = [
            'date' => date('M d, Y', strtotime($row['sales_date'])),
            'transaction' => $row['pay_id'],
            'list' => $details,
            'total' => '&#8369; ' . number_format($total, 2),
            'status' => $status,
            'distance' => $distance,
            'time' => $time,
            'address' => $user['address'],
            'images' => $productImages
        ];

        echo json_encode($response);
    } catch (PDOException $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }

    $pdo->close();
}
?>
