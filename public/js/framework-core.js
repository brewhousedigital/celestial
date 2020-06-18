// Enable recaptcha
let recaptchaPublishableKey = document.querySelector("meta[name='recaptchaKey']").getAttribute("content");


// Enable Google Analytics
let googleAnalyticsKey = document.querySelector("meta[name='googleAnalyticsKey']").getAttribute("content");
if(googleAnalyticsKey.length > 0) {
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', googleAnalyticsKey);
}


function get() {
    const ul = document.getElementById('authors');
    const url = 'https://randomuser.me/api/?results=10';

    fetch(url)
        .then((resp) => resp.json())
        .then(function (data) {
            console.log(data);
            let authors = data.results;
            authors.map(function (author) {
                ul.insertAdjacentHTML("beforeend", "<li>" + author.email + "</li>");
            });
        })
        .catch(function (error) {
            console.log(error);
        });
}



function dataAPI(element, data, button) {

    console.log("starting api request");

    // Set the defaults
    let thisMethod = null;
    let thisAction = null;
    let elementTagName = element.tagName.toLowerCase();
    let originalText = button.getAttribute("data-text");

    // If its a form, get the standard html attributes. if its a button, get the data attributes.
    if(elementTagName === "form") {
        thisMethod = element.getAttribute("method").toLowerCase();
        thisAction = element.getAttribute("action");

    } else if(elementTagName === "button") {
        thisMethod = element.getAttribute("data-method").toLowerCase();
        thisAction = element.getAttribute("data-action");

    } else {
        return false;
    }


    // Get the callback function
    let callback = element.getAttribute("data-callback");
    console.log(callback);
    let callbackFunction = window[callback];


    // Only allow these methods. I know that DELETE and PUT aren't semantic, but yolo its 2020.
    let allowedMethods = ["get", "post", "delete", "put"];
    if(!allowedMethods.includes(thisMethod)) {return false;}


    // Toggle button loading state
    dataAPIButtonState(button, originalText, "off");


    // Create our request constructor with all the parameters we need
    let request = new Request(thisAction, {
        method: thisMethod,
        body: data,
        headers: new Headers()
    });

    // Fire that request over to the server!!
    fetch(request)

        // Convert to JSON...
        .then((response) => response.json())

        // Now do all your fun stuff here.
        .then(function (response) {
            // Handle response we get from the API
            console.log(response);

            // Completed fetch
            dataAPIButtonState(button, originalText, "on");

            // Verify its a defined function
            if (typeof callbackFunction === "function") {
                console.log(callbackFunction(response, button, originalText));
            }
        })

        // If any errors, log em to the console
        .catch(function (error) {
            console.log(error);
            //location.reload();

            setTimeout(function() {
                dataAPIButtonState(button, originalText, "on");
            }, 10000);
        });
}


function dataAPIButtonState(element, originalText, status) {
    if(status === "off") {
        let width = element.offsetWidth;
        element.setAttribute("disabled", true);
        element.setAttribute("data-processing", "true");
        element.style.width = width + "px";
        element.innerHTML = loadingText;
    } else if(status === "on") {
        element.removeAttribute("disabled");
        element.setAttribute("data-processing", "false");
        element.style.width = "";
        element.innerHTML = originalText;
    }
}


let allAPIForms = document.querySelectorAll("[data-api-form]");

for (let i = 0; i < allAPIForms.length; i++) {
    allAPIForms[i].addEventListener("submit", function(e) {
        e.preventDefault();

        let element = this;
        let action = element.getAttribute("action");
        action = action.replace(/[^a-z0-9/]/gi,'_');
        console.log(action);
        let data = new FormData(element);
        let button = element.querySelector("button[type='submit']");

        // Fetch request
        if(recaptchaPublishableKey !== undefined && recaptchaPublishableKey.length > 0) {
            grecaptcha.ready(function() {
                grecaptcha.execute(recaptchaPublishableKey, {action: action}).then(function(token) {
                    //let recaptchaResponse = document.getElementById('recaptchaResponse');
                    //recaptchaResponse.value = token;
                    data.append("recaptcha_response", String(token));

                    dataAPI(element, data, button);
                });
            });
        } else {
            dataAPI(element, data, button);
        }
    });
}


let allAPIButtons = document.querySelectorAll("[data-api-btn]");

for (let i = 0; i < allAPIButtons.length; i++) {
    allAPIButtons[i].addEventListener("click", function(e) {
        let element = this;
        let data = new FormData();
        let button = element;

        // Fetch request
        dataAPI(element, data, button);
    });
}










/*****************************

 Fun functions

 ****************************/

// Quickly create elements
function createEl(el, text) {
    let element = document.createElement(el);
    let elementText = document.createTextNode(text);
    element.appendChild(elementText);
    return element;
}






