<?php
// IMPORTANT
// Define your VCT+installation URL/IP and your Access Code (generated during install) in the following variables
$url = 'URL-orIP-to_VCT';
$d = array(
    'a' => 'accesscodeGeneratedDuringInstall',
);
/**
 * Demo file for VerusChainTools+
 * 
 * Description: This demo file is meant for both testing VerusChainTools+ in a new installation environment
 * as well as learning the functions and how to access them using the external website which will be used
 * in a use-case/endpoint for working with the VCT+Api.
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
 * @package  VerusChainTools+
 * @author   Oliver Westbrook 
 * @copyright Copyright (c) 2019, John Oliver Westbrook
 * @link     https://github.com/joliverwestbrook/VerusChainTools
 * @version 0.6.0
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
    curl_setopt( $ch, CURLOPT_USERAGENT, 'VerusChainTools+' );
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
    <link rel="stylesheet" href="css/demo-styles.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
    <title>VerusChainTools+ Demo-er..er</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
</head>
<body>
    <header>
        <div>Demo VCT+</div>
    </header>
    <main>
	<h3 class="link_title"><a href="#demo">Jump to Form</a></h3>
        <div class="collapsible">
            <h2 class="collapsible_button">Instructions (click to expand)</h2>
            <div class="content_top collapsible_inner">
                <div>
                    <p>Thank you for installing VerusChainTools+ and contributing by doing some demos and testing things out.</p>
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
        </div>
        <div class="collapsible">
	        <h2 class="collapsible_button">Original Curl Api Call:</h2>
            <div class="collapsible_inner">
                <div class="return_area">
                    <?php echo json_encode( $d );?>
                </div>
            </div>
        </div>
        <div class="collapsible">
            <h2 class="collapsible_button">Narrowed Return:</h2>
            <div class="collapsible_inner">
                <div class="return_area" style="background: #aeffaf;">
                    <?php echo $opt_d;?>
                </div>
            </div>
        </div>
        <div class="collapsible active">
            <h2 class="collapsible_button" id="raw">Raw Return:</h2>
            <div class="collapsible_inner">
                <div class="return_area">
                    <?php echo $raw_r;?>
                </div>
            </div>
        </div>
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
    <!-- Login Form -->
    
    <!-- End of Form -->
    <script>
    jQuery( document ).ready( function( $ ) {
        // On Load
        var elactive = $( '.active' ).find( '.collapsible_inner' );
        var loadHeight = elactive.children( ':first' ).outerHeight();
        elactive.animate( { height:loadHeight } );
        // On Click
        $( '.collapsible' ).on( 'click touchstart', function() {
            var el = $( this );
            var elin = el.find( '.collapsible_inner' );
            el.toggleClass( 'active' );
            if ( el.hasClass( 'active' ) ) {
                var newHeight = elin.children( ':first' ).outerHeight();
    	        elin.animate( { height:newHeight } );
            }
            else {
                elin.animate( { height:'0' } );
            }
        });
    });
    </script>
</body>
</html>