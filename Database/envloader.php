<?php

class EnvLoader {
    public static function loadEnv($path) {
        $file = fopen($path, 'r');
        while (!feof($file)) {
            $line = trim(fgets($file));
            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                $_ENV[$key] = $value;
            }
        }
        fclose($file);
    }
}

?>
