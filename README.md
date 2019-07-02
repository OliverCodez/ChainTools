# VerusChainTools - PHP Toolkit for Zcash and BTC based blockchains

 - Contributors: Oliver Westbrook
 - Copyright: Copyright (c) 2019, John Oliver Westbrook 
 - Stable Version: 0.4.0-beta

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
Verus Chain Tools is a PHP toolkit for Zcash compatible blockchain integration, for bridging PHP applications

Before usage, a php config file must be created within the same folder utilizing a standard json_encode and with an opening `<php? ` in the file, then saved as "veruschaintools_config.php"

A sample of the file contents is:

``<?php a:2:{s:4:"vrsc";a:5:{s:8:"rpc_user";s:8:"userNAME";s:8:"rpc_pass";s:8:"passWORD";s:4:"port";s:5:"27486";s:5:"taddr";s:34:"RECEIVE_T_ADDRESS";s:5:"zaddr";s:78:"RECEIVE_ZS_ADDRESS";}s:4:"arrr";a:5:{s:8:"rpc_user";s:8:"userNAME";s:8:"rpc_pass";s:8:"passWORD";s:4:"port";s:5:"45453";s:5:"taddr";s:0:"";s:5:"zaddr";s:78:"RECEIVE_ZS_ADDRESS";}}``

This file can be generated using the install script included with VerusPay.  You can also manually produce it, as long as you understand how to adhere to the format.

A more thorough install process is in the works!

Questions can be issued to J Oliver Westbrook via this git repo.
