# VerusChainTools - PHP Web Interface Toolkit for Zcash and BTC based blockchains

 - Contributors: John Oliver Westbrook
 - Copyright: Copyright (c) 2019, John Oliver Westbrook 
 - Stable Version: 0.4.0-rc

## The MIT License (MIT)
 
Copyright (c) 2019 John Oliver Westbrook

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.

## Description
Verus Chain Tools is a PHP API toolkit for Zcash or Bitcoin compatible blockchain integration, for bridging PHP applications to interact with an rpc daemon.

Before use, you must run the Install by simply visiting your main script api URL, after you've placed it on your VerusChainTools (VCT) server. Your VCT API server must be the same server running the daemon or daemons with which you'll interact, unless you have a strong understanding of routing RPC traffic to the VCT API server.  

## Installation
To install and use this API, simply place the contents of this repo inside the webserver directory you are working with, in most cases running on the same server as your blockchain RPC daemon/CLI. This script was designed specifically for use on the same server of the daemon, if you wish to separate it, you'll need to change the default 'localhost' setting inside the index.php file and make any other changes necessary for that config to work.

For the most common case, where you install this API on the same server running your daemon(s), follow these basic steps.  If, for example, your server running your blockchain daemon is at IP address 127.127.27.27, it's easiest to setup an Apache/PHP web server (preferrably with HTTPS enforced) on the same server and place VerusChainTools inside the server's web server directory (e.g. /var/www/html).  You can place it in a subfolder if you wish, e.g. /var/www/html/veruschaintools.  Once you have the files installed there, you can browse to their location and the Installer will guide you.  After installing successfully, it's recommended that you lock that IP/server down with a strong firewall to allow only the server from which you'll interact with the VCT api.

After successfully installing, the install.php file is removed.  If you need to reconfigure your VCT API in the future, simply place the install.php file back in the directory containing the VCT API, change any firewall settings to allow you to browse to your API directly, and again follow the install steps.  It treats it as a new install each time, and generates a new Access Code.

Questions can be issued to J Oliver Westbrook via this git repo or you may reach out to me at the official VerusCoin discord at https://discord.gg/VRKMP2S
