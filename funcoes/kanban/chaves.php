<?php 

$hostsmtp = 'smtp.vetorian.com';
$usersmtp = 'vetorian@vetorian.com';
$passsmtp = 'V3t0r14n!';
$portsmtp = 587;

$smtpoptions = array(
    'ssl' => array(
        'verify_peer' => false,
        'verify_peer_name' => false,
        'allow_self_signed' => true
    )
);

?>