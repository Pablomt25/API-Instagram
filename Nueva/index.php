<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subir Imágenes a Instagram</title>
    <link rel="stylesheet" href="index.css">
    <style>
        .image {
            margin-bottom: 20px;
        }
        .image img {
            max-width: 300px;
            height: auto;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Subir Imágenes a Instagram</h1>
        <form action="subir_imagen.php" method="post" enctype="multipart/form-data">
            <label for="imagen">Seleccionar imagen:</label>
            <input type="file" name="imagen" id="imagen" accept="image/*" required>
            <label for="comentario">Comentario (opcional):</label>
            <textarea name="comentario" id="comentario" rows="4" placeholder="Escribe un comentario aquí"></textarea>
            <button type="submit">Subir Imagen</button>
        </form>
    </div>

    <div class="container2">
        <h1>Imágenes de Instagram</h1>
        <div class="gallery">
            <?php
            // Requerir la librería de la API de Facebook
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
            $igUserId = '17841465658234338'; // Reemplaza con tu ID de usuario de Instagram

            try {
                // Realizar una solicitud GET para obtener las imágenes de la cuenta de Instagram
                $response = $fb->get('/' . $igUserId . '/media', $accessToken);
                $graphEdge = $response->getGraphEdge();

                // Iterar sobre las imágenes y mostrarlas en la página
                foreach ($graphEdge as $graphNode) {
                    $imageUrl = $graphNode->getField('media_url');
                    echo '<div class="image"><img src="' . htmlspecialchars($imageUrl) . '" alt="Imagen de Instagram"></div>';
                }
            } catch (Facebook\Exceptions\FacebookResponseException $e) {
                echo 'Error al obtener las imágenes de Instagram: ' . $e->getMessage();
            } catch (Facebook\Exceptions\FacebookSDKException $e) {
                echo 'Error de SDK de Facebook: ' . $e->getMessage();
            }
            ?>
        </div>
    </div>
</body>
</html>
