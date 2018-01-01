<?php

$mapping = json_decode(file_get_contents('./tests/.travis-keys.json'), true);
$ip = file_get_contents('https://api.ipify.org');
$sh = file_get_contents('.travis.env.sh');

foreach ($mapping as $range => $key) {
    if (ip_in_range($ip, $range)){
        $sh = str_replace('<SECURED_KEY>', $key, $sh);
    }
}

file_put_contents('.travis.env.sh', $sh);

function ip_in_range( $ip, $range ) {
    if ( strpos( $range, '/' ) == false ) {
        $range .= '/32';
    }
    // $range is in IP/CIDR format eg 127.0.0.1/24
    list( $range, $netmask ) = explode( '/', $range, 2 );
    $range_decimal = ip2long( $range );
    $ip_decimal = ip2long( $ip );
    $wildcard_decimal = pow( 2, ( 32 - $netmask ) ) - 1;
    $netmask_decimal = ~ $wildcard_decimal;
    return ( ( $ip_decimal & $netmask_decimal ) == ( $range_decimal & $netmask_decimal ) );
}

