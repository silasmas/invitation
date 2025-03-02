/**
 *
 * -----------------------------------------------------------------------------
 *
 * Template : Bizup - Creative Agency & Portfolio HTML Template
 * Author : reacthemes
 * Author URI : https://reactheme.com/
 *
 * -----------------------------------------------------------------------------
 *
 **/

(function ($) {
    'use strict';
    // Get the form.
    var form = $('#contact-form');
    var btn = document.querySelector('#btnmsg');

    // Get the messages div.
    var formMessages = $('#form-messages');

    // Set up an event listener for the contact form.
    $(form).submit(function (e) {
        // Stop the browser from submitting the form.
        e.preventDefault();
        btn.setAttribute('disabled', 'true');
        btn.innerHTML = "En cour d'envoi";
        // Serialize the form data.
        var formData = $(form).serialize();

        // Submit the form using AJAX.
        $.ajax({
                type: 'POST',
                url: $(form).attr('action'),
                data: formData
            })
            .done(function (response) {
                btn.removeAttribute('disabled');
                btn.innerHTML = 'Envoyer';
                if (response.reponse) {
                    // Make sure that the formMessages div has the 'success' class.
                    $(formMessages).removeClass('error');
                    $(formMessages).addClass('success');
                    swal({
                        title: response.msg,
                        icon: 'success'
                    });
                    // Set the message text.
                    $(formMessages).text(response.msg);
                    // Clear the form.
                    $(form)[0].reset();
                    // $('#nom, #email, #phone_number, #message').val('');
                } else {
                    swal({
                        title: response.msg,
                        icon: 'error'
                    });
                }
            })
            .fail(function (data) {
                btn.removeAttribute('disabled');
                btn.innerHTML = 'Envoyer';
                // Make sure that the formMessages div has the 'error' class.
                $(formMessages).removeClass('success');
                $(formMessages).addClass('error');

                // Set the message text.
                if (data.responseText !== '') {
                    $(formMessages).text(data.msg);
                } else {
                    $(formMessages).text('Oops! An error occured and your message could not be sent.');
                }
            });
    });
})(jQuery);

(function ($) {
    'use strict';
    // Get the form.
    var form = $('#newsletter');
    var btn = document.querySelector('#btnNews');
    // Set up an event listener for the contact form.
    $(form).submit(function (e) {
        // Stop the browser from submitting the form.
        e.preventDefault();
        // Serialize the form data.
        var formData = $(form).serialize();

        btn.setAttribute('disabled', 'true');
        btn.value = "...";
        // Submit the form using AJAX.
        $.ajax({
                type: 'POST',
                url: $(form).attr('action'),
                data: formData
            })
            .done(function (response) {
                btn.removeAttribute('disabled');
                btn.value = 'Envoyer';
                if (response.reponse) {
                    swal({
                        title: response.msg,
                        icon: 'success'
                    });
                    // Clear the form.
                    $(form)[0].reset();
                    // $('#nom, #email, #phone_number, #message').val('');
                } else {
                    swal({
                        title: response.msg,
                        icon: 'error'
                    });
                }
            })
            .fail(function (data) {
                btn.removeAttribute('disabled');
                btn.value = 'Envoyer';
                // Set the message text.
                if (data.responseText !== '') {
                    swal({
                        title: response.msg,
                        icon: 'error'
                    });
                } else {
                    swal({
                        title: response.msg,
                        icon: "Oops! Une erreur s'est produite et votre message n'a pas pu être envoyé."
                    });
                }
            });
    });
})(jQuery);

(function ($) {
    'use strict';
    // Get the form.
    var form = $('#updatUser');
    var btn = document.querySelector('#btnUpdateUser');
    // Set up an event listener for the contact form.
    $(form).submit(function (e) {
        // Stop the browser from submitting the form.
        e.preventDefault();
        // Serialize the form data.
        var formData = $(form).serialize();

        btn.setAttribute('disabled', 'true');
        btn.innerHTML = "En cour d'envoi";
        // Submit the form using AJAX.
        $.ajax({
                type: 'POST',
                url: $(form).attr('action'),
                data: formData
            })
            .done(function (response) {
                btn.removeAttribute('disabled');
                btn.innerHTML = "Modifier";
                if (response.reponse) {
                    swal({
                        title: response.msg,
                        icon: 'success'
                    });
                    // Clear the form.
                    location.reload();
                } else {
                    swal({
                        title: response.msg,
                        icon: 'error'
                    });
                }
            })
            .fail(function (data) {
                btn.removeAttribute('disabled');
                btn.innerHTML = "Modifier";
                // Set the message text.
                if (data.responseText !== '') {
                    swal({
                        title: response.msg,
                        icon: 'error'
                    });
                } else {
                    swal({
                        title: response.msg,
                        icon: "Oops! Une erreur s'est produite et votre message n'a pas pu être envoyé."
                    });
                }
            });
    });
})(jQuery);

function load(id) {
    $(id).children('.ibox-content').toggleClass('sk-loading');
}
