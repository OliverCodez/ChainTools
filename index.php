<?php
define( 'VCTAccess', TRUE );
$vct_version = '0.5.2';
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
 *      update-vp.php
 *      install.php (temporary installer)
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

/**
 * First-time Install
 * 
 * Check if first run / install and either run install or process results
 */
// TODO: Include option to install as API vs Local
// TODO: Create way to return chains installed and active for api caller
if ( file_exists( 'install.php' ) ) {
    if ( is_writable( 'install.php' ) ) {
        if ( ! empty( $_POST['S'] ) ) {
            // Create the config file and remove the install script
            $posted = array_change_key_case( $_POST, CASE_UPPER );
            $daemon = _get_daemon( $posted );
            file_put_contents( 'config.php','<?php if(!defined(\'VCTAccess\')){die(\'Direct access denied\');} $c = \''.serialize( $daemon ).'\'; ?>' );
            unlink( 'install.php' );
            die( '<h2><center>Successfully Installed!</center></h2>' );
        }
        else {
            include_once( 'install.php' );
            die();
        }
    }
    else {
        die( '<h2 style="color:red"><center>Error</center></h2><p>Cannot Write to Directory - Check Permissions for Web User (usually www-data).  The directory containing VerusChainTools must be owned by your servers web user.  It is recommended you also set permissions 755 on the same folder and all contents.</p><p>Install will now exit.</p>' );
    }
}

/**
 * Main Script
 */
$_url = 'localhost';
include_once( 'verusclass.php' );
if ( file_exists( 'config.php' ) ) {
    include_once( 'config.php' );
}
else {
    echo _out( 'Config file missing or corrupt', FALSE );
    die();
}
// Set config settings to array
$c = unserialize($c);
include_once( 'lang.php' );
if ( isset( $c['L'] ) ) {
    $lng = $lng[$c['L']];
}
else {
    $lng = $lng['eng'];
}

/**
 * Update Being Performed?
 * 
 * Check if an update/upgrade is being performed and run upgrade function
 */
if ( isset( $_REQUEST['code'] ) && $_REQUEST['code'] === $c['U'] ) {
    if ( isset( $_REQUEST['version'] ) ) {
        $_upmsg = '';
        if ( isset( $_REQUEST['upgraded'] ) ) {
            $_upmsg = $lng[24];
        }
        echo $_upmsg . $lng[23] . '<h3 style="text-align:center;font-weight:bold;display:inline">' . $vct_version . '</h3>';
        die();
    }
    $ui = array(
        't' => '',
        'c' => $_REQUEST['code'], // Update code passed
        'p' => '',
    );
    // 'update' is for type of request, codes: 0 = direct coin update; 1 = indirect/VerusPay coin update; 2 = save updated coin data; 3 = codebase upgrade to latest version
    if ( isset( $_REQUEST['update'] ) ) {
        $ui['t'] = $_REQUEST['update'];
    }
    else {
        $ui['t'] = '0';
    }
    if ( $ui['t'] == '2' ) {
        $ui['p'] = array_change_key_case( $_POST, CASE_UPPER );
    }
    if ( is_writable( 'config.php' ) ) {
        _upgrade( $ui, $c, $lng );
    }
    else {
        die( $lang[21] );
    }
}
// Check for function whitelist, blank array if none
if ( !isset( $c['F'] ) ) {
    $c['F'] = array();
}
/**
 * Get Input
 */
// TODO: May keep API and just use folder security...or Allow for use as API or Local include, if API do the following (add Local include later and use config.php file to record this option which will be set during the install process )
$i = json_decode( file_get_contents( 'php://input' ), TRUE);
if ( empty( $i ) ) {
    die('<h2>'.$lng[1].'</h2>');
}
/**
 * Check Things
 * 
 * Check access code, chain, and method
 */
// Compare access code provided with set in config
if ( $i['a'] != $c['A'] ) {
    echo _out( $lng[2], FALSE );
    die();
}
// Check that chain is set
if ( empty( $i['c'] ) ) {
    echo _out( $lng[3], FALSE );
    die();
}
// Check that method is set
if ( empty( $i['m'] ) ) {
    echo _out( $lng[4], FALSE );
    die();
}
/**
 * Check Chain & Finalize Settings
 * 
 * Check for chain daemon info in config, if not found, check server and if found update config
 */
$_chn = strtoupper( $i['c'] );
if ( $_chn === '_STAT_' ) {
    switch( $i['m'] ) {
        case 'chainlist':
            $filtered = array_filter( $c['C'] );
            if ( ! empty( $filtered ) ) {
                echo _out( $c['C'] );
                die();
            }
            else {
                echo _out( '_no_chains_found_' );
                die();
            }
            break;
        case 'vct_version':
            echo _out( $vct_version );
            die();
    }
}
else if ( !isset( $c['C'][$_chn] ) || !isset( $c['C'][$_chn]['L'] ) || !isset( $c['C'][$_chn]['U'] ) || !isset( $c['C'][$_chn]['P'] ) || !isset( $c['C'][$_chn]['N'] ) ) {
    $data = array(
        'DYN' => TRUE,
        'S' => 'u',
        $_chn.'_TXTYPE' => '0',
        'C' => array(
            $_chn,
        ),
        $_chn.'_T' => $lng[17],
        $_chn.'_Z' => $lng[17],
    );
    $data = _get_daemon( $data );
    if ( $data === FALSE ) {
        echo _out( $_chn.$lng[16], FALSE );
        die();
    }
    $c['S'] = $data['S'];
    $c['C'] = array_merge( $c['C'], $data['C'] );
    file_put_contents( 'config.php','<?php if(!defined(\'VCTAccess\')){die(\'Direct access denied\');} $c = \''.serialize( $c ).'\'; ?>' );
    $daemon = $c['C'][$_chn];
}
else {
    $daemon = $c['C'][$_chn];
}
$i['m'] = _filter( $i['m'] );
$i = array_merge( $i, array(
    'pro' => 'http',
    'url' => $_url,
    'dir' => $daemon['L'],
    'usr' => $daemon['U'],
    'pas' => $daemon['P'],
    'prt' => $daemon['N'],
    )
);
/**
 * Go VerusClass!
 * 
 * Run the _go function to process the provided method and related data
 */
echo _go( $i );
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
    $verus = new Verus( $d['usr'], $d['pas'], $d['url'], $d['prt'], $d['pro'], $lng );
    $s = $verus->status();
    if ( $s == '0' ) {
        $r = json_encode( array(
            'stat' => $s,
            'desc' => $lng[19],
        ), TRUE );
        return _out( $r, FALSE );
        die();
    }
    else {
        $s = json_encode( array(
            'stat' => $s,
            'desc' => $lng[20],
        ), TRUE );
    }
    $chn = $d['c'];
    $tx = $c['C'][$chn]['TX'];
    $_ts = TRUE;
    $_zs = TRUE;
    if ( isset( $c['C'][$chn]['T'] ) ) {
        if ( $c['C'][$chn]['T'] == $lng[17] || strlen( $c['C'][$chn]['T'] ) < 10 ) {
            $_ts = FALSE;
            $_t = $lng[17];
        }
        else {
            $_t = $c['C'][$chn]['T'];
        }
    }
    else {
        $_ts = FALSE;
        $_t = $lng[13];
    }
    if ( isset( $c['C'][$chn]['Z'] ) ) {
        if ( $c['C'][$chn]['Z'] == $lng[17] || strlen( $c['C'][$chn]['Z'] ) < 78 ) {
            $_zs = FALSE;
        }
        else {
            $_z = $c['C'][$chn]['Z'];
        }
    }
    else {
        $_zs = FALSE;
    }
    $e = $d['m'];
    $p = $d['p'];
    $o = $d['o']; // TODO: Usage?

    switch ( $e ) {
        /**
         * Stats and Testing
         * 
         * Special custom test and config methods
         */
        case 'test':
            return _out( $s );
            break;
        /**
         * Helpful Tools
         * 
         * Custom section with some helper commands for ease-of-use and integration. 
         * Have a suggestion? 
         * Create an issue in https://github.com/joliverwestbrook/VerusChainTools/issues
         *  */
        // Return integer representing the type of txs chain is capable of (0=transparent+private,1=transparent only, 2=private only)
        case 'type':
            return _out( $tx );
            break;
        // Return the current daemon version
        case 'version':
            return _out( $verus->getinfo()['version'] );
            break;
        // Return a count of all T (transparent) addresses
        case 't_count':
            if ( !isset( $p ) ) {
                return _out( $lng[9], FALSE );
                break;
            }
            else {
                return _out( count( $verus->getaddressesbyaccount( $p ) ) );
                break;
            }
            break;
        // Return a count of all Z (private) addresses
        case 'z_count':
            return _out( count( $verus->z_listaddresses() ) );
            break;
        // Iterate all T and Z addresses and return balance of each and totals
        case 'bal':
            if ( !isset( $p ) ) {
                $p = '""'; 
            }
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
                $ub = array( 'unconfirmed' => $verus->getunconfirmedbalance() );
                $r = array_merge( $tb, $zb, $verus->z_gettotalbalance(), $ub );
                if ( is_array( $r ) ) {
                    return _out( _format( $r ) );
                    break;
                }
                else {
                    return _out( $r );
                    break;
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
                    // Return the lowest confirm TX
                    case 'lowest':
                        if ( !isset( $p ) ) {
                            return _out( $lng[9], FALSE );
                            break;
                        }
                        $_isZ = FALSE;
                        if ( substr( $p, 2, 2 ) === 'zs' ) {
                            $_isZ = TRUE;
                        }
                        if ( $_isZ ) {
                            $r = $verus->z_listreceivedbyaddress( $p );
                            $a = array();
                            foreach ( $r as $v ) {
                                array_push( $a, $v['amount'] );
                            }
                            return _out( array_sum( $a ) );
                            break;
                        }
                        else {
                            return _out( $verus->getreceivedbyaddress( $p ) );
                            break;
                        }
                        break;
                    // Show the configured T-Cashout address where relevant
                    case 'show_taddr':
                        if ( $tx == 0 || $tx == 1 ) {
                            return _out( $c['C'][$chn]['T'] );
                        }
                        else {
                            return _out( $lng[13], FALSE );
                        }
                        break;
                    // Show the configured Z-Cashout address where relevant
                    case 'show_zaddr':
                        if ( $tx == 0 || $tx == 2 ) {
                            return _out( $c['C'][$chn]['Z'] );
                        }
                        else {
                            return _out( $lng[13], FALSE );
                        }
                        break;
                    // Perform a cashout to the configured T address where relevant
                    case 'cashout_t':
                        if ( $tx == 0 || $tx == 1 ) {
                            if ( $_ts === FALSE ) {
                                return _out( $_t, FALSE );
                                break;
                            }
                            $total = $verus->getbalance();
                            $param = json_encode( array( $_t, $total, 'Cashout_'.time(), 'VerusPay', TRUE ), TRUE );
                            return _go_any( $verus, 'sendtoaddress', $param );
                            break;
                        }
                        else {
                            return _out( $lng[13], FALSE );
                            break;
                        }
                        break;
                    // Perform a cashout to the configured Z address where relevant
                    case 'cashout_z':
                        if ( $tx == 0 || $tx == 2 ) { // If zs tx supported
                            if ( $_zs === FALSE ) {
                                return _out( $_z, FALSE );
                                break;
                            }
                            // Do cashout
                            $zaddlist = $verus->z_listaddresses();
                            $result = array();
                            foreach ( $zaddlist as $zadd ) {
                                $zbal = ( $verus->z_getbalance( json_encode( array( $zadd ), TRUE ) ) - 0.00010000 );
                                if ( $zbal > 0.0000001 ) {
                                    $zbal = (float)number_format( $zbal, 8 );
                                    $param = array( $zadd, array( array( 'address' => $_z, 'amount' => $zbal ) ) );
                                    $result[$zadd] = array(
                                        'cashout_address' => $_z,
                                        'amount' => $zbal,
                                        'opid' => $verus->z_sendmany( json_encode( $param, TRUE ) )
                                    );
                                }
                            }
                            return _out( $result );
                            break;
                        }
                        else { // If zs tx NOT supported return error
                            return _out( $lng[13], FALSE );
                            break;
                        }
                        break;
                    // All other methods, filtered by whitelist preconfigured during install
                    default:
                        // If whitelisted, continue
                        if ( in_array( $e, $c['F'], TRUE ) ) {
                            return _go_any( $verus, $e, $p );
                        }
                        else {
                            // If method not whitelisted, error
                            return _out( $lng[10], FALSE );
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
                    return _go_any( $verus, $e, $p );
                }
                else {
                    // If method not whitelisted, error
                    return _out( $lng[10], FALSE );
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
                return _go_any( $verus, $e, $p );
            }
            else {
                // If method not found, error
                return _out( $lng[10], FALSE );
                die();
                break;
            }
            break;
    }
}

/**
 * Go Do Any Method
 * 
 * Function to handle default cases for _go function
 */
function _go_any( $verus, $e, $p ) {
    global $lng;
    if ( isset( $p ) ) {
        $r = $verus->$e( $p );
    }
    else {
        $r = $verus->$e();
    }
    if ( is_array( $r ) ) {
        return _out( _format( $r ) );
    }
    else {
        if ( strpos( $r, 'curltest') !== FALSE ) {
            $r = strstr( $r, '"params"' );
            $r = preg_replace('/"params": /', '', $r);
            $r = substr( $r, 0, strpos( $r, "}' -H" ) );
            return _out( $lng[15].$r, FALSE );
        }
        else {
            return _out( $r );
        }
    }
}
/**
 * Get Daemon
 * 
 * Pass API chain ticker to search for chain daemon on server and optionally run update config if found
 */
function _get_daemon( $data ) {
    global $lng;
    foreach ( $data['C'] as $k => $v ) {
        $v = strtoupper( $v );
        if ( $v == 'ARRR' ) {
            $vf = 'PIRATE';
        }
        else {
            $vf = $v;
        }
        $dir = $data[$v.'_DIR'];
        if ( !isset( $dir ) || empty( $dir ) ) { // Not Found on Server
            if ( file_exists( 'config.php' ) && $data['S'] != 'u' ) {
                unlink( 'config.php' );
            }
            if ( isset( $data['DYN'] ) && $data['DYN'] === TRUE ) {
                return FALSE;
                die();
            }
            die( $v.$lng[14] );
        }
        else {
            if ( isset( $data['DYN'] ) ) {
                unset( $data['DYN'] );
            }
            if ( !isset( $data['C'][$v] ) ) {
                $data['C'][$v] = array();
            }
            $data['C'][$v]['D'] = date( 'Y-m-d H:i:s', time() );
            $data['C'][$v]['FN'] = $data[$v.'_NAME'];
            unset( $data[$v.'_NAME'] );
            $data['C'][$v]['TX'] = $data[$v.'_TXTYPE'];
            unset( $data[$v.'_TXTYPE'] );
            if ( isset( $data[$v.'_GS'] ) ) {
                $data['C'][$v]['GS'] = $data[$v.'_GS'];
                unset( $data[$v.'_GS'] );
            }
            if ( isset( $data[$v.'_GM'] ) ) {
                $data['C'][$v]['GM'] = $data[$v.'_GM'];
                unset( $data[$v.'_GM'] );
            }
            if ( isset( $data[$v.'_T'] ) ) {
                $data['C'][$v]['T'] = $data[$v.'_T'];
                unset( $data[$v.'_T'] );
            }
            if ( isset( $data[$v.'_Z'] ) ) {
                $data['C'][$v]['Z'] = $data[$v.'_Z'];
                unset( $data[$v.'_Z'] );
            }
            $data['C'][$v]['L'] = $dir;
            $data['C'][$v]['U'] = trim( substr( shell_exec( 'cat ' . $dir . '/' . $vf . '.conf | grep "rpcuser="' ), strlen( 'rpcuser=' ) ) );
            $data['C'][$v]['P'] = trim( substr( shell_exec( 'cat ' . $dir . '/' . $vf . '.conf | grep "rpcpassword="' ), strlen( 'rpcpassword=' ) ) );
            $data['C'][$v]['N'] = trim( substr( shell_exec( 'cat ' . $dir . '/' . $vf . '.conf | grep "rpcport="' ), strlen( 'rpcport=' ) ) );
            unset( $data['C'][$k] );
        }
    }
    if ( $data['S'] != 'u' ) {
        $data['F'] = explode( ',', $data['F'] );
        foreach( $data['F'] as $k => $v ) {
            $data['F'][$k] = _filter( $v );
        }
    }
    return $data;
}

/**
 * Format
 * 
 * Formats the values of provided array to return human-readable and accurate representation of data points
 */
function _format( $d ) {
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
 * Filter
 * 
 * Cleans up data provided, removing whitespace, ensuring lowercase, etc
 */
function _filter( $d, $p = FALSE ) {
    if ( $p === FALSE ) {
        $d = trim( htmlentities( strip_tags( $d ) ) );
        if ( get_magic_quotes_gpc() ) {
            $d = stripslashes( $d );
        }
        // Replace all non-alpha characters or spaces with underscore
        $d = preg_replace( '/\s+|[^\da-z]/i', '_', $d );
        $r = strtolower( $d );
    }
    if ( $p === TRUE ) {
        // TODO: Create filter for params data
    }
    return $r;
}

/**
 * Output Helper
 * 
 * For errors, params missing, or similar to provide a clean json compatible output
 */
function _out( $d, $t = TRUE ) {
    global $lng;
    if ( $t === TRUE ) {
        $t = $lng[5];
    }
    else {
        $t = $lng[8];
    }
    $r = array( 'result' => $t, 'return' => $d );
    return json_encode( $r, TRUE );
}

/**
 * Upgrade function
 * 
 * Performs an inline upgrade of VerusChainTools
 */
function _upgrade( $ui, $c, $lng ) {
    switch ( $ui['t'] ) { // 0 = direct coin update; 1 = indirect/VerusPay coin update; 2 = save updated coin data; 3 = codebase upgrade to latest version
        case '0':
            include_once( 'update.php' );
            break;
        case '1':
            include_once( 'update-vp.php' );
            break;
        case '2':
            $ui['p']['DYN'] = FALSE;
            $c['S'] = $ui['p']['S'];
            $ui['p'] = _get_daemon( $ui['p'] );
            unset( $ui['p']['CODE'], $ui['p']['S'], $ui['p']['D'], $c['C'] );
            $daemon = array_merge( $c, $ui['p'] );
            file_put_contents( 'config.php','<?php if(!defined(\'VCTAccess\')){die(\'Direct access denied\');} $c = \''.serialize( $daemon ).'\'; ?>' );
            die( $lng[18] );
        case '3':
            echo $lng[22];
            $udir = 'upgrades';
            if ( ! file_exists( $udir ) ) {
                mkdir( $udir, 0777, true);
            }
            chdir( $udir );
            exec( 'wget $(curl -s https://api.github.com/repos/joliverwestbrook/veruschaintools/releases/latest | grep "browser_download_url.*xz" | cut -d : -f 2,3 | tr -d \")' );
            exec( 'wget $(curl -s https://api.github.com/repos/joliverwestbrook/veruschaintools/releases/latest | grep "browser_download_url.*md5" | cut -d : -f 2,3 | tr -d \")' );
            if ($handle = opendir('.')) {
                while (false !== ($file = readdir($handle)))
                {
                    if ($file != "." && $file != ".." && strtolower(substr($file, strrpos($file, '.') + 1)) == 'xz')
                    {
                        $file1 = $file;
                    }
                    if ($file != "." && $file != ".." && strtolower(substr($file, strrpos($file, '.') + 1)) == 'md5')
                    {
                        $file2 = $file;
                    }
                }
                closedir($handle);
            }
            $content = 'Downloaded: ' . $file1 . '<br>' . 'Downloaded: ' . $file2;
            echo '<pre style="width: 400px;display: block;position: relative;margin: 0 auto;background: #e3e3e3;padding: 10px 5px;border: inset 1px grey;height: 100px;">'.$content.'</pre>';
            exec( 'tar -xvf ' . $file1 );
            unlink( $file1 );
            $files = scandir( '.' );
            foreach( $files as $file ) {
                if ( is_file( $file ) ) {
                    if( $file == 'install.php' ) {
                        unlink( $file );
                    }
                    else {
                        copy( $file, '../' . $file );
                        unlink( $file );
                    }
                }
            }
            foreach( $files as $file ) {
                if ( is_file( $file ) ) {
                    chdir( '..' );
                    chmod( $file, 0755 );
                }
            }
            header( 'Location: ' . $_SERVER['PHP_SELF'] . '?code=' . $ui['c'] . '&version=true&upgraded=true' );
    }
    die();
}