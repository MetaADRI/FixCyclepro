<?php
$s = @stream_socket_server('tcp://127.0.0.1:8000', $e, $m);
echo "8000: " . var_export((bool) $s, true) . " err=" . $m . "\n";
$s2 = @stream_socket_server('tcp://0.0.0.0:8123', $e2, $m2);
echo "8123: " . var_export((bool) $s2, true) . " err=" . $m2 . "\n";
if ($s) fclose($s);
if ($s2) fclose($s2);
