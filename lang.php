<?php
if ( ! defined( 'VCTAccess' ) ) {
    die( 'Direct access denied' );
}
$lng = array(
    'eng' => array(
        'Unknown Error', // 0
        'Nothing to do! Is direct access allowed? Ensure this script is running behind a firewall and only your front-end or plugin (such as VerusPay) has access.', // 1
        'Err: Access Code Missing. Check config!', // 2
        'Err: Chain Info Missing', // 3
        'Err: Method Missing', // 4
        'success', // 5
        'online', // 6
        'offline', // 7
        'error', // 8
        'Params are missing or incorrect', // 9
        'Method not allowed', // 10
        'No Address Set', // 11
        'daemon not found on server', // 12
        'Chain does not support this type of transaction', // 13
        ' daemon not found on this server - install halted - add daemon and restart install', // 14
        'Params missing or incorrect, e.g. ', // 15
        ' daemon must be running on daemon server', // 16
        'ForCashout_Complete_update_manually_on_VCT_server', // 17
        '<h2><center>Update Successful!</center></h2>', // 18
        'daemon offline', // 19
        'daemon online', // 20
        '<h2 style="color:red"><center>Error</center></h2><p>Cannot Write to Directory - Check Permissions for Web User (usually www-data).  The directory containing VerusChainTools must be owned by your servers web user.  It is recommended you also set permissions 755 on the same folder and all contents.</p><p>Update will now exit.</p>', // 21
        '<h2 style="text-align:center;padding:40px;">Upgrading . . .</h2>', //22
        '<h2 style="text-align:center;padding:40px;display:inline;">Current Version: </h2>', //23
        '<h1 style="text-align:center;padding:30px;display:block;">Upgrade Complete!</h1>', //24
    ),
    // Add more language translations with a 3 character array key following
);
