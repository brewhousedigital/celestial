<?php


// Security. Prevents man-in-the-middle attacks on non-https sites.
header("Strict-Transport-Security:max-age=63072000");


// Load the fun vendor files.
require_once $_SERVER['DOCUMENT_ROOT'] . '/../vendor/autoload.php';


// Define default variables to use across the site
$data = [];


// Load the project information.
require_once $_SERVER['DOCUMENT_ROOT'] . '/../project-files/framework-setup.php';


// Load the framework core code. The super powerful AI, Machine Learning, Nano-bot, Super Quantum Computing Engine.
require_once $_SERVER['DOCUMENT_ROOT'] . '/../project-files/framework-core.php';


// Load the global site data such as user info, page info, site info.
require_once $_SERVER['DOCUMENT_ROOT'] . '/../project-files/site-data.php';


// Load the website URLs.
require_once $_SERVER['DOCUMENT_ROOT'] . '/../project-files/site-routes.php';


// Load the website APIs.
require_once $_SERVER['DOCUMENT_ROOT'] . '/../project-files/site-apis.php';


// render the site
echo $template->render($data);