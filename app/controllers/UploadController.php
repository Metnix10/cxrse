<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

$cloud_name = "dkqbijjd"; 
$upload_preset = "cxrse_portadas"; 

if (isset($_FILES['imagen'])) {
    
    $file_tmp_path = $_FILES['imagen']['tmp_name'];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api.cloudinary.com/v1_1/" . $cloud_name . "/image/upload");
    curl_setopt($ch, CURLOPT_POST, TRUE);
    

    $post_data = [
        'file' => new CURLFile($file_tmp_path),
        'upload_preset' => $upload_preset
    ];
    
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    $result = json_decode($response);
    
    if ($http_code === 200 && isset($result->secure_url)) {
        http_response_code(200);
        echo json_encode([
            "mensaje" => "Imagen subida exitosamente a Cloudinary",
            "url_imagen" => $result->secure_url
        ]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Error de Cloudinary: " . ($result->error->message ?? "Desconocido")]);
    }

} else {
    http_response_code(400);
    echo json_encode(["error" => "No se recibió ninguna imagen."]);
}
?>