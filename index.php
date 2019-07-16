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
        file_put_contents( 'config.php','<?php $c = \''.serialize($_POST).'\'; ?>' );
        unlink('install.php');
        die();
    }
    else {
        // If first run, do install
        include_once( 'install.php' );
        die();
    }
}
// Check for deprecated version (support ends Sep 1, 2019)
if ( isset( $_POST['access'] ) ) {
    $access_pass = $_POST['access'];
    include_once( 'deprecated-index.php' );
}
else {
    /**
    * 
    * Get php input in json format for handling api calls
    * 
    * */
    // Main data getter
    $i = json_decode( file_get_contents( 'php://input' ), TRUE);
    if ( empty( $i ) ) {
	    die('Nothing to do');
    }
    // Primary default daemon server location (usually on same server, localhost)
    $_url = 'localhost';
    include_once( 'verusclass.php' );
    include_once( 'config.php' );
    $c = unserialize($c);
    // This unserialized variable works as follows:
    //
    // access code is found at: $c['acc']
    // added chains are in an array under: $c['chn']
    // payout addresses are given the key leading with lowercase chain name, followed by payout type: e.g. $c['vrsc_z'] is VerusCoin Z address
    // unsupported or unentered payout types are just blank entries, tools utilizing this Api should ignore empty payout values
    
    // Simple test function for admins
    if ( ! empty( $_GET['test'] ) ) {
        echo 'reachable';
    }
    if ( $i['acc'] != $c['acc'] ) {
        die( 'err_access_code' ); // Die if no access code
    }
    if ( empty( $i['chn'] ) ) {
        die( 'err_chain_missing' );
    }
    // TODO: Function to check for chain on local wallet and return result (error out if non-exist or down)
    //
    //
    if ( empty( $i['exc'] ) ) {
        die( 'err_command_missing' );
    }

    // Build data array for functions with posted chain data
    // TODO: Create more reliable method of finding installed chains: use config after install and if not search and update config if found
    // TODO: add function to allow api call to signal a new chain is being instantiated
    $_chn = strtoupper( $i['chn'] );
    if ( $_chn == 'VRSCTEST' ) { // If parent pbaas chain, set director (only necessary if parent has unique location from pbaas chains, specific testing, etc)
        $_dir = '/home/user/.komodo/VRSCTEST'; // temporary method
    }
    else {
        $_dir = trim( shell_exec( 'find /opt -type d -name "'.$_chn.'"' ) );
    }
    $i['exc'] = vct_clean( $i['exc'] );
    $i = array_merge( $i, array(
        'pro' => 'http',
        'url' => $_url,
        'dir' => $_dir,
        'usr' => trim( substr( shell_exec( 'cat ' . $_dir . '/' . $_chn . '.conf | grep "rpcuser="' ), strlen( 'rpcuser=' ) ) ),
        'pas' => trim( substr( shell_exec( 'cat ' . $_dir . '/' . $_chn . '.conf | grep "rpcpassword="' ), strlen( 'rpcpassword=' ) ) ),
        'prt' => trim( substr( shell_exec( 'cat ' . $_dir . '/' . $_chn . '.conf | grep "rpcport="' ), strlen( 'rpcport=' ) ) ),
    ) );
    /**
    *  Execute the function (data points: code, chain, exc, par, opt)
    */
    echo json_encode( array( 'command' => $i['exc'], 'result' => _go( $i ) ), TRUE );
}

/**
 *  Primary data and exec function
 */
function _go( $d ) {
    $verus = new Verus( $d['usr'], $d['pas'], $d['url'], $d['prt'], $d['pro'] );
    $e = $d['exc'];
    $p = $d['par'];
    $o = $d['opt'];
    switch ( $e ) {
        /**
         * Tests
         * 
         * For testing status of daemon(s)
         * 
         */
        case 'test':
            $verus->status();
	        if ( $verus->sts === 404 ) {
                return vct_custom_return( 'status', 'online' );
	        }
	        else {
                return vct_custom_return( 'status', 'offline' );
	        }
            break;
        /**
         * VerusPay
         * 
         * Custom section with cases built for use with VerusPay
         * 
         *  */
        case 'lconf': // return lowest confirm tx
            if ( !isset( $p ) ) {
                return vct_custom_return( 'error', '100' );
            }
            else if ( substr( $p, 0, 2 ) === 'zs' ) {
                $r = $verus->z_listreceivedbyaddress( $p );
                $a = array();
                foreach ( $r as $v ) {
                    array_push( $a, $v['amount'] );
                }
            return json_encode( array_sum( $a ), TRUE );
            }
            else {
		        return json_encode( $verus->getreceivedbyaddress( $p ), TRUE );
            }
            break;
        case 'tcount': // return count of all t addresses
            if ( !isset( $p ) ) {
                return vct_custom_return( 'error', '100' );
            }
            else {
                return json_encode( count( $verus->getaddressesbyaccount( $p ) ), TRUE );
            }
            break;
        case 'zcount': // return count of all z addresses
            return json_encode( count( $verus->z_listaddresses() ), TRUE );
            break;
        case 'recby': // return total received by balance
            if ( !isset( $p ) ) {
                return vct_custom_return( 'error', '100' );
            }
            else {
                return json_encode( $verus->getreceivedbyaddress( $p ), TRUE );
            }
            break;
        case 'bal': // Iterate throught all addresses provided and display balance of each
            if ( !isset( $p ) ) {
                return vct_custom_return( 'error', '100' );
            }
            else {
                $t = $verus->getaddressesbyaccount( $p );
                $z = $verus->z_listaddresses();
                if ( json_encode( $z, TRUE ) == 'false' && json_encode( $t, TRUE) == 'false' ) {
                    return null;
                }
                else {
                    $tb = array();
                    $ttb = array();
                    $zb = array();
                    $ztb = array();
                    $b = array();
                    $bt = array();
                    if ( json_encode( $t, TRUE ) != 'false' ) {
                        foreach ( $t as $v ) {
                            $tb[$v] = $verus->z_getbalance( json_encode( $v, TRUE ) );
                        }
                        foreach ( $tb as $v ) {
                            $ttb[] = $v;
                        }
                        $ttb = array(
                            'total_t_balance' => array_sum( $ttb )
                        );
                    }
                    if ( json_encode( $z, TRUE ) != 'false' ) {
                        foreach ( $z as $v ) {
                            $zb[$v] = $verus->z_getbalance( json_encode( $v, TRUE ) );
                        }
                        foreach ( $zb as $v ) {
                            $ztb[] = $v;
                        }
                        $ztb = array(
                            'total_z_balance' => array_sum( $ztb )
                        );
                    }
                    $bt = array(
                        'total_balance' => array_sum( array( $ttb['total_t_balance'], $ztb['total_z_balance'] ) )
                    );
                    $b = array_merge( $tb, $zb, $ttb, $ztb, $bt );
                    $r = $b;
                    if ( is_array( $r ) ) {
                        return vct_format( $r );
                    }
                    else {
                        return $r;
                    }
                }
            }
            break;
        /**
         * Default
         * 
         * Pass any/all unfiltered requests through to the daemon
         * 
         */
        default: // TODO : Setup filter for optional usage of some commands
            // Filter specific commands here
            //vct_filter( $e );
            if ( isset( $p ) ) {
                $r = $verus->$e( $p );
            }
            else {
                $r = $verus->$e();
            }
            if ( is_array( $r ) ) {
                return vct_format( $r );
            }
            else {
                return $r;
            }
            break;
    }
}

function vct_format( $d ) {
    foreach ( $d as $k => $v ) {
        if ( $v == '0' ) {
            $d[$k] = '0';
        }
	    else if ( is_bool( $v ) ) {
            $d[$k] = ( $v ) ? 'true' : 'false';
        }
        else if ( is_float( $v) ) {
            $d[$k] = sprintf( '%.8f', floatval( $v ) );
        }
        else if ( is_integer( $v ) ) {
            $d[$k] = (string)$v;
        }
    }
    return json_encode( $d, TRUE );
}

function vct_clean( $d ) {
    $d = trim( htmlentities( strip_tags( $d ) ) );
    if ( get_magic_quotes_gpc() ) {
        $d = stripslashes( $d );
    }
    $d = strtolower( $d );
    return $d;
}

function vct_custom_return( $t, $d ) {
    if ( $t === 'error' ) {
        switch ( $d ) {
            case '100':
                $r = array( $t => $d, 'message' => 'Param missing or malformed' );
                break;
            case '101':
                $r = array( $t => $d, 'message' => 'Unknown' );
        }
    }
    else {
        $r = array( $t => $d );
    }
    return json_encode( $r, TRUE );
}
