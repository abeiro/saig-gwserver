<?php 

// Fake Close conection asap

ignore_user_abort(true);
set_time_limit(1200);
header('Content-Encoding: none');
header('Content-Length: ' . ob_get_length());
header('Connection: close');
echo "\n";

@ob_end_flush();
@ob_flush();
@flush();

// We now have now almost 1200 seconds to do whaterver thing.
sleep(10);
syslog(LOG_WARNING,__FILE__." end execution: ".print_r($_POST,true));


?>
