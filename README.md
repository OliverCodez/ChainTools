# VerusChainTools+ - Advanced and Versatile PHP Web Interface Toolkit for Verus, Verus PBaaS, Komodo, Pirate, Zcash and any other Bitcoin-based blockchain

 - Contributors: John Oliver Westbrook
 - Copyright: Copyright (c) 2019, John Oliver Westbrook
 - Active Development Version: 0.6.0
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
VerusChainTools+ (VCT+) is a PHP API toolkit for interfacing web applications with virtually any "bitcoin-based" blockchain and which "sort of" adheres to RESTful, although it uses JSON responses which technically diverts us away from REST. See the restcookbook here > http://restcookbook.com/Mediatypes/json/.

VCT+ is designed primarily for the Verus and any future Verus PBaaS blockchains, Verus Coin being a friendly fork of Komodo which was in-turn a fork of Zcash. VTC+ allows for the "bridging" of web applications to directly interact with the daemon of the blockchain, making integration between web apps and the blockchain not only possible, but extremely usable and easy, allowing for full RPC access to the daemon. The VCT+ API can be implemented as a backend for virtually any cryptocurrency web wallet, web application, blockchain gui, etc providing easy installation and implementation.

No PHP dependencies or frameworks are used in this project (fully functional on a "vanilla" PHP server implementation).  All code is open source and included in this repository.

## Requirements
VCT+ must be installed on a basic web server running PHP. A simple Apache2+ & PHP7+ running on Debian 9+ or Ubuntu 18+, with a min of 4GB RAM is recommended as a minimal configuration (although as minimal as 1GB RAM + 4GB SWAP is also easily usable for low-availability applications).

## General Notes and Security
This repository includes a `demo.php` file not found within the release, as it's only intended for demonstration and education purposes. Also within this repo and releases, is the main `index.php` script which is the primary VCT+ API script.  Along with this are the class file, basic language file, first-time install file, and two update files...one for WordPress-VerusPay installs and the other for simply updating the daemon config if/when other daemons are added to the server.

In addition to these scripts, included is a `htaccess` file to place in the same directory containing the API, if desired, just rename to `.htaccess`.

The API should always be in it's own directory, not in a directory with other live web files being used on your site. Whether the API is used on a daemon server, behind a strong firewall with only your web server allowed through (recommended), or on the same server as your website and your daemon, it always needs to be in its own subfolder of the domain/IP. The custom `htaccess` file works in conjunction with the Apache Directory settings, locking down this subfolder to only the local server or remote server for API access. Any attempts to browse to this folder are treated as a "normal" 404 request using this unique .htaccess file, confuscating what folder even contains your API and blocking access with the actual 403 forbidden process.

It is advised to always seperate your public-facing web server and daemon server for maximum security, however, security is only ever as good as the measures you've taken.  If, for example, your web server is not well secured, your daemon server is still at as much risk as the capabilities allowed by the API and the funds left in the daemon server. Always use strong, non-word passwords and difficult to guess usernames when setting up any web service/site. In addition, it's strongly advised to only allow SSH-Key logins to all your servers.

## Installation, Updates for Config, and Upgrade of VCT
To install and use this API, simply download the latest official release and extract it to the webserver subfolder to be used. VCT+ is designed to work ON THE SAME SERVER AS YOUR BLOCKCHAIN DAEMON. If you wish to seperate these servers, you'll need to make the necessary configuration changes and tweaks, and this should only be done if you have a STRONG understanding of the implications of doing this!

After you've extracted the release to a webserver subfolder, ensure the correct permissions are set for that subfolder and contents. Your web server user should own the subfolder/contents and at a minimum the subfolder should be 755 and the contents 644. You can set these with the command (as root or sudo) `chmod 644 *.php` within the subfolder.

Once you've set the permissions, simply browse to the IP/Domain and subfolder in a browser.  You'll be greated by the installer and it will walk you through the remaining installation steps. After you've finished with the install, it is recommended your firewall be configured to only allow your web application which you're bridging to access the VCT+ API. 

After successfully installing, the install.php file is removed.  If you need to completely reconfigure your VCT+ API in the future, simply place the install.php file back in the directory containing the VCT+ API, change any firewall settings to allow you to browse to your API directly, and again follow the install steps.  It treats it as a new install each time, and generates a new Access Code.  Otherwise updating is allowed by using the Update Code as a GET call, e.g. https://yourdomain.com/locationofVCT/?code=YOURUPDATECODE. If using VerusPay, the update is allowed from within VerusPay, using your unique Update Code when running the update.

Upgrading to the latest version of Verus Chain Tools is also allowed from the script itself, by doing the same as an update, but also including "update=3", for example: https://yourdomain.com/locationofVCT/?code=YOURUPDATECODE&update=3.  

## Help & Support
If you need help with your install, update, or any other part of the process of implementing VCT+, please reach out to me at johnwestbrook@pm.me. If you find a bug or other issue with this software, please generate a new Issue at https://github.com/joliverwestbrook/VerusChainTools/issues.
