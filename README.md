# VerusChainTools
Verus Chain Tools is a PHP toolkit for Zcash compatible blockchain integration, for bridging PHP applications

Before usage, a php config file must be created within the same folder utilizing a standard json_encode and with an opening `<php? ` in the file, then saved as "veruschaintools_config.php"

A sample of the file contents is:

``<?php a:2:{s:4:"vrsc";a:5:{s:8:"rpc_user";s:8:"userNAME";s:8:"rpc_pass";s:8:"passWORD";s:4:"port";s:5:"27486";s:5:"taddr";s:34:"RECEIVE_T_ADDRESS";s:5:"zaddr";s:78:"RECEIVE_ZS_ADDRESS";}s:4:"arrr";a:5:{s:8:"rpc_user";s:8:"userNAME";s:8:"rpc_pass";s:8:"passWORD";s:4:"port";s:5:"45453";s:5:"taddr";s:0:"";s:5:"zaddr";s:78:"RECEIVE_ZS_ADDRESS";}}``

This file can be generated using the install script included with VerusPay.  You can also manually produce it, as long as you understand how to adhere to the format.

A more thorough install process is in the works!

Questions can be issued to J Oliver Westbrook via this git repo.
