<?php
$dir = dirname(__FILE__) . '/sb/courses/-1/';
rrmdir($dir);
function rrmdir($dir) {
    foreach(glob($dir . '/*') as $file) {
        if(is_dir($file))
            rrmdir($file);
        else
            unlink($file);
    }
    rmdir($dir);
}

?>
