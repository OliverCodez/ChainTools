<?php
// IMPORTANT
// Define your VCT installation URL/IP and your Access Code (generated during install) in the following variables
$url = 'localhost/veruschaintools/index.php';
$data = array(
    'code' => 'v040EV4MCOYhoFNYfs9UJncvfVfc3r8Z8qFxvuLgbDEJyOTYrsv3ATwfI1lx8j2PbB6bL9T4',
);

/**
 * Demo file for VerusChainTools
 * 
 * Description: This demo file is meant for both testing VerusChainTools in a new installation environment
 * as well as learning the functions and how to access them using the external website which will be used
 * in a use-case/endpoint for working with the VCT Api.
 * 
 * Included files:
 *      index.php
 *      verusclass.php
 *      install.php (temporary installer)
 *      demo.php (this file)
 *
 * @category Cryptocurrency
 * @package  VerusChainTools
 * @author   Oliver Westbrook <johnwestbrook@pm.me>
 * @copyright Copyright (c) 2019, John Oliver Westbrook
 * @link     https://github.com/joliverwestbrook/VerusChainTools
 * @version 0.4.0-beta
 * 
 * ====================
 * 
 * The MIT License (MIT)
 * 
 * Copyright (c) 2019 John Oliver Westbrook
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 * 
 * ====================
 * 
 */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$hash_array = null;
$opt_get = null;
$chain_get = null;
$raw_data = null;
$raw_result = null;
$opt_data = null;

if ( !empty( $_GET['exec'] ) ) {
    //
    // GET Hash for testing simple calls only (non-complex/json - in 
    // your code you should always pass hash to the data arraay as an array
    // of your resultant hash options.
    // 
    // For example, to pass a command like 'setgenerate' you need two options,
    // true/false and the number of threads.  You would pass hash into the call
    // as an array of ["false","0"] for example.
    // 
    // To do something like z_sendmany you would have an array of your json formatted
    // data, like so: array("address"=>"theaddress","amount"=>100)
    //
    // This will result in the VCT function as being converted to the appropriate 
    // option set array or json formatted option set.
    // 
    // Here we have the get function for hash, but to test fully you should
    // build your test array as $hash_array.
    if ( !empty( $_GET['hash'] ) ) { $hash_array = explode(',',$_GET['hash']); }
    //
    // GET Opt is used for various options
    if ( !empty( $_GET['opt'] ) ) {
        $opt_get = $_GET['opt'];
        if ( $_GET['opt'] == 'zsendtest' ) {
            $hash_array = array(
                "zs1vzj3r59cumts5gmyx8tw543zf8qwhe05lu5p89juyfq8w58c9mtnt2r7nu4rx2at8qyvzlj8kg0",
                array(
                    array(
                        "address" => "RQkGsTA3ANtZDS4Pt1UPA2UntDV3RUp1yq",
                        "amount" => 100,
                    )
                )
            );
        }
    }
    //
    // GET chain defaults to PBAAS in this sample, but can be changed to another default
    if ( empty( $_GET['chain'] ) ) { $chain_get = 'PBAAS'; } else { $chain_get = strtoupper( $_GET['chain'] ); }
    $data = array_merge( $data, array(
        'chain' => $chain_get,
        'exec'  => $_GET['exec'],
        'hash'  => $hash_array,
        'opt'   => $opt_get,
    ) );
    $raw_data = json_decode( vg_go( $url, $data ), true );

    if ( $data['opt'] != null ) {
        $opt_data = json_decode( $raw_data['result'], true);
        if ( isset( $opt_data[$data['opt']] ) ) {
            $opt_data = $data['chain'] . '/' . $data['exec'] . ' - ' . $data['opt'] . ': ' . $opt_data[$data['opt']];
        }
        else {
            $opt_data = null;
        }
    }
    if ( $opt_data == null ) { $opt_data = $data['chain'] . ' - Hash or Opt not set!'; }
    
    $raw_result = $raw_data['result'];
}
else {
    $opt_data = 'Missing Exec Command - Nothing to do!';
}
function vg_go( $url, $data ) {
    $ch = curl_init();
    $data = json_encode( $data );
    curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
    curl_setopt( $ch, CURLOPT_URL, $url );
    curl_setopt( $ch, CURLOPT_USERAGENT, 'VerusChainTools' );
    curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Accept: application/json') );
    curl_setopt( $ch, CURLOPT_POST, 1);
    curl_setopt( $ch, CURLOPT_POSTFIELDS, $data );
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
    $re  = curl_exec( $ch );
    
    if($re === false){
        echo 'curl err: ' . curl_error($ch);
    }
    else{
        return $re;
    }
    curl_close($ch);
}
?>
<html>
<head>
</head>
<body style="display:block;max-width:calc(100% * .9);background: #545454;margin:0 auto;padding:40px;color:#fff">

    <div style="display:block;font-size: 1.6rem;padding: 20px 40px;">Narrowed Return:</div>
    <div style="display: block;position: relative;word-wrap: anywhere;background: #aeffaf;padding: 40px;border-radius: 10px;font-size: 1.4rem;color:#000"><?php echo $opt_data;?></div>
    <div style="display:block;font-size: 1.6rem;padding: 20px 40px;">Raw Return:</div>
    <div style="display:block;position: relative;word-wrap: anywhere;background: whitesmoke;padding: 40px;border-radius: 10px;color:#000"><?php echo $raw_result;?></div>
    <div style="display:block;font-size: 1.6rem;padding: 20px 40px;">Original Curl Api Call:</div>
    <div style="display:block;position: relative;word-wrap: anywhere;background: whitesmoke;padding: 40px;border-radius: 10px;color:#000"><?php echo json_encode( $data );?></div>
</body>
</html>