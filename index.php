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
 *      update.php
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
    if ( ! empty( $_POST['S'] ) ) {
        // Create the config file and remove the install script
        $posted = array_change_key_case( $_POST, CASE_UPPER );
        $daemon = vct_find_daemon( $posted );
        file_put_contents( 'config.php','<?php $c = \''.serialize( $daemon ).'\'; ?>' );
        unlink( 'install.php' );
        die( '<h2><center>Successfully Installed!</center></h2>' );
    }
    else {
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
     * Includes and Config
     */
    $_url = 'localhost';
    include_once( 'verusclass.php' );
    if ( file_exists( 'config.php' ) ) {
        include_once( 'config.php' );
    }
    else {
        echo vct_return_helper( 'error', 'Config file missing or corrupt' );
        die();
    }
    include_once( 'lang.php' );
    // Set config settings to array
    $c = unserialize($c);
    /**
     * Manual Update
     * 
     * Check if an update is being performed
     */
    if ( isset( $_REQUEST['update'] ) ) {
        if ( $_SERVER['REQUEST_METHOD'] === 'GET' && $_REQUEST['update'] === $c['U'] && file_exists( 'update.php' ) ) {
            include_once( 'update.php' );
            die();
        }
        else if ( $_SERVER['REQUEST_METHOD'] === 'POST' && $_REQUEST['update'] === $c['U'] ) {
            $posted = array_change_key_case( $_POST, CASE_UPPER );
            $posted['DYN'] = FALSE;
            $c['S'] = $posted['S'];
            $posted = vct_find_daemon( $posted );
            unset( $posted['UPDATE'], $posted['S'], $posted['D'], $c['C'] );
            $daemon = array_merge( $c, $posted );
            file_put_contents( 'config.php','<?php $c = \''.serialize( $daemon ).'\'; ?>' );
            die( '<h2><center>Update Successful!</center></h2>' );
        }
        else {
            die();
        }
    }
    // Check for function whitelist, blank array if none
    if ( !isset( $c['F'] ) ) {
        $c['F'] = array();
    }
    /**
     * Get Input
     */
    $i = json_decode( file_get_contents( 'php://input' ), TRUE);
    if ( empty( $i ) ) {
        die('<h2>'.$lng[$c['L']][1].'</h2>');
    }
    /**
     * Check Things
     * 
     * Check access code, chain, and method
     */
    // Compare access code provided with set in config
    if ( $i['a'] != $c['A'] ) {
        echo vct_return_helper( 'error', $lng[$c['L']][2] );
        die();
    }
    // Check that chain is set
    if ( empty( $i['c'] ) ) {
        echo vct_return_helper( 'error', $lng[$c['L']][3] );
        die();
    }
    // TODO: Function to check for chain on local wallet and return result (error out if non-exist or down)
    //
    // Check that method is set
    if ( empty( $i['m'] ) ) {
        echo vct_return_helper( 'error', $lng[$c['L']][4] );
        die();
    }

    /**
     * Check Chain & Finalize Settings
     * 
     * Check for chain daemon info in config, if not found, check server and if found update config
     */
    $_chn = strtoupper( $i['c'] );
    if ( !isset( $c['C'][$_chn] ) || !isset( $c['C'][$_chn]['L'] ) || !isset( $c['C'][$_chn]['U'] ) || !isset( $c['C'][$_chn]['P'] ) || !isset( $c['C'][$_chn]['N'] ) ) {
        $data = array(
            'DYN' => TRUE,
            'S' => 'u',
            $_chn.'_TXTYPE' => '0',
            'C' => array(
                $_chn,
            ),
            $_chn.'_T' => 'ForCashout_Complete_update_manually_on_VCT_server',
            $_chn.'_Z' => 'ForCashout_Complete_update_manually_on_VCT_server',
        );
        $data = vct_find_daemon( $data );
        if ( $data === FALSE ) {
            echo vct_return_helper( 'error', $_chn.' daemon must be running on daemon server' );
            die();
        }
        $c['S'] = $data['S'];
        $c['C'] = array_merge( $c['C'], $data['C'] );
        file_put_contents( 'config.php','<?php $c = \''.serialize( $c ).'\'; ?>' );
        $daemon = $c['C'][$_chn];
    }
    else {
        $daemon = $c['C'][$_chn];
    }
    $i['m'] = vct_clean( $i['m'] );
    $i = array_merge( $i, array(
        'pro' => 'http',
        'url' => $_url,
        'dir' => $daemon['L'],
        'usr' => $daemon['U'],
        'pas' => $daemon['P'],
        'prt' => $daemon['N'],
        )
    );

//TEST
/*$i = array(
    'm' => 'version',
    'p' => null,
    'o' => null,
    'pro' => 'http',
    'url' => $_url,
    'usr' => 'user3420326507',
    'pas' => 'pass5fd3c109caf64a87d38c59e951f363e09f0f6faf81cdcfed6b7254d2b430443564',
    'prt' => '18361',
);
//END */
    /**
     * Go VerusClass!
     * 
     * Run the _go function to process the provided method and related data
     */
    echo _go( $i );
}

/**
 * Go Process Request
 * 
 * Main data processor using verusclass to communicate with compatible RPC daemons
 */
function _go( $d ) {
    // Include config array
    global $c;
    global $lng;
    // New Verus class for interacting with daemon
    $verus = new Verus( $d['usr'], $d['pas'], $d['url'], $d['prt'], $d['pro'] );
    $s = $verus->status();
    if ( $s === 'daemon offline' ) {
        return vct_return_helper( 'error', $s );
        die();
    }
    $chn = $d['c'];
    $tx = $c['C'][$chn]['TX'];
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
            return vct_return_helper( 'success', $s );
            break;
        /**
         * Helpful Tools
         * 
         * Custom section with some helper commands for ease-of-use and integration. Have a suggestion? Create an issue in https://github.com/joliverwestbrook/VerusChainTools/issues
         *  */

        // Return the current daemon version
        case 'version':
            return vct_return_helper( 'success', $verus->getinfo()['version'] );
            break;
        // Return the lowest confirm TX
        case 'lowest':
            if ( !isset( $p ) ) {
                return vct_return_helper( 'error', $lng[$c['L']][9] );
                break;
            }
            else if ( substr( $p, 1, 2 ) === 'zs' ) {
                $r = $verus->z_listreceivedbyaddress( $p );
                $a = array();
                foreach ( $r as $v ) {
                    array_push( $a, $v['amount'] );
                }
                return vct_return_helper( 'success', array_sum( $a ) );
                break;
            }
            else {
                return vct_return_helper( 'success', $verus->getreceivedbyaddress( $p ) );
                break;
            }
            break;
        // Return a count of all T (transparent) addresses
        case 't_count':
            if ( !isset( $p ) ) {
                return vct_return_helper( 'error', $lng[$c['L']][9] );
                break;
            }
            else {
                return vct_return_helper( 'success', count( $verus->getaddressesbyaccount( $p ) ) );
                break;
            }
            break;
        // Return a count of all Z (private) addresses
        case 'z_count':
            return vct_return_helper( 'success', count( $verus->z_listaddresses() ) );
            break;
        // Iterate all T and Z addresses and return balance of each and totals
        case 'bal':
            if ( !isset( $p ) ) {
                return vct_return_helper( 'error', $lng[$c['L']][9] );
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
                        return vct_return_helper( 'success', vct_format( $r ) );
                        break;
                    }
                    else {
                        return vct_return_helper( 'success', $r );
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
            if ( $c['M'] === '_vp_' ) {
                switch ( $e ) {
                    /**
                     * VerusPay-specific Custom Methods
                     *  */
                    // Show the configured T-Cashout address where relevant
                    case 'show_taddr':
                        if ( $tx == 0 || $tx == 1 ) {
                            return vct_return_helper( 'success', $c['C'][$chn]['T'] );
                        }
                        else {
                            return vct_return_helper( 'error', $lng[$c['L']][13] );
                        }
                        break;
                    // Show the configured Z-Cashout address where relevant
                    case 'show_zaddr':
                        if ( $tx == 0 || $tx == 2 ) {
                            return vct_return_helper( 'success', $c['C'][$chn]['Z'] );
                        }
                        else {
                            return vct_return_helper( 'error', $lng[$c['L']][13] );
                        }
                        break;
                    // Perform a cashout to the configured T address where relevant
                    // TODO: Left off here, cleaning all functions and final backend edits!
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
                            return $lng[$c['L']][11];
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
                            return vct_return_helper( 'success', $results );
                            break;
                        }
                        else {
                            // If address not set, error
                            return $lng[$c['L']][11];
                            break;
                        }
                        break;
                    // All other methods, filtered by whitelist preconfigured during install
                    default:
                        // If whitelisted, continue
                        if ( in_array( $e, $c['F'], TRUE ) ) {
                            if ( isset( $p ) ) {
                                $r = $verus->$e( $p );
                            }
                            else {
                                $r = $verus->$e();
                            }
                            if ( is_array( $r ) ) {
                                return vct_return_helper( 'success', vct_format( $r ) );
                                break;
                            }
                            else {
                                if ( strpos( $r, 'curltest') !== FALSE ) {
                                    $r = strstr( $r, '"params"' );
                                    $r = preg_replace('/"params": /', '', $r);
                                    $r = substr( $r, 0, strpos( $r, "}' -H" ) );
                                    return vct_return_helper( 'error', 'Params missing or incorrect, e.g. '.$r );
                                    break;
                                }
                                else {
                                    return vct_return_helper( 'success', $r );
                                    break;
                                }
                            }
                        }
                        else {
                            // If method not whitelisted, error
                            return vct_return_helper( 'error', $lng[$c['L']][10] );
                            die();
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
            else if ( $c['M'] === '_lt_' ) {
                // If whitelisted, continue
                if ( in_array( $e, $c['F'], TRUE ) ) {
                    if ( isset( $p ) ) {
                        $r = $verus->$e( $p );
                    }
                    else {
                        $r = $verus->$e();
                    }
                    if ( is_array( $r ) ) {
                        return vct_return_helper( 'success', vct_format( $r ) );
                        break;
                    }
                    else {
                        if ( strpos( $r, 'curltest') !== FALSE ) {
                            $r = strstr( $r, '"params"' );
                            $r = preg_replace('/"params": /', '', $r);
                            $r = substr( $r, 0, strpos( $r, "}' -H" ) );
                            return vct_return_helper( 'error', 'Params missing or incorrect, e.g. '.$r );
                            break;
                        }
                        else {
                            return vct_return_helper( 'success', $r );
                            break;
                        }
                    }
                }
                else {
                    // If method not whitelisted, error
                    return vct_return_helper( 'error', $lng[$c['L']][10] );
                    die();
                    break;
                }
            }
            /**
             * Bridge Mode
             * 
             * Full access to daemon (no whitelist)
             */
            else if ( $c['M'] === '_bg_' ) {
                if ( isset( $p ) ) {
                    $r = $verus->$e( $p );
                }
                else {
                    $r = $verus->$e();
                }
                if ( is_array( $r ) ) {
                    return vct_return_helper( 'success', vct_format( $r ) );
                    break;
                }
                else {
                    if ( strpos( $r, 'curltest') !== FALSE ) {
                        $r = strstr( $r, '"params"' );
                        $r = preg_replace('/"params": /', '', $r);
                        $r = substr( $r, 0, strpos( $r, "}' -H" ) );
                        return vct_return_helper( 'error', 'Params missing or incorrect, e.g. '.$r );
                        break;
                    }
                    else {
                        return vct_return_helper( 'success', $r );
                        break;
                    }
                }
            }
            else {
                // If method not found, error
                return vct_return_helper( 'error', $lng[$c['L']][10] );
                die();
                break;
            }
            break;
    }
}

/**
 * Find/Add Daemon
 * 
 * Pass API chain ticker to search for chain daemon on server and optionally run update config if found ($u = true to update config)
 */
function vct_find_daemon( $data ) {
    foreach ( $data['C'] as $k => $v ) {
        $v = strtoupper( $v );
        $dir = trim( shell_exec( 'find /opt /home -type d -name "'.$v.'" 2>&1 | grep -v "Permission denied"' ) );
        if ( !isset( $dir ) || empty( $dir ) || !strstr( $dir, $v ) ) { // Not Found on Server
            if ( file_exists( 'config.php' ) && $data['S'] != 'u' ) {
                unlink( 'config.php' );
            }
            if ( $data['DYN'] === TRUE ) {
                return FALSE;
                die();
            }
            die( $v.' daemon not found on this server - install halted - add daemon and restart install' );
        }
        else {
            if ( isset( $data['DYN'] ) ) {
                unset( $data['DYN'] );
            }
            if ( !isset( $data['C'][$v] ) ) {
                $data['C'][$v] = array();
            }
            $data['C'][$v]['D'] = date( 'Y-m-d H:i:s', time() );
            $data['C'][$v]['TX'] = $data[$v.'_TXTYPE'];
            unset( $data[$v.'_TXTYPE'] );
            if ( isset( $data[$v.'_T'] ) ) {
                $data['C'][$v]['T'] = $data[$v.'_T'];
                unset( $data[$v.'_T'] );
            }
            if ( isset( $data[$v.'_Z'] ) ) {
                $data['C'][$v]['Z'] = $data[$v.'_Z'];
                unset( $data[$v.'_Z'] );
            }
            $data['C'][$v]['L'] = $dir;
            $data['C'][$v]['U'] = trim( substr( shell_exec( 'cat ' . $dir . '/' . $v . '.conf | grep "rpcuser="' ), strlen( 'rpcuser=' ) ) );
            $data['C'][$v]['P'] = trim( substr( shell_exec( 'cat ' . $dir . '/' . $v . '.conf | grep "rpcpassword="' ), strlen( 'rpcpassword=' ) ) );
            $data['C'][$v]['N'] = trim( substr( shell_exec( 'cat ' . $dir . '/' . $v . '.conf | grep "rpcport="' ), strlen( 'rpcport=' ) ) );
            unset( $data['C'][$k] );
        }
    }
    if ( $data['S'] != 'u' ) {
        $data['F'] = explode( ',', $data['F'] );
        foreach( $data['F'] as $k => $v ) {
            $data['F'][$k] = vct_clean( $v );
        }
    }
    return $data;
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
    $r = array( 'return' => $t, 'details' => $d );
    return json_encode( str_replace('\"', '"', json_encode( $r, TRUE ) ), TRUE );
}

