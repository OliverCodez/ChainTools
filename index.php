<?php
// TODO: Remove following before production
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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
 *      lang.php
 *      install.php (temporary installer)
 *      demo.php
 *
 * @category Cryptocurrency
 * @package  VerusChainTools
 * @author   Oliver Westbrook <johnwestbrook@pm.me>
 * @copyright Copyright (c) 2019, John Oliver Westbrook
 * @link     https://github.com/joliverwestbrook/VerusChainTools
 * @version 0.4.0-rc
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

/**
 * First-time Install
 * 
 * Check if first run / install and either run install or process results
 */
if ( file_exists( 'install.php' ) ) {
    if ( ! empty( $_POST['s'] ) ) {
        // Create the config file and remove the install script
        $s_a = $_POST;
        if ( isset( $s_a['c'] ) ) {
            foreach ( $s_a['c'] as $k => $v ) {
                $s_a['c'][$v] = array();
                $s_a['c'][$v]['d'] = date( 'Y-m-d H:i:s', time() );
                $s_a['c'][$v]['tx'] = $s_a[$v.'_txtype'];
                unset( $s_a[$v.'_txtype'] );
                if ( isset( $s_a[$v.'_t'] ) ) {
                    $s_a['c'][$v]['t'] = $s_a[$v.'_t'];
                    unset( $s_a[$v.'_t'] );
                }
                if ( isset( $s_a[$v.'_z'] ) ) {
                    $s_a['c'][$v]['z'] = $s_a[$v.'_z'];
                    unset( $s_a[$v.'_z'] );
                }
                unset( $s_a['c'][$k] );
            }
        }
        $s_a['f'] = explode( ',', $s_a['f'] );
        foreach( $s_a['f'] as $k => $v ) {
            $s_a['f'][$k] = vct_clean( $v );
        }
        file_put_contents( 'config.php','<?php $c = \''.serialize($s_a).'\'; ?>' );
        unlink( 'install.php' );
        die( '<h2><center>Successfully Installed!</center></h2>' );
    }
    else {
        // If first run, do install
        include_once( 'install.php' );
        die();
    }
}
/**
 * Backwards Compatibility
 * 
 * Support for deprecated versions (support ends Sep 1, 2019)
 */
if ( isset( $_POST['access'] ) ) {
    $access_pass = $_POST['access'];
    include_once( 'deprecated-index.php' );
}
/**
 * Main Script
 */
else {
    
    /**
     * Get Data and Setup Vars
     *
     * Get php input in json format for handling api calls
     */
    $_url = 'localhost';
    include_once( 'verusclass.php' );
    include_once( 'config.php' );
    include_once( 'lang.php' );
    // Set config settings to array
    $c = unserialize($c);
    $c['lng'] = $lng[$c['l']];
    if ( !isset( $c['f'] ) ) {
        $c['f'] = array();
    }
    $i = json_decode( file_get_contents( 'php://input' ), TRUE);
    // If no input, die
    if ( empty( $i ) ) {
	    die($c['lng'][1]);
    }
    /**
     * Check Things
     * 
     * Check access code, chain, and method
     */
    // Compare access code provided with set in config
    if ( $i['a'] != $c['a'] ) {
        die( $c['lng'][2] );
    }
    // Check that chain is set
    if ( empty( $i['c'] ) ) {
        die( $c['lng'][3] );
    }
    // TODO: Function to check for chain on local wallet and return result (error out if non-exist or down)
    //
    // Check that method is set
    if ( empty( $i['m'] ) ) {
        die( $c['lng'][4] );
    }

    // Build data array for functions with posted chain data
    // TODO: Create more reliable method of finding installed chains: use config after install and if not search and update config if found
    // TODO: add function to allow api call to signal a new chain is being instantiated
    $_chn = strtoupper( $i['c'] );
    if ( $_chn == 'VRSCTEST' ) { // If parent pbaas chain, set director (only necessary if parent has unique location from pbaas chains, specific testing, etc)
        $_dir = '/home/user/.komodo/VRSCTEST'; // temporary method
    }
    else {
        $_dir = trim( shell_exec( 'find /opt -type d -name "'.$_chn.'"' ) );
    }
    /**
     * Input Array
     * 
     * Finish setting up input data array before processing
     */
    $i['m'] = vct_clean( $i['m'] );
    $i = array_merge( $i, array(
        'pro' => 'http',
        'url' => $_url,
        'dir' => $_dir,
        'usr' => trim( substr( shell_exec( 'cat ' . $_dir . '/' . $_chn . '.conf | grep "rpcuser="' ), strlen( 'rpcuser=' ) ) ),
        'pas' => trim( substr( shell_exec( 'cat ' . $_dir . '/' . $_chn . '.conf | grep "rpcpassword="' ), strlen( 'rpcpassword=' ) ) ),
        'prt' => trim( substr( shell_exec( 'cat ' . $_dir . '/' . $_chn . '.conf | grep "rpcport="' ), strlen( 'rpcport=' ) ) ),
    ) );
    /**
     * Go!
     * 
     * Run the _go function to process the provided method and related data
     */
    echo json_encode( array( 'command' => $i['m'], 'result' => _go( $i ) ), TRUE );
}

/**
 * Go Process Request
 * 
 * Main data processor using verusclass to communicate with compatible RPC daemons
 */
function _go( $d ) {
    // Include config array
    global $c;
    // New Verus class for interacting with daemon
    $verus = new Verus( $d['usr'], $d['pas'], $d['url'], $d['prt'], $d['pro'] );
    $e = $d['m'];
    $p = $d['p'];
    $o = $d['o'];
    switch ( $e ) {

        /**
         * Testing
         * 
         * For testing status of daemon(s)
         */

        case 'test':
            $verus->status();
	        if ( $verus->sts === 404 ) {
                return vct_return_helper( $c['lng'][5], $c['lng'][6] );
                break;
	        }
	        else {
                return vct_return_helper( $c['lng'][5], $c['lng'][7] );
                break;
	        }
            break;

        /**
         * Helpful Tools
         * 
         * Custom section with some helper commands for ease-of-use and integration. Have a suggestion? Create an issue in https://github.com/joliverwestbrook/VerusChainTools/issues
         *  */

        // Return the current daemon version
        case 'version':
            return $verus->getinfo()['version'];
            break;
        // Return the lowest confirm TX
        case 'lowest':
            if ( !isset( $p ) ) {
                return vct_return_helper( 1, NULL );
                break;
            }
            else if ( substr( $p, 0, 2 ) === 'zs' ) {
                $r = $verus->z_listreceivedbyaddress( $p );
                $a = array();
                foreach ( $r as $v ) {
                    array_push( $a, $v['amount'] );
                }
            return json_encode( array_sum( $a ), TRUE );
            break;
            }
            else {
                return json_encode( $verus->getreceivedbyaddress( $p ), TRUE );
                break;
            }
            break;
        // Return a count of all T (transparent) addresses
        case 't_count':
            if ( !isset( $p ) ) {
                return vct_return_helper( 1, NULL );
                break;
            }
            else {
                return json_encode( count( $verus->getaddressesbyaccount( $p ) ), TRUE );
                break;
            }
            break;
        // Return a count of all Z (private) addresses
        case 'z_count':
            return json_encode( count( $verus->z_listaddresses() ), TRUE );
            break;
        // Iterate all T and Z addresses and return balance of each and totals
        case 'bal':
            if ( !isset( $p ) ) {
                return vct_return_helper( 1, NULL );
                break;
            }
            else {
                $t = $verus->getaddressesbyaccount( $p );
                $z = $verus->z_listaddresses();
                if ( json_encode( $z, TRUE ) == 'false' && json_encode( $t, TRUE) == 'false' ) {
                    return null;
                    break;
                }
                else {
                    $tb = array();
                    $zb = array();
                    if ( json_encode( $t, TRUE ) != 'false' ) {
                        foreach ( $t as $v ) {
                            $tb[$v] = $verus->z_getbalance( json_encode( $v, TRUE ) );
                        }
                    }
                    if ( json_encode( $z, TRUE ) != 'false' ) {
                        foreach ( $z as $v ) {
                            $zb[$v] = $verus->z_getbalance( json_encode( $v, TRUE ) );
                        }
                    }
                    $r = array_merge( $tb, $zb, $verus->z_gettotalbalance() );
                    if ( is_array( $r ) ) {
                        return vct_format( $r );
                        break;
                    }
                    else {
                        return $r;
                        break;
                    }
                }
            }
            break;

        /**
         * Main Section
         * 
         * All other methods passed will attempt to pass through the default case
         */

        // Pass all other methods and evaluate for filtering (VerusPay, Limited, or full Bridge)
        default:
            /**
             * VerusPay Mode
             * 
             * Specific to use with VerusPay Plugin with custom whitelist in config file, set at install, and custom methods defined below
             */
            if ( $c['m'] === '_vp_' ) {
                switch ( $e ) {
                    /**
                     * VerusPay-specific Custom Methods
                     *  */
                    // Show the configured T-Cashout address where relevant
                    case 'show_taddr':
                        return $installed_wallets[$coin][ 'taddr' ];
                        break;
                    // Show the configured Z-Cashout address where relevant
                    case 'show_zaddr':
                        return $installed_wallets[$coin][ 'zaddr' ];
                        break;
                    // Perform a cashout to the configured T address where relevant
                    case 'cashout_t':
                        if ( strtolower($coin) == 'arrr' ) { // TODO: config file to track chains and capabilities etc
                            return "Transparent TXs Not Supported"; // Msg that T is not supported - TODO: Based on chain won't even use this.
                            break;
                        }
                        else if ( strlen($installed_wallets[$coin][ 'taddr' ]) > 10 ) {
                            return $verus->sendtoaddress( $installed_wallets[$coin][ 'taddr' ],$verus->getbalance(),"Cashout_" . time() . "","VerusPay",true );
                            break;
                        }
                        else {
                            // If address not set, error
                            return $c['lng'][11];
                            break;
                        }
                        break;
                    // Perform a cashout to the configured Z address where relevant
                    case 'cashout_z':
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
                                            'amount' => $zbal,
                                        )
                                    );
                                    $results[$zaddress] = array(
                                        'cashout_address' => $installed_wallets[$coin][ 'zaddr' ],
                                        'amount' => $zbal,
                                        'opid' => $verus->z_sendmany($zaddress, $txdata),
                                    );
                                }
                            }
                            return json_encode( $results, true );
                            break;
                        }
                        else {
                            // If address not set, error
                            return $c['lng'][11];
                            break;
                        }
                        break;
                    // All other methods, filtered by whitelist preconfigured during install
                    default:
                        // If whitelisted, continue
                        if ( in_array( $e, $c['f'], TRUE ) ) {
                            if ( isset( $p ) ) {
                                $r = $verus->$e( $p );
                            }
                            else {
                                $r = $verus->$e();
                            }
                            if ( is_array( $r ) ) {
                                return vct_format( $r );
                                break;
                            }
                            else {
                                if ( strpos( $r, 'curltest') !== FALSE ) {
                                    $r = strstr( $r, '"params"' );
                                    $r = preg_replace('/"params": /', '', $r);
                                    $r = substr( $r, 0, strpos( $r, "}' -H" ) );
                                    return vct_return_helper( 2, $r );
                                    break;
                                }
                                else {
                                    return $r;
                                    break;
                                }
                            }
                        }
                        else {
                            // If method not whitelisted, error
                            return $c['lng'][10];
                            break;
                        }
                        break;
                }
            }
            /**
             * Limited Mode
             * 
             * Limited access to daemon methods using whitelist configured at install
             */
            else if ( $c['m'] === '_lt_' ) {
                // If whitelisted, continue
                if ( in_array( $e, $c['f'], TRUE ) ) {
                    if ( isset( $p ) ) {
                        $r = $verus->$e( $p );
                    }
                    else {
                        $r = $verus->$e();
                    }
                    if ( is_array( $r ) ) {
                        return vct_format( $r );
                        break;
                    }
                    else {
                        if ( strpos( $r, 'curltest') !== FALSE ) {
                            $r = strstr( $r, '"params"' );
                            $r = preg_replace('/"params": /', '', $r);
                            $r = substr( $r, 0, strpos( $r, "}' -H" ) );
                            return vct_return_helper( 2, $r );
                            break;
                        }
                        else {
                            return $r;
                            break;
                        }
                    }
                }
                else {
                    // If method not whitelisted, error
                    return $c['lng'][10];
                    break;
                }
            }
            /**
             * Bridge Mode
             * 
             * Full access to daemon (no whitelist)
             */
            else if ( $c['m'] === '_bg_' ) {
                if ( isset( $p ) ) {
                    $r = $verus->$e( $p );
                }
                else {
                    $r = $verus->$e();
                }
                if ( is_array( $r ) ) {
                    return vct_format( $r );
                    break;
                }
                else {
                    if ( strpos( $r, 'curltest') !== FALSE ) {
                        $r = strstr( $r, '"params"' );
                        $r = preg_replace('/"params": /', '', $r);
                        $r = substr( $r, 0, strpos( $r, "}' -H" ) );
                        return vct_return_helper( 2, $r );
                        break;
                    }
                    else {
                        return $r;
                        break;
                    }
                }
            }
            else {
                // If method not found, error
                die( $c['lng'][10] );
            }
            break;
    }
}

/**
 * Format
 * 
 * Formats the values of provided array to return human-readable and accurate representation of data points
 */
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

/**
 * Clean
 * 
 * Cleans up data provided, removing whitespace, ensuring lowercase, etc
 */
function vct_clean( $d ) {
    $d = trim( htmlentities( strip_tags( $d ) ) );
    if ( get_magic_quotes_gpc() ) {
        $d = stripslashes( $d );
    }
    // Replace all non-alpha characters or spaces with underscore
    $d = preg_replace( '/\s+|[^\da-z]/i', '_', $d );
    $d = strtolower( $d );
    return $d;
}

/**
 * Return Helper
 * 
 * For errors, params missing, or similar to provide a clean json compatible output
 */
function vct_return_helper( $t, $d ) {
    global $c;
    switch ( $t ) {
        case 1:
            $r = array( 'return' => $c['lng'][8], 'details' => $c['lng'][9] );
            break;
        case 2:
            $r = array( 'return' => $c['lng'][8], 'details' => $c['lng'][9], 'param_example' => $d );
            break;
        default:
            $r = array( 'return' => $t, 'details' => $d );
            break;
        }
    return str_replace('\"', '"', json_encode( $r, TRUE ) );
}
