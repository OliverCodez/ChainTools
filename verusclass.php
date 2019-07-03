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
 *      index.php
 *      verusclass.php (this file)
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
 * 
 */
// Begin Class Code
class rpcVerus {
    private $u;
    private $p;
    private $pr;
    private $h;
    private $po;
    private $c;

    // Information and debugging
    public $vct_stat;
    public $vct_err;
    public $vct_raw;
    public $vct_re;
    private $vct_id = 0;
    /**
     * @param string $u
     * @param string $p
     * @param string $h
     * @param int $po
     * @param string $pr
     * @param string $u
     */
    public function __construct( $u, $p, $h, $po, $pr ) {
        $this->u    = $u;
        $this->p    = $p;
        $this->h    = $h;
        $this->po   = $po;
        $this->pr   = $pr;
        $this->c    = null;
    }
    /**
     * @param string|null $vct_cert
     */
    public function setSSL( $vct_cert = null ) {
        $this->pr         = 'https';
        $this->c = $vct_cert;
    }
    public function __call( $vct_meth, $vct_params ) {
        $this->vct_stat       = null;
        $this->vct_err        = null;
        $this->vct_raw = null;
        $this->vct_re     = null;
        // If params is not empty, filter for bool and integers
        if ( !empty( $vct_params ) ) {
            // TODO : This is a problem, causing the array to only take the first entry and ignore beyond
            $vct_params = $vct_params[0];
            foreach ( $vct_params as $key => $value ) {
                if ( is_numeric( $value ) ) {
                    $vct_params[$key] = (int)$value;
                }
                else if ( $value === 'true' ) {
                    $vct_params[$key] = true;
                }
                else if ( $value === 'false' ) {
                    $vct_params[$key] = false;
                }
                else if ( $value === '--' ) {
                    $vct_params[0] = "";
                }
            }
        }
        $vct_params = array_values( $vct_params );
        $this->vct_id++;
        $vct_req = json_encode( array(
            'method' => $vct_meth,
            'params' => $vct_params,
            'id'     => $this->vct_id
        ) );
        // TODO : Test area
        //return $vct_req;
        //die();
        // END
        $vct_c    = curl_init( "{$this->pr}://{$this->h}:{$this->po}" );
        $vct_opt = array(
            CURLOPT_HTTPAUTH       => CURLAUTH_BASIC,
            CURLOPT_USERPWD        => $this->u . ':' . $this->p,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_HTTPHEADER     => array( 'Content-type: application/json' ),
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $vct_req
        );
        if ( ini_get( 'open_basedir' ) ) {
            unset( $vct_opt[CURLOPT_FOLLOWLOCATION] );
        }
        if ( $this->pr == 'https' ) {
            if ( ! empty( $this->c ) ) {
                $vct_opt[CURLOPT_CAINFO] = $this->c;
                $vct_opt[CURLOPT_CAPATH] = DIRNAME( $this->c );
            } else {
                $vct_opt[CURLOPT_SSL_VERIFYPEER] = false;
            }
        }
        curl_setopt_array( $vct_c, $vct_opt );
        $this->vct_raw = curl_exec( $vct_c );
        $this->vct_re = json_decode( $this->vct_raw, true );
        $this->vct_stat = curl_getinfo( $vct_c, CURLINFO_HTTP_CODE );
        $vct_c_error = curl_error( $vct_c );
        curl_close( $vct_c );
        if ( ! empty( $vct_c_error ) ) {
            $this->vct_err = $vct_c_error;
        }
        if ( $this->vct_re[ 'error' ] ) {
            $this->vct_err = $this->vct_re[ 'error' ][ 'message' ];
        } elseif ( $this->vct_stat != 200 ) {
            switch ( $this->vct_stat ) {
                case 400:
                    $this->vct_err = 'Error 400 - Bad Request';
                    break;
                case 401:
                    $this->vct_err = 'Error 401 - Unauthorized';
                    break;
                case 403:
                    $this->vct_err = 'Error 403 - Forbidden';
                    break;
                case 404:
                    $this->vct_err = 'Error 404 - Not Found';
                    break;
            }
        }
        if ( $this->vct_err ) {
            return false;
        }
        return $this->vct_re[ 'result' ];
    }
}