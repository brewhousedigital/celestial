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

