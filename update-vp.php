<?php
if ( ! defined( 'VCTAccess' ) ) {
    die( 'Direct access denied' );
}
/**
 * VerusChainTools VerusPay-integrated Updater
 * 
 * Description: This file is the VerusPay integrated updater for VerusChainTools
 * 
 * Included files:
 *      index.php
 *      verusclass.php
 *      lang.php
 *      update.php
 *      update-vp.php (this file)
 *      install.php
 *      demo.php
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
 */
?>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
    <title>VerusChainTools Installer</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <style>
        main {
            font-family: inherit;
            font-size: 1.6rem;
        }
        .content_top {
            display: block;
            float: none;
            position: relative;
        }
        .code_block-outer {
            padding-right:5px;
            display: block;
            position: relative;
            float: none;
        }
        .code_block-outer > p:first-child {
            font-weight: bold;
            font-size: 1.8rem;
            text-align: center;
            display: block;
            float: none;
            margin: 0 auto;
            width: 100%;
            padding: 5px 0;
        }
        .code_block-inner {
            display: block;
            float: none;
            margin: 0 auto;
            width: 100%;
            padding: 5px 0;
            height: 60px;
        }
        #copy_code {
            height: 50px;
            max-width: 40px;
            display: block;
            width: 10%;
            float: left;
        }
        .copy_symbol {
            border: 1px #545454 solid;
            border-radius: 10px;
            display: block;
            height: 40px;
            line-height: 38px;
            float: left;
            max-width: 30px;
        }
        .copy_symbol:after {
            content: "copy";
            position: relative;
            display: block;
            height: 40px;
            width: 30px;
            border: 1px solid #545454;
            border-radius: 10px;
            left: 5px;
            top: 5px;
            background: #fff;
            font-size: 10px;
            font-weight: bold;
            text-align: center;
            transition: all .5s ease;
        }
        .copy_symbol:hover {
            cursor:pointer;
            color: #FB5656;
        }
        #access_code-container {
            width: 90%;
            display: block;
            line-height: 50px;
            height: 50px;
            float: left;
            padding: 0 0 0 10px;
            overflow-x: scroll;
            overflow-y: hidden;
            border: 1px dotted #FB5656;
            margin: 0 10px;
        }
        #access_code {
            text-align: center;
            color: #FB5656;
            font-weight: bold;
            font-size: 1.6rem;
        }
        #success_div {
            display:none;
            top: 0;
            left: 0;
            position: absolute;
            height: 100%;
            width: 100%;
            line-height: 130px;
            text-align: center;
            background: #fff;
            font-weight: bold;
             color: #FB5656;
            font-size: 2rem;
        }
        #config {
            display: block;
            width: 100%;
        }
        .easytitle {
            font-size: 2rem;
            color: #3f79a2;
            display: inline-block;
            margin: 30px 0;
            width:100%;
        }
        .chain_del {
            display: inline-block;
            padding: 5px;
            border-radius: 10px;
            background: #ff0000b0;
            width: 140px;
            color: #fff;
            text-align: center;
            margin: 0 10px;
            cursor: pointer;
            height: 36px;
            float: right;
        }
        .addr_block {
            padding: 5px;
            border: solid 1px #cfcfcf;
            display: block;
            opacity: 1;
            height:auto;
            float: none;
            width: 100%;
            margin: 10px auto;
            margin-bottom: 30px;
            font-weight:bold;
            transition:all 0.5s ease;
        }
        .addr_text {
            width: 100%;
            margin: 5px 0;
            background: #f8f8f8;
            border: 1px solid #68afff;
            border-radius: 10px;
            padding: 4px;
            height: 40px;
            font-size: 2rem;
            padding-left: 10px;
        }
        .dropdown {
            border: 1px solid #68afff;
            height: 40px;
            border-radius: 10px;
            margin: 0 10px;
        }
        .box {
            width: 20px;
            height: 20px;
            margin: 10px;
            bottom: -3px;
            display: inline-block;
            position: relative;
        }
        .add_chain_container {
            display: block;
            float: none;
            margin: 20px auto;
            height: 60px;
            width: 230px;
        }
        #chain_name {
            display: block;
            float: left;
            width: 60px;
            height: 52px;
            background: #f8f8f8;
            padding: 4px;
            border: 1px dotted #f9cb03;
            font-size: 16px;
        }
        #add_new {
            background: #ffffff;
            border: 1px dotted #f9cb03;
            margin: 10px auto;
            width: 170px;
            height:52px;
            padding: 10px;
            display: block;
            float: none;
            text-align:right;
            line-height: 30px;
            transition:all 0.5s ease;
        }
        #add_new:hover {
            background: #545454;color:#ffffff;
            cursor:pointer;
        }
        .submit_container {
            margin: 30px auto;
            border-top: solid 1px #545454;
            padding-top: 40px;
        }
        .submit_button {
            display: block;
            float: none;
            width: 160px;
            background: #FB5656;
            border: 1px solid #FB5656;
            padding: 5px;
            color: #fff;
            font-weight: bold;
            margin: 5px auto;
            height: 45px;
            border-radius: 15px;
            font-size: 24px;
            text-transform: uppercase;
            line-height: 40px;
            transition: all 0.5s ease;
        }
        .submit_button:hover {
            background:#ffffff;
            color:#FB5656;
        }
        .addr_block_template {
            height:0;
            opacity:0;
            overflow:hidden;
        }
         footer {
            border-top: 1px solid #545454;
        }
        @media (max-width:767px) {
            main {
                font-size: 1.4rem;
            }
            #copy_code {
                min-width: 40px;
                width: calc(100% * 1/8);
            }
            #access_code-container {
                width: calc(100% * 6/8);
            }
        }
    </style>

</head>
<body>
    <main>
        <div class="code_block-outer">
            
            <form id="config" name="config" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                <input type="hidden" name="code" value="<?php echo $_GET['code']; ?>">
                <input type="hidden" name="S" value="u">
                <input type="hidden" name="update" value="2">
                <?php
                    foreach ( $c['C'] as $key => $value ) {
                        $sel = array(
                            0 => '',
                            1 => '',
                            2 => '',
                        );
                        $adr = array(

                        );
                        $sel[$c['C'][$key]['TX']] = 'selected';
                        
                        if ( $sel['0'] == 'selected' ) {
                            $addresses = '<input id="'.$key.'_t" class="addr_text taddr" placeholder="Transparent Payout Address (leave empty if unsupported or not desired)" type="text" value="'.$c['C'][$key]['T'].'" name="'.$key.'_t"><input id="'.$key.'_z" class="addr_text zaddr" placeholder="Private (Sapling) Payout Address (leave empty if unsupported or not desired)" type="text" value="'.$c['C'][$key]['Z'].'" name="'.$key.'_z">';
                        }
                        if ( $sel['1'] == 'selected' ) {
                            $addresses = '<input id="'.$key.'_t" class="addr_text taddr" placeholder="Transparent Payout Address (leave empty if unsupported or not desired)" type="text" value="'.$c['C'][$key]['T'].'" name="'.$key.'_t"><input id="'.$key.'_z" class="addr_text zaddr" placeholder="Private (Sapling) Payout Address (leave empty if unsupported or not desired)" type="text" value="" name="" style="display:none;">';
                        }
                        if ( $sel['2'] == 'selected' ) {
                            $addresses = '<input id="'.$key.'_t" class="addr_text taddr" placeholder="Transparent Payout Address (leave empty if unsupported or not desired)" type="text" value="" name="" style="display:none;"><input id="'.$key.'_z" class="addr_text zaddr" placeholder="Private (Sapling) Payout Address (leave empty if unsupported or not desired)" type="text" value="'.$c['C'][$key]['Z'].'" name="'.$key.'_z">';
                        }
                        $gs = '';
                        $gm = '';
                        if ( isset( $c['C'][$key]['GS'] ) && $c['C'][$key]['GS'] == '1' ) {
                            $gs = 'checked';
                        }
                        if ( isset( $c['C'][$key]['GM'] ) && $c['C'][$key]['GM'] == '1' ) {
                            $gm = 'checked';
                        }
                        echo '<div class="addr_block '.$key.'_container"><span class="easytitle"><span class="addr">'.$key.'</span> Chain Settings<span class="chain_del" data-chain="'.$key.'">delete chain</span></span><input class="addr_text friendly" type="text" name="'.$key.'_name" value="'.$c['C'][$key]['FN'].'" placeholder="Friendly name e.g. Verus"><label class="dropdown_label" style="display: block;font-weight:normal;"> TX Capabilities:<select class="dropdown chain_capabilities" data-chain="'.$key.'" name="'.$key.'_txtype" style="min-width: 300px;"><option value="0" '.$sel['0'].'>Transparent and Private</option><option value="1" '.$sel['1'].'>Transparent Only</option><option value="2" '.$sel['2'].'>Private zs Only</option></select></label><span class="easytitle">Enable Mining/Staking?</span><div class="boxes"><p><input class="box gs" type="checkbox" name="'.$key.'_gs" value="1" '.$gs.'><label>Enable Staking (if supported)</label></p><p><input class="box gm" type="checkbox" name="'.$key.'_gm" value="1" '.$gm.'><label>Enable Mining (if supported)</label></p></div><span class="easytitle">Payout Addresses</span><input class="addr_name" type="hidden" value="'.$key.'" name="c[]">'.$addresses.'</div>';
                    }
                ?>
                <div id="addr_block_location"></div>
                <p style="font-weight: bold;font-size: 2.2rem;text-align: center;display: block;float: none;margin: 0 auto;width: 100%;padding: 5px 0;margin-top: 20px;">Add New Chains:</p>
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
    <script>
    jQuery( function( $ ) {
        $('select[name="m"]').change(function(){
            if($(this).val() == "_lt_"){
                $('#vct_limits').val('').attr('name','f').fadeIn();
            }
            if($(this).val() == "_bg_"){
                $('#vct_limits').val('').attr('name','').fadeOut();
            }
            if($(this).val() == "_vp_"){
                $('#vct_limits').fadeOut().val('setgenerate,getgenerate,getnewaddress,z_getnewaddress,z_getbalance,getunconfirmedbalance,getaddressesbyaccount,z_listaddresses,getreceivedbyaddress').attr('name','f');
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

        $('#add_new').on('click touchstart', function(){
            var newAddr = $('.addr_block_template').clone();
            var chn = $('#chain_name').val().toLowerCase();
                $(newAddr).addClass(chn+'_container');
                $(newAddr).insertBefore('#addr_block_location');
                $(newAddr).children('.easytitle').children('.addr').text(chn.toUpperCase());
                $(newAddr).children('.easytitle').children('.chain_del').data('chain', chn);
                $(newAddr).children('.friendly').attr('name',chn+'_name');
                $(newAddr).children('.chaindir').attr('name',chn+'_dir');
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