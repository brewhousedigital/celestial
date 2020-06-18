<?php


// Maintenance mode. This locks the website to only be visible for your IP address.
// This is at the top for easy access.
$maintenanceMode = false;


// File name of an html page in your html folder.
// Comment out if you dont want a custom page and just want it to say "Under maintenance. Come back soon."
$customMaintenancePage = "under-maintenance.html";


// Website title, email title, and website description defaults can be set here.
// Each page has the option to overwrite these.
$siteTitle  = "Celestial Framework";
$emailFrom  = "Celestial Framework";
$siteDescription = "Celestial Framework can be deployed with a single zip file and set up in less than 1 minute.";


// Site URL setup
$siteURL = "https://www.celestial.dev";


// Encryption key: random long string.
// https://www.random.org/strings/?num=1&len=20&digits=on&upperalpha=on&loweralpha=on&unique=on&format=html&rnd=new
$encryptionKey = "ABCDEFGHIJQLMNOPQRSTUVWXYZ1234567890";


// The AES-128 encryption system uses a 16 character string (initialization vector) to base it's encryption off of. Keep this secure!
// Generate a 16 character long, random string by using
// https://www.random.org/strings/?num=1&len=20&digits=on&upperalpha=on&loweralpha=on&unique=on&format=html&rnd=new
// https://www.geeksforgeeks.org/how-to-encrypt-and-decrypt-a-php-string/
$cipher_iv = "1234567890ABCDEF";


// Opengraph graphic
$siteOpenGraphImageURL = $siteURL . "/images/site-opengraph.jpg";


// Website meta theme color. This is used on mobile devices for the most part.
$siteMetaThemeColor = "#db1f26";


// Put in your IP address or an array of IP addresses to auto-clear cache for you :)
$ipAddresses = ["123.456.789.000"];








/*****************************

    Third party APIs

****************************/

// Mailgun. Great tool for efficiently sending emails. First 1000 emails per month are free.
// https://documentation.mailgun.com/en/latest/api_reference.html
$MailgunAPIKey = '';
$MailgunDomain = '';
$MailgunDefaultFrom = $emailFrom . ' <team@your-website.com>';


// Google ReCaptcha v3. Block them bots real good.
// https://www.google.com/recaptcha/intro/v3.html
$recaptchaPublishableKey = "";
$recaptchaSecretKey = "";


// Google Analytics
// UA-123456789-0
$googleAnalytics = "";










/*****************************

Custom integrations and global variables

 ****************************/

// Your service here
// $yourServiceAPI = "";
// $data['site']['yourService'] = $yourServiceAPI;