<?php
require_once 'php-graph-sdk-5.x/src/Facebook/autoload.php';

// Configurar credenciales de la API de Facebook
$fb = new Facebook\Facebook([
    'app_id' => '990224855964782',
    'app_secret' => 'c48f06ee32fa2984ec6f9e24e775cff6',
    'default_graph_version' => 'v19.0',
]);

// Token de acceso de Instagram
$accessToken = 'EAAOEmrDJnG4BOwMfQDT0bJgw6lMESkDUcWOh5CECqfjDSU2Y6WxIqMdZCTru9Fp5uRXHjwxe7ZCBtmIEHfTGw3dcnb3IlmBdfABCCRfvkayK8yEazh4CO9SXfmP0ZB9KcJJy84LSZClE6UOJVRlSpkPAZAgLl9l5uqX8VyLVZC7IGuHP4im2IR7CtecRqvZABUdwLjJ2vbDN2FNDrb5ZB5I3CkozEmwnNoRQarcWIIKhwQwZD';

// ID de usuario de Instagram
$igUserId = '17841465658234338';

// Función para cargar la imagen en ImgBB y obtener la URL de la imagen cargada
function uploadImageAndGetUrl($imagePath) {
    // Configurar la URL del punto final de carga de ImgBB
    $uploadUrl = 'https://api.imgbb.com/1/upload?key=21e89e645bae3b85fb6ce050b09d6930';

    // Configurar datos para la carga de la imagen
    $postData = array('image' => base64_encode(file_get_contents($imagePath)));

    // Inicializar cURL para la carga de la imagen
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $uploadUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    $response = curl_exec($ch);
    curl_close($ch);

    // Decodificar la respuesta JSON y obtener la URL de la imagen cargada
    $responseData = json_decode($response, true);
    if (isset($responseData['data']['url'])) {
        return $responseData['data']['url'];
    } else {
        return false;
    }
}

// Manejar la subida de la imagen y la publicación en Instagram
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verificar si se ha seleccionado un archivo de imagen
    if (isset($_FILES["imagen"]) && $_FILES["imagen"]["error"] == 0) {
        $imagenTmpName = $_FILES["imagen"]["tmp_name"];
        
        // Subir la imagen a ImgBB y obtener la URL de la imagen cargada
        $imageUrl = uploadImageAndGetUrl($imagenTmpName);
        
        if ($imageUrl) {
            // Crear un contenedor de imagen en Instagram con la URL de la imagen
            try {
                $response = $fb->post('/' . $igUserId . '/media', [
                    'image_url' => $imageUrl,
                    'caption' => $_POST['comentario']
                ], $accessToken);
                $graphNode = $response->getGraphNode();
                $containerId = $graphNode['id'];
                
                // Publicar la imagen desde el contenedor creado en Instagram
                $response = $fb->post('/' . $igUserId . '/media_publish', [
                    'creation_id' => $containerId
                ], $accessToken);
                
                echo 'Imagen publicada exitosamente en Instagram.';
            } catch (Facebook\Exceptions\FacebookResponseException $e) {
                echo 'Error al publicar la imagen: ' . $e->getMessage();
            } catch (Facebook\Exceptions\FacebookSDKException $e) {
                echo 'Error de SDK de Facebook: ' . $e->getMessage();
            }
        } else {
            echo 'Error al subir la imagen a ImgBB.';
        }
    } else {
        echo 'Error: No se ha seleccionado una imagen.';
    }
}

?>
