<?php
// IMPORTANT
// Define your VCT installation URL/IP and your Access Code (generated during install) in the following variables
$url = 'localhost/path/to/vct/script';
$d = array(
    'a' => 'accesscodeGeneratedDuringInstall',
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
 *      lang.php
 *      update.php
 *      update-vp.php
 *      install.php (temporary installer)
 *      demo.php (this file)
 *
 * @category Cryptocurrency
 * @package  VerusChainTools
 * @author   Oliver Westbrook 
 * @copyright Copyright (c) 2019, John Oliver Westbrook
 * @link     https://github.com/joliverwestbrook/VerusChainTools
 * @version 0.5.2
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

$_mth = null;
$_chn = null;
$_par = null;
$_opt = null;
$raw_d = null;
$raw_r = null;
$opt_d = null;

// Handle request
if ( !empty( $_POST['method'] ) ) {
    $_mth = $_POST['method'];
    $_chn = $_POST['chain'];
    $_par = $_POST['params'];
    $_opt = $_POST['option'];
}
else {
    $opt_d = 'Hmmph. Nothing to do :(';
}
    // GET chain defaults to VRSCTEST in this sample, but can be changed to another default
if ( empty( $_chn ) ) { $_chn = 'VRSCTEST'; } else { $_chn = strtoupper( $_chn ); }
if ( empty( $_par ) ) { $_par = null; } 
if ( empty( $_opt ) ) { $_opt = null; }
$d = array_merge( $d, array(
    'c' => $_chn,
    'm'  => $_mth,
    'p'  => $_par,
    'o'   => $_opt,
    ) 
);
$raw_d = json_decode( vg_go( $url, $d ), TRUE );
if ( $d['o'] != null ) {
    $opt_d = json_decode( $raw_d, true);
    if ( isset( $opt_d[$d['o']] ) ) {
        $opt_d = $d['c'] . '/' . $d['m'] . ' - ' . $d['o'] . ': ' . $opt_d[$d['o']];
    }
    else {
        $opt_d = null;
    }
}
if ( $opt_d == null ) { $opt_d = $d['c'] . ' - Params or Opt not set!'; }
$raw_r = str_replace( '\"', '"', json_encode( $raw_d, TRUE ) ); // Using str_replace for "friendly" viewing of output, in normal funtion you'd call the array 'result' or 'return'

function vg_go( $url, $d ) {
    $ch = curl_init();
    $d = json_encode( $d );
    curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
    curl_setopt( $ch, CURLOPT_URL, $url );
    curl_setopt( $ch, CURLOPT_USERAGENT, 'VerusChainTools' );
    curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Accept: application/json') );
    curl_setopt( $ch, CURLOPT_POST, 1);
    curl_setopt( $ch, CURLOPT_POSTFIELDS, $d );
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
            padding: 20px 10px 10px 10px;
        }
        h2 {
            display: block;
            font-size: 2.4rem;
            padding: 20px 40px 0 40px;
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
            padding: 20px 20px 0 20px;
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
            margin: 10px auto;
	    margin-bottom:10px
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
	.link_title {
	    display:block;
	    float:none;
	    width:220px;
	    margin: 5px auto;
	    text-align:center;
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
            //border-top: 1px solid #545454;
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
	<h3 class="link_title"><a href="#demo">Jump to Form</a></h3>
        <h3 id="instructions_button">Instructions (click to expand)</h3>
        <div class="content_top" id="instructions">
            <div class="instructions_inner">
                <p>Thank you for installing VerusChainTools and contributing by doing some demos and testing things out.</p>
                <p>Please let me know if you run into errors or unexpected behaviors! I'm always improving the script and the entire community benefits when people provide feedback.</p> 
                <p>Using the form below you can interact with the Verus test blockchain, VRSCTEST, using commands you'd normally use from the CLI (command line interface) wallet. Don't know CLI?  Don't worry! Part of the purpose of this demo is to help developers and anyone else curious become familiar and skilled in the "dark arts" of CLI! :)</p>
                <p></p>
                <p>To use the form below, begin by simply entering a valid "method" and leave the Chain default at VRSCTEST.  If you know the correct Params for the Method you are running, enter it and click Go.  If you don't know the right Params, it will return telling you an example in the exact format you would enter it.</p>
                <p></p>
                <p>Go ahead! Try it out!</p>
                <p></p>
                <p>Results are shown in the cells above the form, just below these instructions.  The Raw Return can be narrowed down by entering one of the returned value titles in the Options field in the form. If you are confused hit me up on Discord!  Enjoy :)</p>
            </div>
        </div>
	<h2>Original Curl Api Call:</h2>
        <div class="return_area"><?php echo json_encode( $d );?></div>
        <h2>Narrowed Return:</h2>
        <div class="return_area" style="background: #aeffaf;"><?php echo $opt_d;?></div>
	<h2 id="raw">Raw Return:</h2>
        <div class="return_area"><?php echo $raw_r;?></div>
        <div class="form_block-outer">
            <form id="demo" name="demo" action="" method="POST">
                <input type="text" name="method" value='<?php echo $_mth; ?>' placeholder="Method (the command)">
                <input type="text" name="chain" value='<?php echo $_chn; ?>' placeholder="Chain (e.g. VRSCTEST)">
                <input type="text" name="params" value='<?php echo $_par; ?>' placeholder="Params JSON">
                <input type="text" name="option" value='<?php echo $_opt; ?>' placeholder="Option">
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
