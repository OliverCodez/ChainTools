<?php
/**
 * VerusChainTools
 * 
 * Description: A toolkit for interacting with Verus and Verus PBaaS blockchains, 
 * allowing websites to access the daemon RPC via PHP for a more secure and 
 * flexible integration. VerusChainTools works with VerusCoin, PBaaS by Verus 
 * chains, Komodo and Komodo asset chains, and any Verus, Komodo, Zcash, or 
 * Bitcoin fork with minimal adaptation.
 * 
 * Included files:
 *      index.php (this file)
 *      verusclass.php
 *      install.php (temporary installer)
 *      demo.php
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
 */
// TODO: Remove following before production
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Check if first run / install
if ( file_exists( 'install.php' ) ) {
    if ( ! empty( $_POST['save'] ) ) {
        // Create the config file and remove the install script
        file_put_contents( 'config.php','<?php $vct_config = \''.serialize($_POST).'\'; ?>' );
        unlink('install.php');
        die();
    }
    else {
        // If first run, do install
        include_once( 'install.php' );
        die();
    }
}

// Primary daemon server location (usually on same server, localhost)
$vct_url = 'localhost';
include_once( 'verusclass.php' );
include_once( 'config.php' );
$vct_config = unserialize($vct_config);
// This unserialized variable works as follows:
//
// access code is found at: $vct_config['code']
// added chains are in an array under: $vct_config['chain']
// payout addresses are given the key leading with lowercase chain name, followed by payout type: e.g. $vct_config['vrsc_z'] is VerusCoin Z address
// unsupported or unentered payout types are just blank entries, tools utilizing this Api should ignore empty payout values

/**
 * 
 * Get php input in json format for handling api calls
 * 
 * */
if ( ! empty( $_GET['test'] ) ) {
    echo 'reachable';
}
$vct_data = json_decode( file_get_contents( 'php://input' ), true);
if ( $vct_data['code'] != $vct_config['code'] ) {
    die( 'access_code_err' ); // Die if no access code
}
if ( empty( $vct_data['chain'] ) ) {
    die( 'chain_missing_err' );
}
// TODO: Function to check for chain on local wallet and return result (error out if non-exist or down)
//
//
if ( empty( $vct_data['exec'] ) ) {
    die( 'exec_missing_err' );
}

// Build data array for functions with posted chain data
// TODO: Create more reliable method of finding installed chains: use config after install and if not search and update config if found
// TODO: add function to allow api call to signal a new chain is being instantiated
$vct_chain = strtoupper( $vct_data['chain'] );
if ( $vct_chain == 'PBAAS' ) { // If parent pbaas chain, set director (only necessary if parent has unique location from pbaas chains, specific testing, etc)
    $vct_dir = '/home/user/.komodo/VRSCTEST'; // temporary method
    $vct_name = 'VRSCTEST';
}
else {
    $vct_dir = trim( shell_exec( 'find /opt -type d -name "'.$vct_chain.'"' ) );
    $vct_name = $vct_chain;
}
$vct_data = array_merge( $vct_data, array(
    'vct_proto' => 'http',
    'vct_url' => $vct_url,
    'vct_dir' => $vct_dir,
    'vct_user' => trim( substr( shell_exec( 'cat ' . $vct_dir . '/' . $vct_name . '.conf | grep "rpcuser="' ), strlen( 'rpcuser=' ) ) ),
    'vct_pass' => trim( substr( shell_exec( 'cat ' . $vct_dir . '/' . $vct_name . '.conf | grep "rpcpassword="' ), strlen( 'rpcpassword=' ) ) ),
    'vct_port' => trim( substr( shell_exec( 'cat ' . $vct_dir . '/' . $vct_name . '.conf | grep "rpcport="' ), strlen( 'rpcport=' ) ) ),
) );
/**
 *  Execute the function (data points: code, chain, exec, hash, opt)
 */
echo json_encode( array( 'exec' => $vct_data['exec'], 'result' => verigate_go( $vct_data ) ), true );

/**
 *  Primary data and exec function
 */
function verigate_go( $vct_data ) {
    $verus = new rpcVerus( $vct_data['vct_user'], $vct_data['vct_pass'], $vct_data['vct_url'], $vct_data['vct_port'], $vct_data['vct_proto'] );
    switch ( $vct_data['exec'] ) {
        case 'test':
            $verus->status();
            return json_encode( $verus->vct_stat, true );
            break;
        case 'lconf': // return lowest confirm tx
            if ( ! isset( $vct_data['hash'] ) | ! isset( $vct_data['opt'] ) ) {
                return json_encode( "Error 2 - Hash Function", true );
            }
            else if ( substr($vct_data['hash'], 0, 2) === 'zs' ) {
                $result = $verus->z_listreceivedbyaddress( $vct_data['hash'], (int)$vct_data['opt'] );
                $amounts = array();
                foreach ( $result as $item ) {
                    array_push( $amounts, $item['amount'] );
                }
            return json_encode( array_sum( $amounts ), true );
            }
            else {
                return json_encode( $verus->getreceivedbyaddress( $vct_data['hash'], (int)$vct_data['opt'] ), true );
            }
        break;
        case 'tcount': // return count of all t addresses
            return json_encode( count( $verus->getaddressesbyaccount( "" ) ), true );
            break;
        case 'zcount': // return count of all z addresses
            return json_encode( count( $verus->z_listaddresses() ), true );
            break;
        case 'recby': // return total received by balance
            if ( ! isset( $vct_data['hash'] ) ) {
                return json_encode( "Error 2 - Hash", true );
            }
            else {
                return json_encode( $verus->getreceivedbyaddress( $vct_data['hash'] ), true );
            }
            break;
        default: // TODO : Allow for passing of any data to the daemon via web portal, and format (format not working currently)
            $exec = $vct_data['exec'];
            $hash = $vct_data['hash'];
            $opt = $vct_data['opt'];
            
            // TODO: Testing area
            if ( $opt == 'test' ) {
                return $hash;
                die();
            }
            // end Testing area
            if ( isset( $hash ) ) {
                if ( $hash == 'default' ) {
                    $return = $verus->$exec( "" );
                }
                else {
                    $return = $verus->$exec( $hash );
                }
            }
            else {
                $return = $verus->$exec();
            }
            if ( is_array( $return ) ) {
                return vct_format( $return );
            }
            else {
                return $return;
            }
            break;
    }

}

function vct_format( $info_ret ) {
    foreach ( $info_ret as $key => $value ) {
        if ( is_bool( $value ) ) {
            $info_ret[$key] = ($value) ? 'true' : 'false';
        }
        else if ( $value == '0' ) {
            $info_ret[$key] = '0';
        }
        else if ( is_float( $value) ) {
            $info_ret[$key] = sprintf('%.8f',floatval($value));
        }
        else if ( is_integer( $value ) ) {
            $info_ret[$key] = (string)$value;
        }
    }
    return json_encode( $info_ret, true );
}