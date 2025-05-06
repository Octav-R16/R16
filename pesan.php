<?php
// Kirim pesan ke HR
$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://api.fonnte.com/send',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => array(
        'target' => '6281223542690,62895361639529,6281324240316', // Kirim ke kedua nomor
        'message' => 'Bro ada komplenan coba lo buka di https://tascominimart.co.id/tiket/form_login.php',
        'countryCode' => '62',
    ),
    CURLOPT_HTTPHEADER => array(
        'Authorization: MFWeP@p92_26G12WzcPK' // Token API Anda
    ),
));
$response = curl_exec($curl);
curl_close($curl);
echo $response;
?>