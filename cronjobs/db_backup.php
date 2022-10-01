<?php
    if (file_exists("uf_db_backup.sql")) {
      unlink('uf_db_backup.sql');
    }

    /**
     * @var string $servername The Hostname of MySQL Server
     * @var string $username The Username of MySQL Server
     * @var string $password The Password of MySQL Server
     * @var string $dbname The DB Name of MySQL Server
     * @var mysqli $conn The DB Connection Object
     */
    require_once '../dashboard/api/settings.php';

    $backup_name = "uf_db_backup.sql";
    $tables = array("accounts", "ads", "affiliates", "announcements", "backup", "callbacks", "clients", "conversions", "errors", "payouts", "suspensions");

    Export_Database($conn, $tables=false, $backup_name=false);

    function Export_Database($conn, $tables=false, $backup_name=false) {
        $conn->query("SET NAMES 'utf8'");

        $queryTables = $conn->query('SHOW TABLES');
        while($row = $queryTables->fetch_row()) {
            $target_tables[] = $row[0];
        }
        if($tables !== false) {
            $target_tables = array_intersect($target_tables, $tables);
        }
        foreach($target_tables as $table) {
            $result = $conn->query('SELECT * FROM '.$table);
            $fields_amount = $result->field_count;
            $rows_num = $conn->affected_rows;
            $res = $conn->query('SHOW CREATE TABLE '.$table);
            $TableMLine = $res->fetch_row();
            $content = (!isset($content) ?  '' : $content) . "\n\n".$TableMLine[1].";\n\n";

            for ($i = 0, $st_counter = 0; $i < $fields_amount; $i++, $st_counter=0) {
                while($row = $result->fetch_row()) { //when started (and every after 100 command cycle):
                    if ($st_counter%100 == 0 || $st_counter == 0 ) {
                            $content .= "\nINSERT INTO ".$table." VALUES";
                    }
                    $content .= "\n(";
                    for($j=0; $j<$fields_amount; $j++) {
                        $row[$j] = str_replace("\n","\\n", addslashes($row[$j]) );
                        if (isset($row[$j])) {
                            $content .= '"'.$row[$j].'"' ;
                        } else {
                            $content .= '""';
                        }
                        if ($j<($fields_amount-1)) {
                            $content.= ',';
                        }
                    }
                    $content .=")";
                    //every after 100 command cycle [or at last line] ....p.s. but should be inserted 1 cycle eariler
                    if ((($st_counter+1)%100==0 && $st_counter!=0) || $st_counter+1==$rows_num) {
                        $content .= ";";
                    } else {
                        $content .= ",";
                    }
                    $st_counter=$st_counter+1;
                }
            }
            $content .="\n\n\n";
        }
        //$backup_name = $backup_name ? $backup_name : $name."___(".date('H-i-s')."_".date('d-m-Y').")__rand".rand(1,11111111).".sql";
        //$backup_name = $backup_name ? $backup_name : $name.".sql";

        $backup_name = "../backup/uf_db_backup.sql";
        file_put_contents($backup_name, $content);

        uploadToDrive($conn, $backup_name);
    }

    function uploadToDrive($conn, $name) {
        //Make object of Google API Client for call Google API
        $google_client = new Google_Client();
        //Set the OAuth 2.0 Client ID
        $google_client->setClientId('***REMOVED***');
        //Set the OAuth 2.0 Client Secret key
        $google_client->setClientSecret('***REMOVED***');
        //Set redirect to self
        $redirect = filter_var('https://app.ultifreehosting.com' . $_SERVER['PHP_SELF'],
            FILTER_SANITIZE_URL);
        $google_client->setRedirectUri($redirect);
        // Set the scope to the Drive API
        $google_client->setScopes(array('https://www.googleapis.com/auth/drive'));
        $google_client->setAccessType("offline");

//        Uncomment This Stuff If Access Token Not Working (Also Uncomment Below)
//        if(isset($_GET["code"])) {
//            $google_client->revokeToken();
//            // Attempt to exchange code for a valid token
//            $token = $google_client->fetchAccessTokenWithAuthCode($_GET["code"]);
//            $refresh = $google_client->getRefreshToken();
//
//            echo "Refresh Token: " . $refresh;
//
//            // Save New Refresh Token to DB
//            $saveTokenSQL = "UPDATE `backup` SET `refreshToken` = '$refresh', `accessToken` = '$token' WHERE `id` = 1";
//            $conn->query($saveTokenSQL);

            // Get Current Token + FileId from DB
            $getDriveInfoSQL = "SELECT `refreshToken`, `file` FROM `backup` WHERE `id` = 1";
            try {
                $getDriveInfoResult = $conn->query($getDriveInfoSQL)->fetch_assoc();

                $refreshToken = $getDriveInfoResult['refreshToken'];
                $fileId = $getDriveInfoResult['file'];

                // Update Refresh Token
                $google_client->refreshToken($refreshToken);
                $newToken = $google_client->getAccessToken()['access_token'];

                // Save New Token to DB
                $saveTokenSQL = "UPDATE `backup` SET `accessToken` = '$newToken' WHERE `id` = 1";
                $conn->query($saveTokenSQL);

                // Set the access token used for requests
                $google_client->setAccessToken($newToken);
                $service = new Google_Service_Drive($google_client);

                $content = file_get_contents($name);
                $mimeType='application/octet-stream';

                try {
                    // Empty to avoid fieldNotWritable error
                    $fileMetadata = new Google_Service_Drive_DriveFile(array());

                    $additionalParams = array(
                        'data' => $content,
                        'mimeType' => $mimeType
                    );

                    // Send the request to the API.
                    $updatedFile = $service->files->update($fileId, $fileMetadata, $additionalParams);
                } catch (Exception $e) {
                    // If not found -> Create new file
                    // Create the file on your Google Drive
                    $fileMetadata = new Google_Service_Drive_DriveFile(array(
                        'name' => 'uf_db_backup.sql',
                        //Set the Parent Folder
                        'parents' => array('1laAeE6kmtc07mfY7Atia4B8D8dNOc5pM') // this is the folder id
                    ));
                    $file = $service->files->create($fileMetadata, array(
                        'data' => $content,
                        'mimeType' => $mimeType,
                        'fields' => 'id'));

                    $updateFileIdSQL = "UPDATE `backup` SET `file` = '$file->id' WHERE `id` = 1";
                    $conn->query($updateFileIdSQL);
                }
            } catch (Exception $e) {
                echo $e . " <br> ";
                echo $conn->error;
            }
//        } else {
//            $authUrl = $google_client->createAuthUrl();
//            header('Location: ' . $authUrl);
//            exit();
//        }
    }