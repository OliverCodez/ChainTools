<?php
// IMPORTANT
// Define your VCT installation URL/IP and your Access Code (generated during install) in the following variables
$url = 'https://IP_or_URL_of_VerusChainTools_Daemon_server_Here';
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

$_exec = null;
$_chain = null;
$_hash1_raw = null;
$_hash2_raw = null;
$_hash = null;
$_opt = null;
$raw_data = null;
$raw_result = null;
$opt_data = null;

if ( !empty( $_GET['exec'] ) || !empty( $_POST['exec'] ) ) {
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
    // For Example:
    //                  $_hash = array(
    //                      "zs1vzj3r59cumts5gmyx8tw543zf8qwhe05lu5p89juyfq8w58c9mtnt2r7nu4rx2at8qyvzlj8kg0",
    //                          array(
    //                              array(
    //                                  "address" => "RQkGsTA3ANtZDS4Pt1UPA2UntDV3RUp1yq",
    //                                  "amount" => 100,
    //                              )
    //                          )
    //                  );
    //
    //
    // This will result in the VCT function as being converted to the appropriate 
    // option set array or json formatted option set.
    //
    // Note on sending "" for hash: In some cases you need to send "" to the daemon to signify the default account.  
    // To do so, instead send -- (dash dash) to the API which will be interpreted as "".
    // 
    // Here we have the get function for hash, but to test fully you should
    // build your test array as $_hash.

    // Handle request
    if ( !empty( $_GET['exec'] ) ) {
        $_exec = $_GET['exec'];
        $_chain = $_GET['chain'];
        if ( !empty( $_GET['hash'] ) ) {
            $_hash = explode(',',$_GET['hash']);
        }
        $_opt = $_GET['opt'];
    }
    else if ( !empty( $_POST['exec'] ) ) {
        $_exec = $_POST['exec'];
        $_chain = $_POST['chain'];
        if ( !empty( $_POST['hash1'] ) ) {
            $_hash1_raw = $_POST['hash1'];
            $_hash1 = explode( ',', $_hash1_raw );
            if ( !empty( $_POST['hash2'] ) ) {
                $_hash2_raw = $_POST['hash2'];
                preg_match_all('/(.*?):\s?(.*?)(,|$)/', $_hash2_raw, $matches);
                $_hash2 = array_combine(array_map('trim', $matches[1]), $matches[2]);
                foreach( $_hash2 as $key => $value ) {
                    if ( $key == 'memo' ) {
                        $_hash2[$key] = bin2hex( $value );
                    }
                }
                $_hash = array_merge(
                    $_hash1,
                    array(
                        array(
                            $_hash2
                        )
                    )
                );
            }
            else {
                $_hash = $_hash1;
            }
        }
        if ( empty( $_POST['hash1'] ) && !empty( $_POST['hash2'] ) ) {
            $_hash2_raw = $_POST['hash2'];
            preg_match_all('/(.*?):\s?(.*?)(,|$)/', $_hash2_raw, $matches);
            $_hash2 = array_combine(array_map('trim', $matches[1]), $matches[2]);
            foreach( $_hash2 as $key => $value ) {
                if ( $key == 'memo' ) {
                    $_hash2[$key] = bin2hex( $value );
                }
            }
            $_hash = array(
                array(
                    $_hash2
                )
                );
        }
        $_opt = $_POST['opt'];
    }

    // GET chain defaults to VRSCTEST in this sample, but can be changed to another default
    if ( empty( $_chain ) ) { $_chain = 'VRSCTEST'; } else { $_chain = strtoupper( $_chain ); }
    if ( empty( $_hash ) ) { $_hash = null; } 
    if ( empty( $_opt ) ) { $_opt = null; }
    $data = array_merge( $data, array(
        'chain' => $_chain,
        'exec'  => $_exec,
        'hash'  => $_hash,
        'opt'   => $_opt,
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
    $opt_data = 'Hmmph. Nothing to do :(';
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
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
    <title>VerusChainTools Demo-er..er</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <style>
        body {
            max-width: 1200px;
            margin: auto;
            padding: 20px 10px;
        }
        h2 {
            display: block;
            font-size: 2.4rem;
            padding: 20px 40px;
        }
        header {
            height: 100px;
            font-family: arial;
            font-size: 4rem;
            line-height: 100px;
            font-weight: bold;
            text-align: center;
            border-bottom: 1px solid #545454;
            padding: 10px;
            margin-bottom: 40px;
        }
        main {
            padding: 20px;
            font-family: arial;
            font-size: 1.4rem;
        }
        .content_top {
            display: block;
            float: none;
            position: relative;
        }
        .return_area {
            display: block;
            position: relative;
            word-wrap: anywhere;
            background: whitesmoke;
            padding: 40px;
            border-radius: 10px;
            font-size:1.6rem;
            color: #000;
        }
        .form_block-outer {
            border-top: solid 10px #545454;
            border-bottom: solid 10px #545454;
            border-radius: 10px;
            border-left: 1px solid #545454;
            border-right: 1px solid #545454;
            margin: 40px auto;
            margin-top:0;
            max-width: 1000px;
            display: block;
            position: relative;
            float: none;
        }
        .form_block-outer > p:first-child {
            font-weight: bold;
            font-size: 2.2rem;
            text-align: center;
            display: block;
            float: none;
            margin: 0 auto;
            width: 100%;
            padding: 5px 0;
        }
        #demo {
            display: block;
            width: 100%;
        }
        .add_chain_container {
            display: block;
            float: none;
            margin: 20px auto;
            height: 60px;
            width: 230px;
        }
        .submit_container {
            margin: 30px auto;
            border-top: solid 1px #545454;
            padding-top: 40px;
        }
        .submit_button {
            display: block;
            float: none;
            width: 180px;
            background: #FB5656;
            border:1px solid #FB5656;
            border-radius:5px;
            padding: 5px;
            color: #fff;
            font-size: 2rem;
            font-weight: bold;
            margin: 5px auto;
            transition:all 0.5s ease;
        }
        .submit_button:hover {
            background:#ffffff;
            color:#FB5656;
        }
         footer {
            border-top: 1px solid #545454;
        }
        #instructions_button {
            cursor:pointer;
            font-size: 2rem;
            text-align: center;
        }
        #instructions {
            height:0;
            overflow: hidden;
            transition: all 0.5s ease;
        }
        .height_auto {
            height:100% !important;
        }
        input[type=text] {
            background: none;
            border: 1px solid #5353;
            border-radius: 10px;
            height: 60px;
            padding: 10px;
            line-height: 40px;
            margin: 10px auto;
            display: block;
            min-width: 260px;
            max-width: 600px;
            width: 100%;
            font-size: 2rem;
        }
        textarea {
            background: none;
            border: 1px solid #5353;
            border-radius: 10px;
            min-height: 200px;
            padding: 10px;
            line-height: 2.5rem;
            margin: 10px auto;
            display: block;
            min-width: 260px;
            max-width: 600px;
            width: 100%;
            font-size: 2rem;
        }
        @media (max-width:767px) {
            header {
                min-height: 100px;
                height: auto;
                font-size: 2.5rem;
                line-height: 3rem;
            }
            main {
                font-size: 1.6rem;
                padding: 0 5px;
            }
            .form_block-outer {
                padding: 20px 4px;
            }
        }
    </style>

</head>
<body>
    <header>
        <div>Welcome to the VerusChainTools Demo-erer!</div>
    </header>
    <main>
        <div class="content_top">
            <p>Thank you for installing VerusChainTools and contributing by doing some demos and testing things out.</p>
            <p>Please let me know if you run into errors or unexpected behaviors! I'm always improving the script and the entire community benefits when people provide feedback.</p>      
        </div>
        <h3 id="instructions_button">Instructions (click to expand)</h3>
        <div class="content_top" id="instructions">
            <div class="instructions_inner">
                <p>Using the form below you can create RPC calls against the daemon of your choice. Simply enter the daemon ticker in the Chain field, and at minimum a command in the Exec field.  If the command has parameters you can enter them in the "simple param(s)" field and then put any json params in the "json params" text area, use a colon to separate param and value, and commas to separate sets of param/values. An example is provided in the form field.</p>
                <p></p>
                <p>Results are shown in the cells above the form, just below these instructions.  If you are confused hit me up on Discord!  Enjoy :)</p>
            </div>
        </div>
        <h2>Narrowed Return:</h2>
        <div class="return_area" style="background: #aeffaf;"><?php echo $opt_data;?></div>
        <h2>Raw Return:</h2>
        <div class="return_area"><?php echo $raw_result;?></div>
        <h2>Original Curl Api Call:</h2>
        <div class="return_area"><?php echo json_encode( $data );?></div>
        <h2>Build a Valid Daemon RPC Command:</h2>
        <div class="form_block-outer">
            <form id="demo" name="demo" action="" method="POST">
                <input type="text" name="exec" value="<?php echo $_exec; ?>" placeholder="Enter the command name here, e.g. getinfo">
                <input type="text" name="chain" value="<?php echo $_chain; ?>" placeholder="Enter the chain (VRSCTEST if blank). For now VRSCTEST is required">
                <p style="font-weight: bold;font-size: 2.2rem;text-align: center;display: block;float: none;margin: 0 auto;width: 100%;padding: 5px 0;margin-top: 20px;">Params and Options:</p>
                <input type="text" name="hash1" value="<?php echo $_hash1_raw; ?>" placeholder="Enter simple param(s) (if required). Substitute -- for &quot;&quot;">
                <textarea name="hash2" value="<?php echo $_hash2_raw; ?>" placeholder="Enter json params (if any) here, if any. Use the following format > paramname:paramvalue,paramname2:value2"></textarea>
                <input type="text" name="opt" value="<?php echo $_opt; ?>" placeholder="Specific returned data you want to see, e.g. blocks">
                <div class="submit_container">
                    <input class="submit_button" type="submit" value="Go!">
                </div>
            </form>
        </div>
    </main>
    <footer>
    </footer>
    <script>

        jQuery('#instructions_button').on('click touchstart', function(){
            jQuery('#instructions').toggleClass('active');
            if ( jQuery('#instructions').hasClass('active') ) {
                var newHeight = jQuery(".instructions_inner").height();
    	        jQuery('#instructions').animate({height:newHeight});
            }
            else {
                jQuery('#instructions').animate({height:'0'});
            }
        });

    </script>
</body>
</html>