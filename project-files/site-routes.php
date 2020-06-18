<?php


// Home
$Router->get('/', function () {
    global $data;

    $data['page']['title'] = 'Home';
    $data['page']['template'] = 'homepage';
});


// About Page
$Router->get('/about/', function() {
    global $data;

    $data['page']['title'] = 'About';
    $data['page']['template'] = 'about';
});


// Contact Page
$Router->get('/contact/', function() {
    global $data;

    $data['page']['title'] = 'Contact';
    $data['page']['template'] = 'contact';
});