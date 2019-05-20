<?php
/**
 * Verus Chain Tools
 *
 * @category Cryptocurrency
 * @package  VerusChainTools
 * @author   Oliver Westbrook <johnwestbrook@pm.me>
 * @copyright Copyright (c) 2019, John Oliver Westbrook
 * @link     https://github.com/joliverwestbrook/VerusPHPTools
 * @version 0.2.1
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
$version = '0.2.1';
if ( isset( $_POST['access'] ) ) {
    $access_pass = $_POST['access'];
}
else {
    $access_pass = null;
}
if ( isset( $_POST['ver'] ) ) {
    echo $version;
}
global $installed_wallets;
require_once 'easybitcoin.php';
// Config is created during installation script.
$installed_wallets = ltrim(file_get_contents('veruschaintools_config.php'), '<?php ');
$installed_wallets = unserialize( $installed_wallets );
if ( $installed_wallets['access']['pass'] != $access_pass ) {
    die();
}
if ( isset( $_POST['coin'] ) ){
    $coin = $_POST['coin'];
}
else {
    // Set default coin to Verus Coin VRSC
    $coin = 'vrsc';
}
if ( isset( $_POST['exec'] ) ) {
    $exec = $_POST['exec'];
}
else {
    $exec = null;
}
if ( isset( $_POST['hash'] ) ) {
    $hash = $_POST['hash'];
}
else {
    $hash = null;
}
if ( isset( $_POST['amt'] ) ) {
    $amt = $_POST['amt'];
}
else {
    $amt = null;
}

if ($exec == 'test'){
    echo verus_chain_tools_conn_stat( $coin );
}
else { 
    echo verus_chain_tools_go_verus( $coin, $exec, $hash, $amt ); 
}

/**
 * Test RPC connection - Return status, 0 means not running, anything else is running even if errors occur.
 */
function verus_chain_tools_conn_stat( $coin ) {
    global $installed_wallets;
    // Create new RPC connection to Verus Daemon
    $verus = new Bitcoin( $installed_wallets[$coin]['rpc_user'], $installed_wallets[$coin]['rpc_pass'], 'localhost', $installed_wallets[$coin]['port'] );
    $verus->status();
    return $verus->status;
}
/**
 * Primary data and exec function
 */
function verus_chain_tools_go_verus( $coin, $command, $hash, $amt ) {
    global $installed_wallets;
    $verus = new Bitcoin( $installed_wallets[$coin][ 'rpc_user' ], $installed_wallets[$coin][ 'rpc_pass' ], 'localhost', $installed_wallets[$coin]['port'] );
    // Execute commands availabel for to interact with Verus Daemon
    switch ( $command ) {
        case 'getnewaddress':
            return $verus->getnewaddress();
            break;
        case 'getnewsapling':
            return $verus->z_getnewaddress( sapling );
            break;
        case 'getbalance':
            if ( ! isset( $hash ) ) {
                return "Error 2 - Hash Function";
            }
            else {
                return $verus->z_getbalance( $hash );
            }
        break;
        case 'lowestconfirm':
            if ( ! isset( $hash ) | ! isset( $amt ) ) {
                return "Error 2 - Hash Function";
            }
            else if ( substr($hash, 0, 2) === 'zs' ) {
                $data = $verus->z_listreceivedbyaddress( $hash, (int)$amt );
                $amounts = array();
                foreach ( $data as $item ) {
                    array_push($amounts,$item['amount']);
                }
            return array_sum($amounts);
            }
            else {
                return $verus->getreceivedbyaddress( $hash, (int)$amt );
            }
        break;
        case 'getblockcount':
            return $verus->getblockcount();
        break;
        case 'countaddresses':
            return count( $verus->getaddressesbyaccount( "" ) );
            break;
        case 'countzaddresses':
            return count( $verus->z_listaddresses() );
            break;
        case 'listaddresses':
            $taddrlist = json_encode( $verus->getaddressesbyaccount( "" ), true );
            return $taddrlist;
            break;
        case 'listzaddresses':
            $zaddrlist = json_encode( $verus->z_listaddresses(), true );
            return $zaddrlist;
            break;
        case 'totalreceivedby':
            if ( ! isset( $hash ) ) {
                return "Error 2 - Hash";
            }
            else {
                return $verus->getreceivedbyaddress( $hash );
            }
            break;
        case 'getttotalbalance':
            return $verus->getbalance();
            break;
        case 'getunconfirmedbalance':
            return $verus->getunconfirmedbalance();
            break;
        case 'getztotalbalance':
            $zaddresses = $verus->z_listaddresses();
            if ( json_encode( $zaddresses, true ) == 'false' ) {
                return null;
            }
            else {
                $zbal = array();
                foreach ( $zaddresses as $zaddress ) {
                    $zbal[] = $verus->z_getbalance( $zaddress );
                };
                return array_sum( $zbal );
            }
            break;
        case 'gettotalbalance':
            $tbal = array();
            $tbal[] = $verus->getbalance();
            $zaddresses = $verus->z_listaddresses();
            foreach ( $zaddresses as $zaddress ) {
                $tbal[] = $verus->z_getbalance( $zaddress );
            };
            $tbal = array_sum( $tbal );
            return $tbal;
            break;
        case 'show_taddr':
            // Return the transparent address set
            return $installed_wallets[$coin][ 'taddr' ];
        case 'show_zaddr':
            // Return the private address set
            return $installed_wallets[$coin][ 'zaddr' ];
        case 'cashout_t':
            // Run transparent withdraw command and return the conf
            if ( strtolower($coin) == 'arrr' ) {
                return "Transparent TXs Not Supported";
            }
            else if ( strlen($installed_wallets[$coin][ 'taddr' ]) > 10 ) {
                return $verus->sendtoaddress($installed_wallets[$coin][ 'taddr' ],$verus->getbalance(),"Cashout_" . time() . "","VerusPay",true);
            }
            else {
                return "No Address Set!";
            }
            break;
        case 'cashout_z':
            // Run private withdraw command and return the conf
            if ( strlen($installed_wallets[$coin][ 'zaddr' ]) > 10 ) {
                $zaddresses = $verus->z_listaddresses();
                $results = array();
                foreach ( $zaddresses as $zaddress ) {
                    $zbal = $verus->z_getbalance( $zaddress );
                    $zbal = ($zbal - 0.00010000);
                    if ( $zbal > 0.0000001 ) {
                        $zbal = (float)number_format($zbal,8);
                        $txdata = array(
                            array(
                            'address' => $installed_wallets[$coin][ 'zaddr' ],
                            'amount' => $zbal)
                        );
                        $results[$zaddress] = array(
                            'cashout_address' => $installed_wallets[$coin][ 'zaddr' ],
                            'amount' => $zbal,
                            'opid' => $verus->z_sendmany($zaddress, $txdata),
                        );
                    }
                };
                return json_encode( $results, true );
            }
            else {
                return "No Address Set!";
            }
    }
}