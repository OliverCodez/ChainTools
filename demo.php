<?php
// IMPORTANT
// Define your VCT installation URL/IP and your Access Code (generated during install) in the following variables
$url = 'location/or/url/of/vct/main';
$d = array(
    'a' => 'accesscodefrominstall',
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

$raw_d = json_decode( vg_go( $url, $d ), true );
if ( $d['o'] != null ) {
    $opt_d = json_decode( $raw_d['result'], true);
    if ( isset( $opt_d[$d['o']] ) ) {
        $opt_d = $d['c'] . '/' . $d['m'] . ' - ' . $d['o'] . ': ' . $opt_d[$d['o']];
    }
    else {
        $opt_d = null;
    }
}
if ( $opt_d == null ) { $opt_d = $d['c'] . ' - Hash or Opt not set!'; }

$raw_r = $raw_d['result'];


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
                <p>Using the form below you can interact with the Verus test blockchain, VRSCTEST, using commands you'd normally use from the CLI (command line interface) wallet. The purpose of this demo is primarily to help developers familiarize with how to pass commands and params to the Verus blockchain and see what sort of results/return data occurs.  It's also to help anyone who is interested in learning more about the CLI wallet learn what commands are possible and what they do.</p>
                <p></p>
                <p>To use the form below, type a valid CLI command into the Exec field and the blockchain ticker in the chain field.  For now VRSCTEST is the only daemon running on this demo server.  Next, enter any parameters into the simple and multi-line json param fields, as expected by the command you are testing. Some commands take no params, in which case you'll leave the param fields blank.  Lastly, some commands will return a lot of data in json format.  You may want to single out one part of the data, like in the case of the command "getinfo" you may want to just return the block height...so in the option field you'd put the word blocks. You'll notice in json returns there's a data type name/title and the value.  That title is called a "key" in array terms, and the value is just known as the value.  So you can single out a "key" in options and see it's value returned in the top green area, along with info about the chain and the command run.</p>
                <p></p>
                <p><strong>Some notes: </strong><br><br><br>
                <ul>
                <li>For the exec field, capitalization doesn't matter, everything is stripped and made lowercase before hitting the daemon.</li>
                <li>For the complex params field, quotations are never required and will break the function for now.</li>
                <li>If the param is "" as is the case with many wallet commands for transparent addresses, you'll put -- (dash dash) into the Simple params field instead, which will convert to "" in the function.</li>
                <li>Besides the CLI commands, I've created some helper commands.  They are:<br>bal - Displays the balance of each address in the wallet<br>test - Simply tests connectivity<br>lconf (requires Simple param of the address and Option of the number of confirms to compare against) - Compares the lowest confirm transaction of the given address with the number supplied in Option.<br>tcount and zcount - Return the count of addresses (t or z)<br>recby (requires Simple param to be the address) - Returns total received at the address supplied.
                </ul></p>
                <p></p>
                <h4>Examples</h4>
                <p><strong>To display t addresses...</strong><br><br>
                Exec field: getaddressesbyaccount<br>
                Chain field: VRSCTEST<br>
                Simple param: --<br>
                Complex param: blank<br>
                Option: blank<br>
                </p>
                <p><strong>To turn staking on...</strong><br><br>
                Exec field: setgenerate<br>
                Chain field: VRSCTEST<br>
                Simple param: true,0<br>
                Complex param: blank<br>
                Option: blank
                </p>
                <p><strong>To check staking status...</strong><br><br>
                Exec field: getgenerate<br>
                Chain field: VRSCTEST<br>
                Simple param: blank<br>
                Complex param: blank<br>
                Option: staking
                </p>
                <p><strong>To get a new sapling address...</strong><br><br>
                Exec field: z_getnewaddress<br>
                Chain field: VRSCTEST<br>
                Simple param: sapling<br>
                Complex param: blank<br>
                Option: blank
                </p>
                <p><strong>To get a new transparent address...</strong><br><br>
                Exec field: getnewaddress<br>
                Chain field: VRSCTEST<br>
                Simple param: blank<br>
                Complex param: blank<br>
                Option: blank
                </p>
                <p><strong>To send 100 VRSCTEST from z_address to t_address...</strong><br><br>
                Exec field: z_sendmany<br>
                Chain field: VRSCTEST<br>
                Simple param: z_address (from addr)<br>
                Complex param: address:t_address,amount:100<br>
                Option: blank
                </p>
                <p></p>
                <p>Results are shown in the cells above the form, just below these instructions.  If you are confused hit me up on Discord!  Enjoy :)</p>
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
