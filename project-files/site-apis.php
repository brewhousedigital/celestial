<?php


// Contact us form example
$Router->post('/api/contact-form/', function () {
    // Set your globals first
    global $Create;


    // Set response defaults in case of failure.
    $response = [];
    $formValues = [];


    // Set your validations if you have any
    $formValues['name']    = strip_tags($Create->FormValidation('name'));
    $formValues['email']   = $Create->FormValidation('email', 'email');
    $formValues['message'] = strip_tags($Create->FormValidation('message', 'string'));
    $formValues['date']    = date("M d, Y g:i a");


    // API Actions on the validated data

    // Recaptcha check
    $formValues['recaptcha'] = $Create->Recaptcha('recaptcha_response');

    // Replacing dynamic email values
    $EmailContent = [
        ["__USERNAME__", $formValues['name']],
        ["__USEREMAIL__", $formValues['email']],
        ["__MESSAGE__", $formValues['message']],
        ["__SUBMITTED__", $formValues['date']]
    ];


    // Send mail
    $mailgunResponse = $Create->Mail(
        "team@your-website.com",
        "New Message from the Your Website",
        "contact",
        $EmailContent,
        "mail"
    );


    // Complete the API
    $response['status'] = true;
    $response['message'] = "message sent";
    $Create->JSON($response);
});