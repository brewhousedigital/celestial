// Test API
function orderForm(response, el, text) {
    // Validate response. Must be an array.
    if(response === undefined || response === null) {return "no data to process";}

    if(response['status']) {
        el.innerText = "Order Sent ✔";

        document.getElementById("formName").value = "";
        document.getElementById("formEmail").value = "";
        document.getElementById("formMessage").value = "";

        setTimeout(function() {
            el.innerText = text;
        }, 5000);

    } else {
        el.innerText = "Unable to send ✖";

        setTimeout(function() {
            el.innerText = text;
        }, 5000);

    }
}


function testAPIExample(response, el, text) {
    let responseStatusContainer = document.getElementById("api-test-response-field-status");
    let responseMessageContainer = document.getElementById("api-test-response-field-message");

    document.querySelector(".api-test-response-field-box").style.opacity = "1";

    if(response['status']) {
        //el.innerText = "✔";
        alert("Success!");
        responseStatusContainer.innerText = 'true';
        responseMessageContainer.innerText = response['message'];

        setTimeout(function() {
            //el.innerText = text;
        }, 5000);

    } else {
        el.innerText = "✖";
        alert("The input field for 'name' is missing!");
        responseStatusContainer.innerText = 'false';
        responseMessageContainer.innerText = 'Enter in your name!';

        setTimeout(function() {
            el.innerText = text;
        }, 5000);

    }
}










function contactForm(response, el, text) {
    // Validate response. Must be an array.
    if(response === undefined || response === null) {return "no data to process";}

    if(response['status']) {
        el.innerText = "Message Sent ✔";

        document.getElementById("formName").value = "";
        document.getElementById("formEmail").value = "";
        document.getElementById("formMessage").value = "";

        setTimeout(function() {
            el.innerText = text;
        }, 5000);

    } else {
        el.innerText = "Unable to send ✖";

        setTimeout(function() {
            el.innerText = text;
        }, 5000);

    }
}

