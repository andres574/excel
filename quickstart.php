<?php
require __DIR__ . '/vendor/autoload.php';


function getClient()
{
    $client = new Google_Client();
    $client->setApplicationName('Google Sheets API PHP Quickstart');
    $client->setScopes(Google_Service_Sheets::SPREADSHEETS);
    $client->setAuthConfig(__DIR__ . '/credentials.json');
    $client->setAccessType('offline');
    $client->setPrompt('select_account consent');

    $tokenPath = __DIR__ . '/token.json';
    if (file_exists($tokenPath)) {
        $accessToken = json_decode(file_get_contents($tokenPath), true);
        $client->setAccessToken($accessToken);
    }

    if ($client->isAccessTokenExpired()) {

        if ($client->getRefreshToken()) {
            $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
        } else {

            $authUrl = $client->createAuthUrl();
            printf("Open the following link in your browser:\n%s\n", $authUrl);
            print 'Enter verification code: ';
            $authCode = trim(fgets(STDIN));

            $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
            $client->setAccessToken($accessToken);


            if (array_key_exists('error', $accessToken)) {
                throw new Exception(join(', ', $accessToken));
            }
        }

        if (!file_exists(dirname($tokenPath))) {
            mkdir(dirname($tokenPath), 0700, true);
        }
        // file_put_contents($tokenPath, json_encode($client->getAccessToken()));
    }
    return $client;
}

$client = getClient();
$service = new Google_Service_Sheets($client);

$spreadsheetId = '1xqXZ0cAT-Ghj7lRh9efLmBIVx0OBLMIXvzslEhjzXEA';
$range = 'datos!A2:D';
$response = $service->spreadsheets_values->get($spreadsheetId, $range);
$values = $response->getValues();

if (empty($values)) {
    print "No data found.\n";
} else {
    ?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css"/>

        <meta name="viewport" content="width=device-width, initial-scale=1.0">
     

        <title>Document</title>
    </head>
    <body>
<h1>contenido de google excel</h1>
    <table id="excel" class="display" style="width:50%" >
                <thead>
                    <tr>
                        <th>nombres</th>
                        <th>apellidos</th>
                        <th>edad</th>
                        <th>cargo</th>
                    </tr>
                </thead>
                
                <tbody>
                <?php   foreach ($values as $row) {
    // printf("%s, %s, %s, %s\n", $row[0], $row[1], $row[2], $row[3]);

    
            $nombres = $row[0];            
            $apellidos = $row[1];
            $edad = $row[2];
            $cargo = $row[3];?> 
            
            <tr>
                <td><?php echo $nombres?></th>
                <td><?php echo $apellidos?></th>
                <td><?php echo $edad ?></th>
                <td><?php echo $cargo ?></th>

                
        </tr>
            <?php }
            ?>
                
            
        </tbody>
        </table>  



        <script type="text/javascript" src="https://code.jquery.com/jquery-3.5.1.js"></script>
        <script type="text/javascript" src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>

        <script src="./tabla.js"></script>

       
        </body>
         
        </html>

<?php

}




// IMPORTANTE PARA LEER 
// EL SIGUIENTE LINK ESTA EL INSTRUCTIVO PARA CONEXION Y CODIDO 
// https://developers.google.com/sheets/api/quickstart/php
// LINK VIDEO PARA INSTRUCCIONES https://www.youtube.com/watch?v=CI0xL93Xtpg