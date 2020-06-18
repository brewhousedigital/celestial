<?php

// Version 1.0.0

// Force the site to use HTTPS
if ($_SERVER['HTTPS'] !== "on") {
    die("Site must be using HTTPS. Please install an SSL certificate to continue.");
}


// Check current PHP Version and verify that it is version 7.2 or higher
if(floatval(PHP_VERSION) < 7.2) {
    $ErrorMessage = 'Your PHP Version is not compatible with Celestial. ';
    $ErrorMessage .= 'Please upgrade to 7.3 or higher.';
    die($ErrorMessage);
}


// Makes URLs case-insensitive by setting the routing to process on lowercase letters
$processURL = strtolower($_SERVER['REQUEST_URI']);


// Ignore GET parameters when checking for trailing slashes
$requestedURL = null;
$processURL = explode('?', $processURL);
if(count($processURL) > 1) {
    $requestedURL = $processURL[0];
    $getParameters = $processURL[1];
} else {
    $requestedURL = $processURL[0];
    $getParameters = "";
}


// Check for trailing slash
if($requestedURL[strlen($requestedURL) - 1] !== "/") {
    // Add Trailing Slash
    $requestedURL = $requestedURL . "/";

    // Add back in GET parameters
    if(strlen($getParameters) !== 0) {
        $requestedURL = $requestedURL . "?" . $getParameters;
    }

    header("HTTP/1.1 301 Moved Permanently");
    header("Location: " . $requestedURL);
    exit();
}


// Maintenance mode
$maintenanceMode = $maintenanceMode ?? false;
$customMaintenancePage = $customMaintenancePage ?? "";
if($maintenanceMode) {
    if(!in_array($_SERVER['REMOTE_ADDR'], $ipAddresses)) {
        if(strlen($customMaintenancePage) > 0) {
            $maintenancePage = file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/../project-files/html/under-maintenance.html");
            $maintenancePage = str_replace("__SITETITLE__", $siteTitle, $maintenancePage);
            echo $maintenancePage;
            exit();
        } else {
            exit("Under maintenance. Come back soon.");
        }
    }
}


/*****************************

Global variables to be used in Twig

 ****************************/
// Begin global site variables
$data['site']['title'] = $siteTitle;
$data['site']['description'] = $siteDescription;
$data['site']['metaThemeColor'] = $siteMetaThemeColor;
$data['site']['openGraphImage'] = $siteOpenGraphImageURL;
$data['site']['baseURL'] = $siteURL;
$data['site']['fullURL'] = $siteURL . $_SERVER['REQUEST_URI'];
$data['site']['recaptchaPublishableKey'] = $recaptchaPublishableKey;
$data['site']['googleAnalytics'] = $googleAnalytics;
$data['user']['authenticated'] = false;
$data['user']['ip'] = $_SERVER['REMOTE_ADDR'];





// PHP defaults
date_default_timezone_set('America/Chicago');
setlocale(LC_ALL,'en_US.UTF-8');
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);
session_start();


// Store the cipher method
$cipher = "AES-128-CTR";
$cipher_length = openssl_cipher_iv_length($cipher);


// Error messaging
ini_set("log_errors", 1);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


// Turn on Twig
$loader = new \Twig\Loader\FilesystemLoader($_SERVER['DOCUMENT_ROOT'] . '/../project-files/html');


// Enable cache based on your IP address
$cache = true;
if(in_array($_SERVER['REMOTE_ADDR'], $ipAddresses)) {
    $cache = false;
} else {
    $cache = $_SERVER['DOCUMENT_ROOT'] . '/../project-files/cache';
}



// Setup Twig Settings
$twig = new \Twig\Environment($loader, [
    'cache' => $cache,
    'debug' => false,
]);


// Additional twig components
$twig->addExtension(new \Twig\Extension\DebugExtension());


// Implement the router and custom Class to make it pretty
$Router = new \Delight\Router\Router();




























/*****************************

PHP functions

 ****************************/


class Create {
    // Send JSON back to the client
    function JSON(array $Array) {
        header('Content-Type: application/json');
        $response = json_encode($Array);
        exit($response);
    }


    // Redirect user to specific page
    function Redirect(string $Destination) {
        header("Location: " . $destination);
        exit();
    }


    // Safely convert whatever variable you have to be a number by removing all non-numeric characters
    function Number($Number, $Type = "integer") {
        if(is_string($Number) || is_int($Number)) {
            $Number = preg_replace('/[^0-9]/', '', $Number);

            if($Type !== "integer") {
                return $Number;
            }

            return intval($Number);
        }

        // If not a number or an int, return null
        return null;
    }


    // Safely convert whatever variable you have to be a floated number by removing all non-numeric characters
    // This creates an american-ized float value
    function Float($Number, $DecimalPlace, $Commas = false) {
        // First clean the entry and remove all non float values
        $Number = preg_replace('/[^0-9.]/', '', $Number);

        // Remove all leading zeroes and extra periods
        $Number = floatval($Number);

        // Convert to string and format to decimal length
        $Number = number_format($Number, $DecimalPlace);

        // Remove the added commas if the user needs
        if(!$Commas) {
            $Number = preg_replace('/[^0-9.]/', '', $Number);
            return floatval($Number);
        }

        // Return a string
        return $Number;
    }


    // Pretty much only works for United States, Mexico, and Canada
    // Safely convert whatever variable you have to be a phone number between 10 and 13 numbers
    // This is helpful to generate safe phone numbers because it removes (), -, and . in the string.
    function PhoneNumber($number) {

        // Safely convert the string by removing all non-numeric characters
        $number = $this->number($number, "string");

        // Phone numbers can be between 10 and 15 digits long across the world
        if(strlen($number) >= 10 && strlen($number) <= 13) {
            return $number;
        } else {
            return null;
        }
    }


    // Create URL for storing in the database. This can be used to automatically generate blog URLs
    // or page URLs created by the website user.
    function URL(string $value) {
        $value = preg_replace("/[^A-Za-z0-9 ]/", '', $value);
        $value = trim($value);
        $value = str_replace("   ", " ", $value);
        $value = str_replace("   ", " ", $value);
        $value = str_replace("   ", " ", $value);
        $value = str_replace("  ", " ", $value);
        $value = str_replace("  ", " ", $value);
        $value = str_replace("  ", " ", $value);
        $value = str_replace(" ", "-", $value);
        $value = strtolower($value);
        $value = urlencode($value);
        return $value;
    }


    // Create a random string based on PHP 7.
    function Random(int $length = 60) {
        $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $pieces = [];
        $max = mb_strlen($keyspace, '8bit') - 1;
        for ($i = 0; $i < $length; ++$i) {
            $pieces []= $keyspace[random_int(0, $max)];
        }

        return implode('', $pieces);
    }


    // Create encrypted value.
    function Secure($ValueToEncrypt) {
        global $cipher;
        global $cipher_iv;
        global $cipher_length;
        global $encryptionKey;

        // Safely create an encrypted value with AES 128 bit openSSL
        $simple_string = strval($ValueToEncrypt);

        // Use openssl_encrypt() function to encrypt the data
        $encryption = openssl_encrypt($simple_string, $cipher, $encryptionKey, 0, $cipher_iv);

        // Return the encrypted string
        return $encryption;
    }


    // Safely convert whatever variable you have to be a monetary value by removing all non-numeric characters
    function MonetaryNumber($Amount) {
        // First clean the entry and remove all non float values
        $Amount = $this->Float($Amount, 2);

        // Multiply by 100 if the value needs to include cents. This is how Stripe handles monetary values
        $Amount = $Amount * 100;

        // Double check that it is an integer
        $Amount = intval($Amount);

        return $Amount;
    }


    // Send mail.
    function Mail(string $To, string $Subject, string $EmailTemplate, array $EmailContent = [], string $Type = "mail") {
        global $MailgunAPIKey;
        global $MailgunDefaultFrom;
        global $MailgunDomain;
        global $siteTitle;
        global $siteURL;


        $response = [];


        // Retrieve requested email template or use a blank template for quick email sending
        $EmailHTML= "";
        $EmailRoot = "/../project-files/emails/";

        if(file_exists($_SERVER['DOCUMENT_ROOT'] . $EmailRoot . $EmailTemplate . ".html")) {
            $EmailHTML = file_get_contents($_SERVER['DOCUMENT_ROOT'] . $EmailRoot . $EmailTemplate . ".html");

        } else {
            $response['status'] = false;
            $response['message'] = "unable to find mail template";
            return false;

        }


        // Defaults
        $EmailContentDefaults = [
            ['__SITETITLE__', $siteTitle],
            ['__SITEURL__', $siteURL],
            ['__YEAR__', date("Y")],
            ['__DATE__', date("M d, Y")],
        ];


        // Update the dynamic values in the email template
        for ($i = 0; $i < count($EmailContentDefaults); $i++) {
            $EmailHTML = str_replace($EmailContentDefaults[$i][0], $EmailContentDefaults[$i][1], $EmailHTML);
        }


        // Update the user supplied dynamic values in the email template
        for ($i = 0; $i < count($EmailContent); $i++) {
            $EmailHTML = str_replace($EmailContent[$i][0], $EmailContent[$i][1], $EmailHTML);
        }


        if($Type === "mail") {
            // Always set content-type when sending HTML email
            $Headers = "From: " . $MailgunDefaultFrom . "\r\n";
            $Headers .= "Reply-To: ". $MailgunDefaultFrom . "\r\n";
            $Headers .= "MIME-Version: 1.0\r\n";
            $Headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";


            mail($To, $Subject, $EmailHTML, $Headers);


            return true;


        } elseif($Type === "mailgun" && strlen($MailgunAPIKey) > 0) {
            // Send mail with MailGun cURL API
            define('MAILGUN_URL', 'https://api.mailgun.net/v3/' . $MailgunDomain);
            define('MAILGUN_KEY', $MailgunAPIKey);

            $array_data = array(
                'from' => $MailgunDefaultFrom,
                'to' => $To,
                'subject' => $Subject,
                'html' => $EmailHTML,
                'text' => '',
                'o:tracking' => 'yes',
                'o:tracking-clicks' => 'yes',
                'o:tracking-opens' => 'yes',
                'o:tag' => '',
                'h:Reply-To' => $MailgunDefaultFrom
            );

            $session = curl_init(MAILGUN_URL.'/messages');
            curl_setopt($session, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($session, CURLOPT_USERPWD, 'api:'.MAILGUN_KEY);
            curl_setopt($session, CURLOPT_POST, true);
            curl_setopt($session, CURLOPT_POSTFIELDS, $array_data);
            curl_setopt($session, CURLOPT_HEADER, false);
            curl_setopt($session, CURLOPT_ENCODING, 'UTF-8');
            curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($session, CURLOPT_SSL_VERIFYPEER, false);
            $response = curl_exec($session);
            curl_close($session);

            $results = json_decode($response, true);

            //echo "<pre>";
            //print_r($results);
            //echo "</pre>";
            //echo $EmailHTML;
            //exit();

            return $results;


        }

        return false;
    }


    // Validate a Google Recaptcha v3 check
    function Recaptcha($RecaptchaPostRequest) {
        global $recaptchaSecretKey;

        $ReturnFormValue = [];

        if(isset($_POST[$RecaptchaPostRequest])) {

            // Make and decode POST request:
            $captcha = $_POST[$RecaptchaPostRequest];
            $url = 'https://www.google.com/recaptcha/api/siteverify';
            $data = array(
                'secret' => $recaptchaSecretKey,
                'response' => $captcha,
                'remoteip' => $_SERVER['REMOTE_ADDR']
            );

            $curlConfig = array(
                CURLOPT_URL => $url,
                CURLOPT_POST => true,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POSTFIELDS => $data
            );

            $ch = curl_init();
            curl_setopt_array($ch, $curlConfig);
            $response = curl_exec($ch);
            curl_close($ch);

            $jsonResponse = json_decode($response);

            if ($jsonResponse->success === true) {
                // Take action based on the score returned:
                if ($jsonResponse->score >= 0.5) {
                    // Verified
                    $ReturnResponse['captcha'] = $jsonResponse;
                    return $ReturnResponse['captcha'];

                } else {
                    // Not verified
                    $ReturnResponse['status'] = false;
                    $ReturnResponse['message'] = "not a human";
                    $ReturnResponse['captcha'] = $jsonResponse;
                    $this->JSON($ReturnResponse);

                }
            }
            else {
                $ReturnResponse['status'] = false;
                $ReturnResponse['message'] = "captcha failed";
                $this->JSON($ReturnResponse);
            }

        } else {
            $ReturnResponse['status'] = false;
            $ReturnResponse['message'] = "captcha not set";
            $this->JSON($ReturnResponse);

        }
    }


    // Validates your form values to be used in your SQL queries.
    function FormValidation(string $Post, string $Type = "string", bool $Required = true) {
        global $RequiredPasswordLength;

        $ReturnFormValue = [];

        if(isset($_POST[$Post])) {
            if ($Type === "string") {
                if (strlen(trim($_POST[$Post])) > 0) {
                    $ReturnFormValue[$Post] = trim($_POST[$Post]);
                }


            } elseif ($Type === "password") {
                if (strlen($_POST[$Post]) >= $RequiredPasswordLength) {
                    $ReturnFormValue[$Post] = trim($_POST[$Post]);
                }

                $response['data']['password-length'] = $RequiredPasswordLength;


            } elseif ($Type === "phone") {
                if (strlen($this->PhoneNumber($_POST[$Post])) > 0) {
                    $ReturnFormValue[$Post] = $this->PhoneNumber($_POST[$Post]);
                }


            } elseif ($Type === "email") {
                if (filter_var($_POST[$Post], FILTER_VALIDATE_EMAIL)) {
                    $ReturnFormValue[$Post] = strtolower($_POST[$Post]);
                }


            } elseif ($Type === "number") {
                $ReturnFormValue[$Post] = $this->Number($_POST[$Post]);


            } elseif ($Type === "float") {
                $ReturnFormValue[$Post] = $this->Float($_POST[$Post], 8);


            } elseif ($Type === "money") {
                $ReturnFormValue[$Post] = $this->MonetaryNumber($_POST[$Post]);


            } elseif ($Type === "date") {
                $ReturnFormValue[$Post] = strtotime($_POST[$Post]);


            } else {
                $ReturnFormValue['unknown'] = null;
            }


            $ReturnResponse['message'] = $Post;
            return $ReturnFormValue[$Post];


        } else {
            if($Required) {
                $ReturnResponse['status'] = false;
                $ReturnResponse['message'] = $Post;
                $this->JSON($ReturnResponse);
            } else {
                $ReturnFormValue[$Post] = null;
                return $ReturnFormValue[$Post];
            }

        }

        return false;

    }
}

$Create = new Create();


class Show {

    // Convert a floating number into a formatted, money-styled string with commas.
    function MonetaryNumber($Amount, $Convert = true, $Cents = true) {

        $Amount = $Convert ? $Amount / 100 : $Amount;

        $decimals = $Cents ? 2 : 0;

        return number_format(floatval($Amount), $decimals);
    }


    // Generate the proper formatting for a phone number
    function PhoneNumber($PhoneNumber, $URL = false) {
        $number = strval($PhoneNumber);
        $newNumber = str_split($number);

        if(count($newNumber) < 10 || count($newNumber) > 13) {return null;}

        // Build the format backwards since the end is always consistent.

        // 123-456-7890
        array_splice( $newNumber, -4, 0, "-" );
        array_splice( $newNumber, -8, 0, "-" );

        if(count($newNumber) > 12 && count($newNumber) < 17) {
            if($URL) {
                array_splice( $newNumber, -12, 0, "-" );
            } else {
                array_splice( $newNumber, -12, 0, " " );
            }


            // Default
            // +1 123-456-7890
            $offset = -14;

            if(count($newNumber) === 15) {
                // +12 123-456-7890
                $offset = -16;

            } elseif(count($newNumber) === 16) {
                // +123 123-456-7890
                $offset = -17;

            }

            array_splice( $newNumber, $offset, 0, "+" );

        }

        if(!is_null($newNumber)) {
            $newNumber = implode($newNumber);
        }

        return $newNumber;
    }


    // Decrypt sensitive information
    function Secure($ValueToDecrypt) {
        global $cipher;
        global $cipher_iv;
        global $cipher_length;
        global $Create;
        global $encryptionKey;

        // Safely create an encrypted value with AES 128 bit openSSL
        $encrypted_string = strval($ValueToDecrypt);

        // Use openssl_decrypt() function to decrypt the data
        $decryption = openssl_decrypt ($encrypted_string, $cipher, $encryptionKey, 0, $cipher_iv);

        // Return the encrypted string
        return $decryption;
    }


    // Show an uncached image
    function Image($ImageName) {
        $basePath = "/images/";

        $imagePath = $basePath . $ImageName;

        $fullImagePath = $_SERVER['DOCUMENT_ROOT'] . $imagePath;

        if(file_exists($fullImagePath)) {
            $fullImagePath = $imagePath . "?v=" . filemtime($fullImagePath);

            return $fullImagePath;

        } else {
            return $ImageName;

        }
    }
}

$Show = new Show();














/*****************************

Twig functions

 ****************************/

$TwigFunction_Form_Start = new \Twig\TwigFunction('startForm', function ($action, $method, $callback, $class = null) {
    $class = !is_null($class) ? "class='$class'" : "";

    return "<form $class data-api-form action='$action' method='$method' data-callback='$callback' enctype='multipart/form-data'>";
}, ['is_safe' => ['html']]);
$twig->addFunction($TwigFunction_Form_Start);


$TwigFunction_Form_End = new \Twig\TwigFunction('endForm', function () {
    return "</form>";
}, ['is_safe' => ['html']]);
$twig->addFunction($TwigFunction_Form_End);


$TwigFunction_Form_Submit = new \Twig\TwigFunction('submitFormButton', function ($text, $classes = "") {
    return "<button type='submit' class='$classes' data-text='$text'>$text</button>";
}, ['is_safe' => ['html']]);
$twig->addFunction($TwigFunction_Form_Submit);


$TwigFunction_ButtonAPI = new \Twig\TwigFunction('APIButton', function ($action, $method, $text, $callback, $classes = "") {
    return "<button class='$classes' 
            type='button' 
            data-api-btn 
            data-action='$action' 
            data-method='$method' 
            data-text='$text' 
            data-callback='$callback'
            data-processing='false'>$text</button>";
}, ['is_safe' => ['html']]);
$twig->addFunction($TwigFunction_ButtonAPI);














/*****************************

Load the user's custom functions

 ****************************/
require_once("site-functions.php");













/*****************************

Load the site templates. Must be the last thing

 ****************************/

// Render the default template
try {$template = $twig->load('templates/main-template.twig');}
catch (Twig\Error\LoaderError $e)  {die("System Error: Loader");}
catch (Twig\Error\RuntimeError $e) {die("System Error: Runtime");}
catch (Twig\Error\SyntaxError $e)  {die("System Error: Syntax");}