<?php
function checkConfigFile($configFile, $envFile){
      if (filesize($configFile) === 0) {
                        // Define the required environment variable keys
            $requiredKeys = ['DB_HOST', 'DB_USER', 'DB_PASSWORD', 'DB_NAME'];

            if (file_exists($envFile) && filesize($envFile) > 0) {
                  $envContent = file_get_contents($envFile);
                  $envVariables = parse_ini_string($envContent, false, INI_SCANNER_RAW);

                  $errorData = [];

                  // Check if all required keys exist in the $envVariables array
                  foreach ($requiredKeys as $key) {
                        if (!isset($envVariables[$key])) {
                              $errorData[] = "Missing required key: $key";
                        }
                  }

                  if (!empty($errorData)) {
                        // If any required key is missing, return an error in JSON format
                        $response = [
                              'error' => 'Missing required configuration in db.env',
                              'messages' => $errorData
                        ];
                        echo json_encode($response);
                        exit();
                  }
                  
                  // Create the config file with the required variables
                  $variableMap = [
                        'DB_HOST' => 'db_host',
                        'DB_USER' => 'db_user',
                        'DB_PASSWORD' => 'db_password',
                        'DB_NAME' => 'db_name',
                  ];
                  
                  $configContent = '<?php' . PHP_EOL;

                  foreach ($variableMap as $envKey => $phpVar) {
                        if (isset($envVariables[$envKey])) {
                        $value = $envVariables[$envKey];
                        $configContent .= "\$$phpVar = $value;" . PHP_EOL;
                        }
                  }

                  file_put_contents($configFile, $configContent);
            

            }elseif(file_exists($envFile)){
                  $errorData = array(
                        'error' => 'Empty file',
                        'message' => 'the provided db.env file is empty'
                        );
            
                  echo json_encode($errorData);
                  exit();
            }  else {
                  $errorData = array(
                  'error' => 'File not found',
                  'message' => 'no db.env file defined.'
                  );

                  echo json_encode($errorData);
                  exit();
            }
      }
}
?>

