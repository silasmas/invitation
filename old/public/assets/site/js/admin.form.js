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



(function($) {
	'use strict';
	// Get the form.
	var form = $('#event-form');
	var btn = document.querySelector('#btform');

	// Get the messages div.
	var formMessages = $('#form-messages');

	// Set up an event listener for the contact form.
	$(form).submit(function(e) {
		// Stop the browser from submitting the form.
		e.preventDefault();
		btn.setAttribute('disabled', 'true');
		btn.innerHTML ="En cour d'envoi";
		// Serialize the form data.
		//var formData = new FormData(this);//.serialize();

		// Submit the form using AJAX.
		$.ajax({
			type: 'POST',
            contentType: false,
            processData:false,
			url: $(form).attr('action'),
			data: new FormData(this)
		})
			.done(function(response) {
				btn.removeAttribute('disabled');
				btn.innerHTML ='Envoyer';
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
                    const err=Object.entries(response.datas);
                    console.log(err);
                    for (const [key, value] of err) {
                        console.log(key, value[0]);
                        document.getElementById(key).insertAdjacentHTML('afterend',`<div style="color:red;">${value[0]}</div>`)

                      }

				}
			})
			.fail(function(data) {
				btn.removeAttribute('disabled');
				btn.innerHTML ='Envoyer';
				// Make sure that the formMessages div has the 'error' class.
				$(formMessages).removeClass('success');
				$(formMessages).addClass('error');

				// Set the message text.
				if (data.responseText !== '') {
                    // console.log(data.datas)
					$(formMessages).text(data.msg);
				} else {
					$(formMessages).text('Oops! An error occured and your message could not be sent.');
				}
			});
	});
})(jQuery);
(function($) {
	'use strict';
	// Get the form.
	var form = $('#eventUpdate-form');
	var btn = document.querySelector('#btform');

	// Get the messages div.
	var formMessages = $('#form-messages');

	// Set up an event listener for the contact form.
	$(form).submit(function(e) {
		// Stop the browser from submitting the form.
		e.preventDefault();
		btn.setAttribute('disabled', 'true');
		btn.innerHTML ="En cour d'envoi";
		// Serialize the form data.
		//var formData = new FormData(this);//.serialize();
        
		// Submit the form using AJAX.
		$.ajax({
			type: 'POST',
            contentType: false,
            processData:false,
			url: $(form).attr('action'),
			data: new FormData(this)
		})
			.done(function(response) {
				btn.removeAttribute('disabled');
				btn.innerHTML ='Envoyer';
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
                    actualiser();
					// $('#nom, #email, #phone_number, #message').val('');
				} else {
                    // const err=Object.entries(response.datas);
                    swal({
						title: response.msg,
						icon: 'error'
					});
                    console.log(err);
                    for (const [key, value] of err) {
                        console.log(key, value[0]);
                        document.getElementById(key).insertAdjacentHTML('afterend',`<div style="color:red;">${value[0]}</div>`)

                      }
                      
				}
			})
			.fail(function(data) {
				btn.removeAttribute('disabled');
				btn.innerHTML ='Envoyer';
				// Make sure that the formMessages div has the 'error' class.
				$(formMessages).removeClass('success');
				$(formMessages).addClass('error');
                swal({
                    title: response.msg,
                    icon: 'error'
                });
				// Set the message text.
				if (data.responseText !== '') {
                    // console.log(data.datas)
					$(formMessages).text(data.msg);
				} else {
					$(formMessages).text('Oops! An error occured and your message could not be sent.');
				}
			});
	});
})(jQuery);


function deletEvent(id, url) {

    swal({
        title: "Attention suppression",
        text: "Etes -vous prêt de supprimer cette information?",
        icon: 'warning',
        dangerMode: true,
        buttons: {
            cancel: 'Non',
            delete: 'OUI'
        }
    }).then(function(willDelete) {
        if (willDelete) {

            $.ajax({
                url: url + "/" + id,
                method: "GET",
                data: {
                    'idv': id
                },
                success: function(data) {
                    //  load('#tab-session');
                    if (!data.reponse) {
                        swal({
                            title: data.msg,
                            icon: 'error'
                        })

                    } else {
                        swal({
                            title: data.msg,
                            icon: 'success'
                        })
                        actualiser();
                    }
                },
            });
        } else {
            swal({
                title: "Suppression annuler",
                icon: 'error'
            })
        }
    });
}
function actualiser() {
    location.reload();
}
