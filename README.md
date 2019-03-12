# VerusChainTools
Verus Chain Tools is a PHP toolkit for Zcash compatible blockchain integration, for bridging PHP applications

An install must be performed first like: 


``$installed_wallet_settings = array(
     'vrsc' => array(
         'rpc_user' => 'user1234',
         'rpc_pass' => 'pass1234',
         'port' => '27486',
     ),
     'arrr' => array(
        'rpc_user' => 'userPIRATES',
        'rpc_pass' => 'pass_PIRATES',
        'port' => '45453',
     )
 );
$installed_wallet_settings_serialized = serialize($installed_wallet_settings);
file_put_contents('veruschaintools_config.php', '<?php '.$installed_wallet_settings_serialized);``
