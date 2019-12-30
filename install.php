<?php
if ( ! defined( 'VCTAccess' ) ) {
    die( 'Direct access denied' );
}
/**
 * VerusChainTools+ Installer
 * 
 * Description: This file is a one-time installer for a first-use of VerusChainTools+. This file is removed after a successful install.
 * 
 * Included files:
 *      index.php
 *      verusclass.php
 *      lang.php
 *      update.php
 *      update-vp.php
 *      install.php (this file)
 *      demo.php
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
 */
$_version = '6';
// Check for AJAX call for adding coins
if ( isset( $_POST[ 'whatdaemon' ] ) ) {
    $whatdaemon = strtolower( $_POST[ 'whatdaemon' ] );
    $r = isDaemon( $whatdaemon );
    exit( $r );
}
// This function will be called in an ajax on adding each new coin/daemon in the install or update process
function isDaemon( $whatdaemon ) {
    // TODO: This to array; confirm duplicate, if not error for user to correct; feed into config after confirmed; use ajax after each coin add
    $_daemonArray = array();
    $_daemonArray = preg_split( '/\s+/', trim( shell_exec( 'for i in $(pgrep "' . $whatdaemon . '"); do readlink -f /proc/"$i"/exe; done' ) ) );
    if ( empty( $_daemonArray ) ) {
        $r = json_encode( array( 'error' => '<p><span style="color:red;font-weight:bold">' . $whatdaemon . '</span> daemon not found to be running on this server! If you misspelled the daemon name or have not started the daemon, please correct the issue and refresh this page to try again. If you believe there is a problem with this install script, please submit an issue on GitHub at <a href="https://github.com/joliverwestbrook/VerusChainTools/issues">https://github.com/joliverwestbrook/VerusChainTools/issues</a></p><form id="config" name="config" action="' . $url_self . '" method="POST"><div class="submit_container"><input class="submit_button" type="submit" value="Try Again"></div></form>' ) );
    }
    else {
        check if array elements are same, if not alert and offer choice for the "right" one, otherwise just put the right one
        run command "getinfo" and get "name" value for coin
        $r = json_encode( array( 'folder' => 'value', 'name' => 'value' ) );
    }
    return $r;
}
$url_self = htmlspecialchars($_SERVER["PHP_SELF"]);
$accessCode = 'A' . $_version . '_' . rand_chars( 'xgSYiMmVPlvoj8fQO7TUp06WyBGAdz1EeknFtKqaJrHDsRZh94LbX2NIc5wuC3', 69, TRUE );
$updateCode = 'U' . $_version . '_' . rand_chars( 'xgSYiMmVPlvoj8fQO7TUp06WyBGAdz1EeknFtKqaJrHDsRZh94LbX2NIc5wuC3', 69, TRUE );
function rand_chars( $c, $l, $u = FALSE ) {
    if ( ! $u ) {
        for ( $s = '', $i = 0, $z = strlen( $c ) - 1; $i < $l; $x = rand( 0, $z ), $s .= $c{ $x }, $i++ );
    }
    else {
        for ( $i = 0, $z = strlen( $c ) - 1, $s = $c{ rand( 0, $z ) }, $i = 1; $i != $l; $x = rand( 0, $z ), $s .= $c{ $x }, $s = ( $s{ $i } == $s{ $i - 1 } ? substr( $s, 0, -1 ) : $s ), $i = strlen( $s ) );
    }
    return $s;
}
?>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/config-styles.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
    <title>VerusChainTools+ Installer</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
</head>
<body>
    <header>
        <div>Welcome to the VerusChainTools+ Installer</div>
    </header>
    <main>
        <div class="content_top">
            <p>Thank you for installing VerusChainTools+. Below is your unique Access Code and Update Code. Keep your Update Code in a secure location for future use, if you ever need to add more blockchain daemons or update any of your config settings. Your Access Code is for use with either VerusPay or the web tool you are using with the VerusChainTools+ API. Please verify the first number of your Access Code matches the first non-zero version number of this VerusChainTools+ API which you're installing.  For example, "4" would match with VerusChainTools+ version 0.4.2.  If it does not match, abandon this install and contact the developer.</p>
            <p></p>
            <p></p>
            <p>After adding your chains and any payout addresses (for VerusPay users), click Save and your config file will be created locally on this server and this installation script will be removed.  If you need to change something or update these settings in the future, visit this same script and append the following to the URL: ?code=YOUR_PRIVATE_UPDATE_CODE ( e.g. https://127.127.27.27/?code=YourPrivateUpdateCode )</p>
            <p></p>
        </div>
        <div class="code_block-outer">
            <p>Your Unique Acces Code:</p>
            <div class="code_block-inner">
                <div id="copy_code">
                    <span class="copy_symbol"></span>
                </div>
                <div id="access_code-container">
                    <p id="access_code"><?php echo $accessCode; ?></p>
                    <p id="success_div" style="z-index:999">Access Code Copied</p>
                </div>
            </div>
            <p>Your Private Update Code:</p>
            <div class="code_block-inner">
                <div id="copy_ucode">
                    <span class="copy_symbol"></span>
                </div>
                <div id="update_code-container">
                    <p id="update_code"><?php echo $updateCode; ?></p>
                    <p id="usuccess_div">Update Code Copied</p>
                </div>
            </div>
            <form id="config" name="config" action="<?php echo $url_self; ?>" method="POST">
                <input type="hidden" name="S" value="s">
                <input type="hidden" name="D" value="<?php echo date( 'Y-m-d H:i:s', time() ); ?>">
                <input type="hidden" name="A" value="<?php echo $accessCode; ?>">
                <input type="hidden" name="U" value="<?php echo $updateCode; ?>">
                <div class="main_container" style="display: block;float: left;width: 100%;padding: 0 0 20px 0;margin: 10px auto;">
    <p style="font-weight: bold;font-size: 2.2rem;text-align: center;display: block;float: none;margin: 0 auto;width: 100%;padding: 5px 0;margin-top: 20px;">Choose VerusChainTools+ Mode and Language:</p>
    <span style="font-size: 16px;padding: 0 0 20px;display: block;">Choose how you intend to use VerusChainTools+ and your preferred language.</span>
                    <select name="m" id="vct_mode" style="display: block;width: 100%;margin: 10px 40px;max-width: 300px;">
                        <option value="_vp_" selected="">VerusPay WordPress Mode</option>
                        <option value="_bg_">Full Bridge Mode</option>
                        <option value="_lt_">Limited Mode</option>
                    </select>
                    <input name="f" id="vct_limits" placeholder="Enter allowed/whitelisted valid method names seperated by a comma" style="display: none;width: 80%;margin: 10px 40px;" value="getinfo,setgenerate,getgenerate,getnewaddress,z_getnewaddress,z_getbalance,getunconfirmedbalance,getaddressesbyaccount,z_listaddresses,getreceivedbyaddress,definechain,getchaindefinition,getdefinedchains">
                    <select name="l" id="vct_lang" style="display: block;margin: 10px 40px;width: 300px;">
                        <option value="eng" selected="">English</option>
                    </select>
                </div>
                <div id="addr_block_location"></div>
                <p style="font-weight: bold;font-size: 2.2rem;text-align: center;display: block;float: none;margin: 0 auto;width: 100%;padding: 5px 0;margin-top: 20px;">Add Your Chains:</p>
                <span style="font-size: 16px;padding: 0 0 20px;display: block;">Add the chains/coins by entering the chain symbol and clicking the Add Chain button.  Add chains this wallet server will access, one at a time and enter the appropriate Payout address (if desired/compatible) for each chain added.  Only add chains for which you have the daemon installed and running on this wallet server.</span>
                <div class="add_chain_container">
                    <input id="chain_name" name="" type="text" placeholder="VRSC" value="">
                    <span id="add_new">+ Add Chain</span>
                </div>
                <div class="submit_container">
                    <input class="submit_button" type="submit" value="Save">
                </div>
            </form>
        </div>
    </main>
    <footer>
    </footer>
    <script>
    jQuery( document ).ready( function( $ ) {
        $('select[name="m"]').change(function(){
            if($(this).val() == "_lt_"){
                $('#vct_limits').val('').attr('name','f').fadeIn();
            }
            if($(this).val() == "_bg_"){
                $('#vct_limits').val('').attr('name','').fadeOut();
            }
            if($(this).val() == "_vp_"){
                $('#vct_limits').fadeOut().val('getinfo,setgenerate,getgenerate,getnewaddress,z_getnewaddress,z_getbalance,getunconfirmedbalance,getaddressesbyaccount,z_listaddresses,getreceivedbyaddress,definechain,getchaindefinition,getdefinedchains').attr('name','f');
            }
        });
        $(document).on('change', '.chain_capabilities', function(){
            var chn = $(this).attr('data-chain');
            if($(this).val() == "0"){
                $('#'+chn+'_t').fadeIn().attr('name',chn+'_t');
                $('#'+chn+'_z').fadeIn().attr('name',chn+'_z');
            }
            if($(this).val() == "1"){
                $('#'+chn+'_t').fadeIn().attr('name',chn+'_t');
                $('#'+chn+'_z').fadeOut().attr('name','');
            }
            if($(this).val() == "2"){
                $('#'+chn+'_t').fadeOut().attr('name','');
                $('#'+chn+'_z').fadeIn().attr('name',chn+'_z');
            }
        });
        $(document).on('click touchstart', '.chain_del', function(){
            var chn = $(this).data('chain');
            $('.'+chn+'_container').remove();
        });
        $('#copy_code').on('click touchstart', function(){
            var $temp = $("<input>");
            var $addr = $('#access_code').text();
            $("body").append($temp);
            $temp.val($('#access_code').text()).select();
            document.execCommand('copy');
            $temp.remove();
            $('#success_div').fadeIn('slow', function () {
                $(this).delay(1000).fadeOut('slow');
            });
        });
        $('#copy_ucode').on('click touchstart', function(){
            var $temp = $("<input>");
            var $addr = $('#update_code').text();
            $("body").append($temp);
            $temp.val($('#update_code').text()).select();
            document.execCommand('copy');
            $temp.remove();
            $('#usuccess_div').fadeIn('slow', function () {
                $(this).delay(1000).fadeOut('slow');
            });
        });

        $('#add_new').on('click touchstart', function(){
            var newAddr = $('.addr_block_template').clone();
            var chn = $('#chain_name').val().toLowerCase();
            if ( empty( chn ) ) {
                alert( 'You must enter the name of the daemon for the coin you are adding' );
                return;
            }
            else {
                // TODO: Fix up this ajax to interact with the isDaemon function on every new attempt at adding a coin (on click / touchstart of #add_new) - disallow adding coins if the daemon is not found running - enforces clean install, clean and accurate config, update/upgrade ready for future support
                $.ajax( {
                    method: 'POST',
                    url: window.location.pathname,
                    whatdaemon: 1
                } ).success( function( result ) {
                    // TODO:
                    //on return, 
                    //var chn = getinfo->name return
                    //var dir = found directory of daemon
                } ).error( function( result ) {
                    //TODO: create error function
                });
            }
                $(newAddr).addClass(chn+'_container');
                $(newAddr).insertBefore('#addr_block_location');
                $(newAddr).children('.easytitle').children('.addr').text(chn.toUpperCase());
                $(newAddr).children('.easytitle').children('.chain_del').data('chain', chn);
                $(newAddr).children('.friendly').attr('name',chn+'_name');
                    // TODO: FIX for populating value from var dir --> $(newAddr).children('.chaindir').attr('name',chn+'_dir');
                $(newAddr).children('.boxes').children('p').children('.gs').attr('name',chn+'_gs');
                $(newAddr).children('.boxes').children('p').children('.gm').attr('name',chn+'_gm');
                $(newAddr).children('.taddr').attr('name',chn+'_t').attr('id',chn+'_t');
                $(newAddr).children('.zaddr').attr('name',chn+'_z').attr('id',chn+'_z');
                $(newAddr).children('.dropdown_label').children('.dropdown').attr('name',chn+'_txtype');
                $(newAddr).children('.dropdown_label').children('.dropdown').attr('data-chain',chn);
                $(newAddr).children('.addr_name').val(chn);
                $(newAddr).removeClass('addr_block_template');
                $('#chain_name').val('');
        });

        $( '#chain_name' ).keypress(function (e) {
            var key = e.which;
            if ( key == 13 ) {
                $( '#add_new' ).click();
                return false;
            }
        });
    });
    </script>
<div class="addr_block_template addr_block">
    <span class="easytitle">
        <span class="addr"></span> Chain Settings<span class="chain_del" data-chain="">delete chain</span>
    </span>
    <input class="addr_text friendly" type="text" name="" value="" placeholder="Friendly name e.g. Verus">
    <input class="addr_text chaindir" type="text" name="" value="" placeholder="Enter the folder location, i.e. /home/user/.komodo/VRSC">
    <label class="dropdown_label" style="display: block;font-weight:normal;"> TX Capabilities:
        <select class="dropdown chain_capabilities" data-chain="" name="" style="min-width: 300px;">
            <option value="0">Transparent and Private</option>
            <option value="1">Transparent Only</option>
            <option value="2">Private zs Only</option>
        </select>
    </label>
    <span class="easytitle">Enable Mining/Staking?</span>
    <div class="boxes">
        <p><input class="box gs" type="checkbox" name="" value="1"><label>Enable Staking (if supported)</label></p>
        <p><input class="box gm" type="checkbox" name="" value="1"><label>Enable Mining (if supported)</label></p>
    </div>
    <span class="easytitle">Payout Addresses</span>
    <input class="addr_name" type="hidden" value="" name="c[]">
    <input class="addr_text taddr" placeholder="Transparent Payout Address (leave empty if unsupported or not desired)" type="text" value="" name="">
    <input class="addr_text zaddr" placeholder="Private (Sapling) Payout Address (leave empty if unsupported or not desired)" type="text" value="" name="">
</div>
</body>
</html>