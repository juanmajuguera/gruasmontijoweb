<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { exit(0); }

$postData = file_get_contents("php://input");
$request = json_decode($postData);

if ($request && isset($request->phone)) {
    $nombre = strip_tags($request->name) ?: 'No indicado';
    $telefono = strip_tags($request->phone);
    $fecha = date('d-m-Y H:i:s');

    if (isset($request->email)) {
        $tipo = "CONTACTO DETALLADO";
        $extra = "📧 Email: " . strip_tags($request->email) . "\n📝 Msj: " . strip_tags($request->message);
    } else {
        $tipo = "ASISTENCIA RÁPIDA (HERO)";
        $extra = "📍 Ubicación: " . strip_tags($request->location);
    }

    $contenido = "--- $tipo ($fecha) ---\n👤 Nombre: $nombre\n📞 Teléfono: $telefono\n$extra\n" . str_repeat("-", 30) . "\n\n";

    file_put_contents(__DIR__ . '/registro_asistencia.txt', $contenido, FILE_APPEND);

    $to = "gruasmontijo@gmail.com";
    $subject = "=?UTF-8?B?".base64_encode("Web: $tipo de $nombre")."?=";
    $headers = "From: Web Grúas Montijo <noreply@gruasmontijo.es>\r\nContent-Type: text/plain; charset=UTF-8\r\n";

    @mail($to, $subject, $contenido, $headers);

    echo json_encode(["status" => "success"]);
} else {
    http_response_code(400);
    echo json_encode(["status" => "error"]);
}