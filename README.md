# VerusChainTools - PHP Web Interface Toolkit for Verus, Verus PBaaS, Zcash and Bitcoin based blockchains

 - Contributors: John Oliver Westbrook
 - Copyright: Copyright (c) 2019, John Oliver Westbrook 
 - Stable Version: 0.5.2

## The MIT License (MIT)
 
Copyright (c) 2019 John Oliver Westbrook

This is experimental and unfinished software. Use at your own risk! 

No warranty for any kind of damage!

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
Verus Chain Tools is a PHP API toolkit for interfacing with Bitcoin and Zcash style blockchains. With an emphasis on Verus and Verus PBaaS blockchains, a friendly fork of Komodo which was in-turn a fork of Zcash. Verus Chain Tools (VCT) allows for bridging PHP applications to interact with the rpc daemon of the blockchain, making integration between web apps and the blockchain possible, allowing for full RPC access to the daemon (as set by the developer implementing).  This API can be implemented as a backend for virtually any web wallet, web application, gui, etc providing easy installation, implementation and bridging between applications and a blockchain.

No PHP dependencies or frameworks are used in this project.  All code is open source and included in this repository.

Before use, you must run the install by visiting the URL path where you placed the files included in this project. Check the latest release for a tar.xz file and accompanying md5, simply unzip the tar to the web directory where you will run this API (it MUST be an empty folder, no other applications running in the same folder). It is recommended that your VCT API server be installed at the same server running the daemon or daemons with which you'll interact, unless you have a strong understanding of routing RPC traffic to the VCT API server.

## General Notes and Security
In this repo you'll find the main `index.php` file, which is the VCT API script. Included is the `verusclass.php` class file, `lang.php` language file, `install.php` one-time installer, `update.php` updater for use in either editing config settings or adding more daemons, `update-vp.php` updater file for VerusPay installations to update and edit config settings and daemons, and a demo page to help developers and users learn how the API works and how to interact with it for their own applications. Releases do NOT include the demo.php file found in this repo, as it is for learning/demo only.

In addition to these scripts, included is a `htaccess` file to place in the same directory containing the API, if desired, just rename to `.htaccess`.  The API should always be in it's own directory, not in a directory with other live files being used on your site. Whether the API is used on a daemon server, behind a strong firewall with only your web server allowed through, or on the same server as your website and your daemon, it always needs to be in its own subfolder of the domain/IP. The custom `htaccess` file works in conjunction with the Apache Directory settings, locking down this subfolder to only the local server or remote server for API access. Any attempts to browse to this folder are treated as a "normal" 404 request using this unique .htaccess file, confuscating what folder even contains your API and blocking access with the actual 403 forbidden process.

It is advised to always seperate your public-facing web server and daemon server for maximum security, however, security is only ever as good as the measures you've taken.  If, for example, your web server is not well secured, your daemon server is still at as much risk as the capabilities allowed by the API and the funds left in the daemon server. Always use strong, non-word passwords and difficult to guess usernames when setting up any web service/site. In addition, it's strongly advised to only allow SSH-Key logins to all your servers.

## Installation, Updates for Config, and Upgrade of VCT
To install and use this API, simply place the contents of this repo inside the webserver directory you are working with, in most cases running on the same server as your blockchain RPC daemon/CLI. This script was designed specifically for use on the same server of the daemon, if you wish to separate it, you'll need to change the default 'localhost' setting inside the index.php file and make any other changes necessary for that config to work.

For the most common case, where you install this API on the same server running your daemon(s), follow these basic steps.  If, for example, your server running your blockchain daemon is at IP address 65.127.27.27, it's easiest to setup an Apache/PHP web server (preferrably with HTTPS enforced) on the same server and place VerusChainTools inside the server's web server directory (e.g. /var/www/html).  You can place it in a subfolder if you wish, e.g. /var/www/html/veruschaintools.  Once you have the files installed there, you can browse to their location and the Installer will guide you.  After installing successfully, it's recommended that you lock that IP/server down with a strong firewall to allow only the web app server from which you'll interact with the VCT api.

After successfully installing, the install.php file is removed.  If you need to completely reconfigure your VCT API in the future, simply place the install.php file back in the directory containing the VCT API, change any firewall settings to allow you to browse to your API directly, and again follow the install steps.  It treats it as a new install each time, and generates a new Access Code.  Otherwise updating is allowed by using the Update Code as a GET call, e.g. https://yourdomain.com/locationofVCT/?code=YOURUPDATECODE. If using VerusPay, the update is allowed from within VerusPay, using your unique Update Code when running the update.

Upgrading to the latest version of Verus Chain Tools is also allowed from the script itself, by doing the same as an update, but also including "update=3", for example: https://yourdomain.com/locationofVCT/?code=YOURUPDATECODE&update=3.  

Questions can be issued to J Oliver Westbrook via this git repo or you may reach out to me at the official VerusCoin discord at https://discord.gg/VRKMP2S
