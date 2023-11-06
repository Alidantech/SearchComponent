<?php
function checkConfigFile($configFile, $envFile){
      if (filesize($configFile) === 0) {
            if (file_exists($envFile) && filesize($envFile) > 0) {
                  $envContent = file_get_contents($envFile);
                  $envVariables = parse_ini_string($envContent, false, INI_SCANNER_RAW);

                  // Create an associative array mapping .env keys to new PHP variable names
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

            } 
      }
}
?>

