#!/bin/bash
#set working directory to the location of this script
DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
cd $DIR
mkdir /tmp/veruspayinstall
cp -r veruspay_scripts /tmp/veruspayinstall/
chmod +x /tmp/veruspayinstall/veruspay_scripts -R
#Get variables and user input
clear
echo "     =========================================================="
echo "     |   WELCOME TO THE VERUS CHAINTOOLS DAEMON INSTALLER!    |"
echo "     |                             version 0.5.2              |"
echo "     |                                                        |"
echo "     |  Support for: Verus, Pirate, Komodo                    |"
echo "     |                                                        |"
echo "     |  This installer will install Verus Chain Tools and     |"
echo "     |  the selected wallet daemons. This installer should be |"
echo "     |  used either on a dedicated remote wallet server or on |"
echo "     |  your WordPress-WooCommerce Store server.              |"
echo "     |                                                        |"
echo "     |  If you are installing on your store server, do so     |"
echo "     |  only AFTER you have installed WordPress but BEFORE    |"
echo "     |  you insall the VerusPay plugin.                       |"
echo "     |                                                        |"
echo "     |  If you are installing on a dedicated wallet server,   |"
echo "     |  please note this installer is meant for a new server. |"
echo "     |  If this is not a new server or you already have a     |"
echo "     |  wallet daemon installed, please abort and contact us  |"
echo "     |  in the official VerusCoin Discord.  Othewise, just    |"
echo "     |  continue!                                             |"
echo "     |                                                        |"
echo "     |                                                        |"
echo "     |     Press any key to continue or CTRL-Z to Abort       |"
echo "     |                                                        |"
echo "     |                                                        |"
echo "     =========================================================="
read anykeyone
clear
shopt -s nocasematch
echo "Is this server a REMOTE WALLET server (not the same as your store server)?"
echo "Choose a wallet daemon installation mode:"
echo ""
echo "1) This server is a DEDICATED WALLET SERVER and is REMOTE FROM MY STORE"
echo "2) This server is THE SAME SERVER AS MY WOOCOMMERCE STORE"
echo ""
echo "Enter a number option (1 or 2) and press ENTER:"
read whatserver
if [ "$whatserver" == "1" ];then
    echo "Okay, we will configure this as a REMOTE WALLET SERVER from your"
    echo "VerusPay WooCommerce Store server."
    export rootpath="/var/www/html"
    export domain="$( curl http://icanhazip.com )"
    export remoteinstall=1
else
    echo "Okay, we will configure this as the SAME SERVER of your"
    echo "VerusPay WooCommerce Store server."
    echo ""
    locwpconfig="$(sudo find /var/www -type f -name 'wp-config.php')"
    if [ -z "$locwpconfig" ];then
        echo "not found!"
        exit
    else
        export rootpath=${locwpconfig%"/wp-config.php"}
        echo "It appears your WordPress store is installed at $rootpath"
        echo "if that is incorrect, press CTRL-Z to end this script."
    fi
    export domain="localhost"
    export remoteinstall=0
fi
echo ""
echo ""
echo "Note: This install may require up to 30 GB of free disk space depending on which daemons you choose to install."
echo ""
generalfreespace=$(df --output=avail -h "$PWD" | sed '1d;s/[^0-9]//g')"GB"
echo "It looks like you have about $generalfreespace available. Following is an estimate of how much space you'll need for installing each daemon (installing takes about double the space of the chain, after the install that space is reclaimed):"
echo ""
echo "Verus: ~3GB"
echo "Pirate: ~5GB"
echo "Komodo: ~20GB"
echo ""
echo "Do you want to continue? Have enough free space? (Yes or No)"
read allhdspace
if [[ $allhdspace == "no" ]] || [[ $allhdspace == "n" ]];then
    echo ""
    echo "Okay, exiting now..."
    sleep 3
    exit
else
    echo ""
    echo "Okay, continuing..."
    echo ""
    sleep 2
fi
if [ "$remoteinstall" == "1" ];then
    echo "Enter the IP address of your WooCommerce VerusPay store server"
    echo "(e.g. 123.12.123.123):"
    read iptoallow
    echo ""
    echo "Would you like this script to install a self-signed SSL certificate?"
    echo "If you don't know how to do it yourself, answer with Yes: (Yes or No)"
    read anscert
    if [[ $anscert == "yes" ]] || [[ $anscert == "y" ]];then
        export certresp=1
    else
        export certresp=0
    fi
else
    sleep 1
fi
echo "Install Base Wallet Daemons (VRSC, ARRR, and KMD supported in this installer - VCT supports any bitcoin or zcash forked chain, but these are the base chains we can install here as they are the officially supported chains by this project) along with Verus Chain Tools?  You can just install VCT if you prefer: "
echo ""
echo "(Yes (to install any of the three chains) or No (to just install VCT))"
read whatinstall
if [[ $whatinstall == "yes" ]] || [[ $whatinstall == "y" ]];then
    export walletinstall=1
else
    export walletinstall=0
fi
if [ "$walletinstall" == "1" ];then
    echo "Install Pirate ARRR Daemon?"
    read arrrans
    if [[ $arrrans == "yes" ]] || [[ $arrrans == "y" ]];then
        export arrr=1
    else
        export arrr=0
        count_arrr_z=0
    fi
    if [ "$arrr" == "1" ];then
        echo ""
        echo "Now you need to enter valid PIRATE wallet addresses YOU OWN which will be used to withdraw store funds."
        echo "To paste in the addresses within a Linux terminal right-click and paste, or SHIFT-CTRL-V."
        echo ""
        echo ""
        echo "Carefully enter a valid ARRR Sapling address, where you'll receive store cash outs:"
        read arrr_z
        count_arrr_z=${#arrr_z}
    fi
    echo "Install Verus VRSC Daemon?"
    read vrscans
    if [[ $vrscans == "yes" ]] || [[ $vrscans == "y" ]];then
        export vrsc=1
    else
        export vrsc=0
        count_vrsc_z=0
    fi
    if [ "$vrsc" == "1" ];then
        echo ""
        echo "Now you need to enter valid VERUS wallet addresses YOU OWN which will be used to withdraw store funds."
        echo "To paste in the addresses within a Linux terminal right-click and paste, or SHIFT-CTRL-V."
        echo ""
        echo ""
        echo "Carefully enter a valid VRSC Transparent address, where you'll receive Transparent store cash outs:"
        read vrsc_t
        count_vrsc_t=${#vrsc_t}
        echo "Carefully enter a valid VRSC Sapling address, where you'll receive Sapling store cash outs:"
        read vrsc_z
        count_vrsc_z=${#vrsc_z}
    fi
    echo "Install Komodo KMD Daemon?"
    read kmdans
    if [[ $kmdans == "yes" ]] || [[ $kmdans == "y" ]];then
        export kmd=1
        echo ""
        echo "Please note: the Komodo blockchain is VERY LARGE"
        echo "and you'll need a min of 20 GB free to install KMD"
        echo "(10GB for the chain, 10GB for the bootstrap) after"
        echo "the install, only about 11 GB is used."
        echo ""
        echo "If you do not have enough disk space, it's recommended"
        echo "you exit now and increase your disk space avail."
        echo ""
        freespace=$(df --output=avail -h "$PWD" | sed '1d;s/[^0-9]//g')"GB"
        echo "It looks like you have $freespace available."
        echo ""
        echo "Do you want to continue? Have enough free space? (Yes or No)"
        read kmdhdspace
        if [[ $kmdhdspace == "no" ]] || [[ $kmdhdspace == "n" ]];then
            echo ""
            echo "Okay, exiting now..."
            sleep 3
            exit
        else
            echo ""
            echo "Okay, continuing..."
            echo ""
            sleep 2
        fi
    else
        export kmd=0
        count_kmd_t=0
    fi
    if [ "$kmd" == "1" ];then
        echo ""
        echo "Now you need to enter valid KOMODO wallet addresses YOU OWN which will be used to withdraw store funds."
        echo "To paste in the addresses within a Linux terminal right-click and paste, or SHIFT-CTRL-V."
        echo ""
        echo ""
        echo "Carefully enter a valid KMD Transparent address, where you'll receive Transparent store cash outs:"
        read kmd_t
        count_kmd_t=${#kmd_t}
    fi
else
    export vrsc=0
    export arrr=0
    export kmd=0
fi
if [[ $vrsc == "0" ]] && [[ $arrr == "0" ]] && [[ $kmd == "0" ]];then
    echo "No Wallet Daemon Selected! It is recommended that you allow"
    echo "the script to install the store wallet daemons, otherwise"
    echo "you'll need to request help at the VerusCoin Official Discord"
    echo "for manual configuration."
    echo ""
    echo "If you didn't meant to do this and want to install one or all"
    echo "wallet daemons, simply start this script again after it breaks."
    break
else
    echo ""
    echo "Please CAREFULLY verify that the following"
    echo "RECEIVE addresses you entered for store cashouts"
    echo "are ABSOLUTELY CORRECT.  If not, cancel and"
    echo "restart this script."
    echo ""
    echo "      "$vrscname
    echo $vrsc_t
    echo $vrsc_z
    echo ""
    echo ""
    echo ""
    echo "      "$arrrname
    echo $arrr_z
    echo ""
    echo ""
    echo ""
    echo "      "$kmdname
    echo $kmd_t
    echo $kmd_z
    echo ""
    echo ""
    echo "Do the above addresses all look COMPLETELY ACCURATE? (Yes or No)"
    read confirmaddr
    if [[ $confirmaddr == "yes" ]] || [[ $confirmaddr == "y" ]];then
        echo "You have confirmed that the addresses you entered are accurate"
        echo "and that you own these and hold the private keys, and can access"
        echo "funds sent to them.  Remember, these are your cash-out addresses"
        echo "for when you cashout from your online store crypto. If you enter"
        echo "them incorrectly here, and can't actually get funds sent to them"
        echo "there is absolutely no way to recover crypto sent to them!!"
        echo ""
        echo "So let's just confirm ONCE more! Are you sure these are accurate"
        echo "and that the addresses accurately coincide with the coin as they"
        echo "show above? Addresses are below the coin heading. Confirm? (Yes or No)"
        read confirmaddragain
        if [[ $confirmaddragain == "yes" ]] || [[ $confirmaddragain == "y" ]];then
            echo ""
            echo "Okay! Let's continue..."
            sleep 3
        else
            echo ""
            echo ""
            echo "Okay, good thing we double-checked! This script will now end, you can"
            echo "start it again and try again :)"
            exit
        fi
    else
        echo ""
        echo ""
        echo "Okay, good thing we checked! This script will now end, you can"
        echo "start it again and try again :)"
        exit
    fi
    echo ""
    echo "Beginning server configuration and installation of the following:"
    echo ""
    echo $vrscname
    echo $arrrname
    echo $kmdname
fi
[ "$ulength" == "" ] && ulength=10
[ "$plength" == "" ] && plength=66
export rpcuser="user"$(tr -dc A-Za-z0-9 < /dev/urandom | head -c ${ulength} | xargs)
export rpcpass="pass"$(tr -dc A-Za-z0-9 < /dev/urandom | head -c ${plength} | xargs)
export access="v036"$(tr -dc A-Za-z0-9 < /dev/urandom | head -c ${plength} | xargs)
if [ "$remoteinstall" == "1" ];then
    sudo fallocate -l 4G /swapfile
    echo "Setting up 4GB swap file..."
    sleep 3
    sudo chmod 600 /swapfile
    sudo mkswap /swapfile
    sudo swapon /swapfile
    sudo cp /etc/fstab /etc/fstab.bk
    echo "/swapfile none swap sw 0 0" | sudo tee -a /etc/fstab
    echo "vm.swappiness=40" | sudo tee -a /etc/sysctl.conf
    echo "vm.vfs_cache_pressure=50" | sudo tee -a /etc/sysctl.conf
    clear
else
    clear
fi
echo "Installing some dependencies..."
sleep 1
sudo apt -qq update
sudo apt --yes -qq install build-essential pkg-config libc6-dev m4 g++-multilib autoconf libtool ncurses-dev unzip git python python-zmq zlib1g-dev wget libcurl4-openssl-dev bsdmainutils automake curl screen unzip
sudo apt -qq update
sudo apt -y -qq autoremove
if [ "$remoteinstall" == "1" ];then
    clear
    echo "Installing Apache..."
    echo ""
    echo ""
    sleep 3
    sudo apt -qq update
    sudo apt -y -qq autoremove
    sudo apt --yes -qq install curl wget apache2
    sudo ufw allow OpenSSH
    sudo ufw allow from $iptoallow to any port 443
    sudo cp /etc/apache2/apache2.conf /etc/apache2/apache2.conf.bak
    echo "ServerName $domain" | sudo tee -a /etc/apache2/apache2.conf
    sudo a2enmod rewrite
    sudo systemctl restart apache2
    sudo apt --yes -qq install php libapache2-mod-php
    sudo rm /etc/apache2/mods-available/dir.conf
    sudo touch /etc/apache2/mods-available/dir.conf
    cd /tmp/veruspayinstall
cat >dir.conf <<EOL
<IfModule mod_dir.c>
        DirectoryIndex index.php index.html index.cgi index.pl index.xhtml index.htm
</IfModule>
# vim: syntax=apache ts=4 sw=4 sts=4 sr noet
EOL
    sudo mv /tmp/veruspayinstall/dir.conf /etc/apache2/mods-available/dir.conf
    sudo systemctl restart apache2
    sudo apt -qq update
    sudo apt --yes -qq install php-curl php-gd php-mbstring php-xml php-xmlrpc php-soap php-intl php-zip expect
    sudo systemctl restart apache2
    if [ "$certresp" == "1" ];then
        echo "Installing SSL..."
        echo ""
        sleep 3
        openssl req -x509 -nodes -days 365 -newkey rsa:2048 -keyout /etc/ssl/private/apache-selfsigned.key -out /etc/ssl/certs/apache-selfsigned.crt
cat >ssl-params.conf <<EOL
SSLCipherSuite EECDH+AESGCM:EDH+AESGCM:AES256+EECDH:AES256+EDH
SSLProtocol All -SSLv2 -SSLv3 -TLSv1 -TLSv1.1
SSLHonorCipherOrder On
Header always set X-Frame-Options DENY
Header always set X-Content-Type-Options nosniff
# Requires Apache >= 2.4
SSLCompression off
SSLUseStapling on
SSLStaplingCache "shmcb:logs/stapling-cache(150000)"
# Requires Apache >= 2.4.11
SSLSessionTickets Off
EOL
        sudo mv /tmp/veruspayinstall/ssl-params.conf /etc/apache2/conf-available/ssl-params.conf
        sudo cp /etc/apache2/sites-available/default-ssl.conf /etc/apache2/sites-available/default-ssl.conf.bak
        sudo rm /etc/apache2/sites-available/default-ssl.conf
        cd /tmp/veruspayinstall
cat >default-ssl.conf <<EOL
<IfModule mod_ssl.c>
        <VirtualHost _default_:443>
                ServerAdmin your_email@example.com
                ServerName $domain

                DocumentRoot $rootpath

                ErrorLog ${APACHE_LOG_DIR}/error.log
                CustomLog ${APACHE_LOG_DIR}/access.log combined

                SSLEngine on

                SSLCertificateFile      /etc/ssl/certs/apache-selfsigned.crt
                SSLCertificateKeyFile /etc/ssl/private/apache-selfsigned.key

                <FilesMatch "\.(cgi|shtml|phtml|php)$">
                                SSLOptions +StdEnvVars
                </FilesMatch>
                <Directory /usr/lib/cgi-bin>
                                SSLOptions +StdEnvVars
                </Directory>

        </VirtualHost>
</IfModule>
EOL
        sudo mv /tmp/veruspayinstall/default-ssl.conf /etc/apache2/sites-available/default-ssl.conf
        sudo a2enmod ssl
        sudo a2enmod headers
        sudo a2ensite default-ssl
        sudo a2enconf ssl-params
        sudo systemctl restart apache2
        clear
    else
        echo "Proceeding without SSL, either configure yourself or uncheck"
        echo "the SSL checkbox within VerusPay when you configure."
    fi
else
    clear
fi
echo "Downloading and unpacking Verus Chain Tools scripts..."
echo ""
echo ""
sleep 3
cd /tmp/veruspayinstall
wget https://github.com/joliverwestbrook/VerusChainTools/releases/download/0.5.2/veruschaintools_0_5_2.tar.xz
wget https://github.com/joliverwestbrook/VerusChainTools/releases/download/0.5.2/veruschaintools_0_5_2.md5
md5vctraw=`md5sum -b veruschaintools_0_5_2.tar.xz`
md5vct=${md5vctraw/% *veruschaintools_0_5_2.tar.xz/}
md5vctcompare=`cat veruschaintools_0_5_2.md5`
if [ "$md5vctcompare" == "$md5vct" ];then
     echo "Checksum matched using MD5!  Continuing..."
else
     echo "VerusChainTools checksum did not match! Exiting..."
     echo ""
     echo "Please report this in the Verus discord"
     exit
fi
tar -xvf veruschaintools_0_5_2.tar.xz
rm veruschaintools_0_5_2.tar.xz
rm veruschaintools_0_5_2.md5
sudo mkdir $rootpath/veruschaintools
sudo mv /tmp/veruspayinstall/* $rootpath/veruschaintools
clear
echo "Installing Verus Chain Tools..."
echo ""
echo ""
sleep 3
sudo chown -R www-data:www-data $rootpath/veruschaintools
sudo chmod 755 -R $rootpath/veruschaintools
clear
if [ "$vrsc" == "1" ];then
    echo "Downloading and unpacking latest Verus CLI release..."
    echo "installing to: /opt/verus ..."
    echo ""
    echo ""
    sleep 6
    sudo mkdir -p /opt/verus
    sudo chown -R $USER:$USER /opt/verus
    mkdir /tmp/veruspayinstall/verus
    cd /tmp/veruspayinstall/verus
    wget $(curl -s https://api.github.com/repos/VerusCoin/VerusCoin/releases/latest | grep 'browser_' | grep -E 'Linux|linux' | grep -v 'sha256' | cut -d\" -f4 )
    wget $(curl -s https://api.github.com/repos/VerusCoin/VerusCoin/releases/latest | grep 'browser_' | grep -E 'Linux|linux' | grep -v 'sha256' | cut -d\" -f4 )".sha256"
    shavrscraw=`sha256sum -b Verus-CLI*.gz`
    shavrsc=${shavrscraw/% *Ver*.gz/}
    shavrsccompareraw=`cat Verus-CLI*.sha256`
    shavrsccompare=${shavrsccompareraw/%  Ver*.gz/}
    if [ "$shavrsccompare" == "$shavrsc" ];then
        echo "Checksum matched using SHA256!  Continuing..."
        rm Verus-CLI*.sha256
    else
        echo "Verus daemon checksum did not match! Exiting..."
        echo ""
        echo "Please report this in the Verus discord"
        exit
    fi
    tar -xvf Verus-CLI*.gz
    cd */
    mv * /opt/verus
    clear
    echo "Fetching Zcash parameters if needed..."
    echo ""
    echo ""
    sleep 3
    mv /tmp/veruspayinstall/veruspay_scripts/officialsupportscripts/verus/* /opt/verus/
    chmod +x /opt/verus/*.sh
    /opt/verus/fetchparams.sh
    clear
    echo "Downloading and unpacking VRSC bootstrap..."
    echo "setting up configuration files..."
    echo ""
    echo ""
    sleep 3
    mkdir /opt/verus/VRSC
    cd /tmp/veruspayinstall
    wget https://bootstrap.0x03.services/veruscoin/VRSC-bootstrap.tar.gz
    wget https://bootstrap.0x03.services/veruscoin/VRSC-bootstrap.tar.gz.sha256sum
    md5vrscbootraw=`sha256sum -b VRSC-bootstrap.tar.gz`
    md5vrscboot=${md5vrscbootraw/% *VRSC-bootstrap.tar.gz/}
    md5vrscbootcompareraw=`cat VRSC-bootstrap.tar.gz.sha256sum`
    md5vrscbootcompare=${md5vrscbootcompareraw/%  VRSC-bootstrap.tar.gz/}
    if [ "$md5vrscbootcompare" == "$md5vrscboot" ];then
        echo "Checksum matched using SHA256!  Continuing..."
    else
        echo "Verus Bootstrap checksum did not match! Exiting..."
        echo ""
        echo "Please report this in the Verus discord"
        exit
    fi
tar -xvf VRSC-bootstrap.tar.gz -C /opt/verus/VRSC
cat >/opt/verus/VRSC.conf <<EOL
rpcuser=$rpcuser
rpcpassword=$rpcpass
rpcport=27486
server=1
txindex=1
rpcworkqueue=256
rpcallowip=127.0.0.1
datadir=/opt/verus/VRSC
wallet=vrsc_store.dat
EOL
    clear
    echo "Starting new screen and running Verus daemon to begin Verus sync..."
    echo ""
    echo ""
    sleep 6
    screen -d -m /opt/verus/start.sh
    echo "Installing cron job to run verusstat.sh script every 5 min"
    echo "to check Verus daemon status and start if it stops..."
    echo ""
    echo ""
    sleep 6
    cd /tmp/veruspayinstall
    crontab -l > tempveruscron
    echo "*/5 * * * * /opt/verus/verusstat.sh" >> tempveruscron
    crontab tempveruscron
    rm tempveruscron
    clear
    vrscstat="Yes"
else
    vrscstat="No"
fi
if [ "$arrr" == "1" ];then
    echo "Downloading and unpacking latest Pirate CLI release..."
    echo "installing to: /opt/pirate ..."
    echo ""
    echo ""
    sleep 6
    sudo mkdir -p /opt/pirate
    sudo chown -R $USER:$USER /opt/pirate
    mkdir /tmp/veruspayinstall/pirate
    cd /tmp/veruspayinstall/pirate
    wget $(curl -s https://api.github.com/repos/joliverwestbrook/pirate-komodo/releases/latest | grep 'browser_' | grep -v 'md5' | cut -d\" -f4 )
    wget $(curl -s https://api.github.com/repos/joliverwestbrook/pirate-komodo/releases/latest | grep 'browser_' | grep -v 'md5' | cut -d\" -f4 )".md5"
    md5arrrraw=`md5sum -b komodo*.gz`
    md5arrr=${md5arrrraw/% *komodo*.gz/}
    md5arrrcompareraw=`cat komodo*.md5`
    md5arrrcompare=${md5arrrcompareraw/%  komodo*.gz/}
    if [ "$md5arrrcompare" == "$md5arrr" ];then
        echo "Checksum matched using MD5!  Continuing..."
        rm komodo*.md5
    else
        echo "Pirate daemon checksum did not match! Exiting..."
        echo ""
        echo "Please report this in the Verus discord"
        exit
    fi
    tar -xvf komodo*.gz
    cd */
    mv * /opt/pirate
    clear
    echo "Fetching Zcash parameters if needed..."
    echo ""
    echo ""
    sleep 3
    mv /tmp/veruspayinstall/veruspay_scripts/officialsupportscripts/pirate/* /opt/pirate/
    chmod +x /opt/pirate/*.sh
    /opt/pirate/fetchparams.sh
    clear
    echo "Getting ARRR bootstrap..."
    echo "setting up configuration file..."
    echo ""
    echo ""
    sleep 3
    mkdir /opt/pirate/ARRR
    cd /tmp/veruspayinstall
    wget https://bootstrap.dexstats.info/PIRATE-bootstrap.tar.gz
    wget https://bootstrap.dexstats.info/PIRATE-bootstrap.tar.gz.sha256
    shaarrrraw=`sha256sum -b PIRATE-bootstrap.tar.gz`
    shaarrr=${shaarrrraw/% *PIRATE-bootstrap.tar.gz/}
    shaarrrcompare=`cat PIRATE-bootstrap.tar.gz.sha256`
if [ "$shaarrrcompare" == "$shaarrr" ];then
     echo "Checksum matched using SHA256!  Continuing..."
else
     echo "Pirate Bootstrap checksum did not match! Exiting..."
     echo ""
     echo "Please report this in the Verus discord"
     exit
fi
    tar -xvf PIRATE-bootstrap.tar.gz -C /opt/pirate/ARRR
cat >/opt/pirate/PIRATE.conf <<EOL
rpcuser=$rpcuser
rpcpassword=$rpcpass
rpcport=45453
server=1
txindex=1
rpcworkqueue=256
rpcallowip=127.0.0.1
datadir=/opt/pirate/ARRR
wallet=arrr_store.dat
EOL
    clear
    echo "Starting new screen and running Pirate daemon to begin Pirate sync..."
    echo ""
    echo ""
    sleep 6
    screen -d -m /opt/pirate/start.sh
    echo "Installing cron job to run piratestat.sh script every 5 min"
    echo "to check Pirate daemon status and start if it stops..."
    echo ""
    echo ""
    sleep 6
    cd /tmp/veruspayinstall
    crontab -l > temppiratecron
    echo "*/5 * * * * /opt/pirate/piratestat.sh" >> temppiratecron
    crontab temppiratecron
    rm temppiratecron
    clear
    arrrstat="Yes"
else
    arrrstat="No"
fi
if [ "$kmd" == "1" ];then
    echo "Downloading and unpacking latest Komodo CLI release..."
    echo "installing to: /opt/komodo ..."
    echo ""
    echo ""
    sleep 6
    sudo mkdir -p /opt/komodo
    sudo chown -R $USER:$USER /opt/komodo
    mkdir /tmp/veruspayinstall/komodo
    cd /tmp/veruspayinstall/komodo
    wget $(curl -s https://api.github.com/repos/joliverwestbrook/pirate-komodo/releases/latest | grep 'browser_' | grep -v 'md5' | cut -d\" -f4 )
    wget $(curl -s https://api.github.com/repos/joliverwestbrook/pirate-komodo/releases/latest | grep 'browser_' | grep -v 'md5' | cut -d\" -f4 )".md5"
    md5kmdraw=`md5sum -b komodo*.gz`
    md5kmd=${md5kmdraw/% *komodo*.gz/}
    md5kmdcompareraw=`cat komodo*.md5`
    md5kmdcompare=${md5kmdcompareraw/%  komodo*.gz/}
    if [ "$md5kmdcompare" == "$md5kmd" ];then
        echo "Checksum matched using MD5!  Continuing..."
        rm komodo*.md5
    else
        echo "Komodo daemon checksum did not match! Exiting..."
        echo ""
        echo "Please report this in the Verus discord"
        exit
    fi
    tar -xvf komodo*.gz
    cd */
    mv * /opt/komodo
    clear
    echo "Fetching Zcash parameters if needed..."
    echo ""
    echo ""
    sleep 3
    mv /tmp/veruspayinstall/veruspay_scripts/officialsupportscripts/komodo/* /opt/komodo/
    chmod +x /opt/komodo/*.sh
    /opt/komodo/fetchparams.sh
    clear
    echo "Getting KMD bootstrap..."
    echo "setting up configuration files..."
    echo ""
    echo ""
    sleep 3
    mkdir /opt/komodo/KMD
    cd /tmp/veruspayinstall
    wget https://bootstrap.dexstats.info/KMD-bootstrap.tar.gz
    wget https://bootstrap.dexstats.info/KMD-bootstrap.tar.gz.sha256
    shakmdraw=`sha256sum -b KMD-bootstrap.tar.gz`
    shakmd=${shakmdraw/% *KMD-bootstrap.tar.gz/}
    shakmdcompare=`cat KMD-bootstrap.tar.gz.sha256`
if [ "$shakmdcompare" == "$shakmd" ];then
     echo "Checksum matched using SHA256!  Continuing..."
else
     echo "Komodo Bootstrap checksum did not match! Exiting..."
     echo ""
     echo "Please report this in the Verus discord"
     exit
fi
    tar -xvf KMD-bootstrap.tar.gz -C /opt/komodo/KMD
cat >/opt/komodo/KMD.conf <<EOL
rpcuser=$rpcuser
rpcpassword=$rpcpass
rpcport=7771
server=1
txindex=1
rpcworkqueue=256
rpcallowip=127.0.0.1
datadir=/opt/komodo/KMD
wallet=kmd_store.dat
EOL
    clear
    echo "Starting new screen and running Komodo daemon to begin Komodo sync..."
    echo ""
    echo ""
    sleep 6
    screen -d -m /opt/komodo/start.sh
    echo "Installing cron job to run komodostat.sh script every 5 min"
    echo "to check Komodo daemon status and start if it stops..."
    echo ""
    echo ""
    sleep 6
    cd /tmp/veruspayinstall
    crontab -l > tempkomodocron
    echo "*/5 * * * * /opt/komodo/komodostat.sh" >> tempkomodocron
    crontab tempkomodocron
    rm tempkomodocron
    clear
    kmdstat="Yes"
else
    kmdstat="No"
fi
clear
echo ""
echo " Cleaning Up...."
sleep 3
sudo rm /tmp/veruspayinstall -r
clear
echo ""
echo "     ======================================================"
echo "     =                     IMPORTANT!                     ="
echo "     =  Write down the following info in a secure place.  ="
echo "     ======================================================"
echo "    |                                                      |"
echo "    |  -------------------------------------------------   |"
echo "    |  RPC Credentials for Both Chains (if installed):     |"
echo "    |  -------------------------------------------------   |"
echo "    |     RPC User: $rpcuser                               |"
echo "    |     RPC Pass: $rpcpass                               |"
echo "    |                                                      |"
echo "    |  -------------------------------------------------   |"
echo "    |   New Wallets Installed:                             |"
echo "    |   ------------------------------------------------   |"
echo "    |      PIRATE ARRR: $arrrstat                          |"
echo "    |      Verus VRSC:  $vrscstat                          |"
echo "    |      Komodo KMD:  $kmdstat                           |"
echo "    |                                                      |"
echo "    |   ------------------------------------------------   |"
echo "    |   Finish VCT Install by navigating to:               |"
echo "    |   ------------------------------------------------   |"
echo "    |      Daemon URL: $domain/veruschaintools             |"
echo "    |                                                      |"
echo "    |  REMOTE/DAEMON-ONLY USERS IMPORTANT NOTE:            |"
echo "    |        After you follow the install at the URL above |"
echo "    |        return here and enable your Daemon Server     |"
echo "    |        firewall by issuing the following command:    |"
echo "    |                                                      |"
echo "    |           sudo ufw enable                            |"
echo "    |                                                      |"
echo "     ======================================================"
echo ""