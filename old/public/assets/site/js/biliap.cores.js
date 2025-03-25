/**
 * @author Xanders Samoth
 * @see https://www.linkedin.com/in/xanders-samoth-b2770737/
 * 
 */
// ==================================== NATIVE FUNCTIONS ====================================
/**
 * Dynamically load JS files
 */
function loadPublicJS() {
    $.getScript('/assets/addons/custom/jquery/js/jquery.min.js');
    $.getScript('/assets/addons/custom/jquery/jquery-ui/jquery-ui.min.js');
    $.getScript('/assets/addons/custom/bootstrap/js/bootstrap.min.js');
    $.getScript('/assets/addons/custom/mdb/js/mdb.min.js');
    $.getScript('/assets/addons/custom/cropper/js/cropper.min.js');
    $.getScript('/assets/addons/custom/autosize/js/autosize.min.js');
    $.getScript('/assets/addons/dairy/wow/wow.min.js');
    $.getScript('/assets/addons/dairy/easing/easing.min.js');
    $.getScript('/assets/addons/dairy/waypoints/waypoints.min.js');
    $.getScript('/assets/addons/dairy/owlcarousel/owl.carousel.min.js');
    $.getScript('/assets/addons/dairy/counterup/counterup.min.js');
    $.getScript('/assets/addons/dairy/parallax/parallax.min.js');
    $.getScript('/assets/addons/dairy/lightbox/js/lightbox.min.js');
    $.getScript('/assets/addons/custom/biliap/js/biliap.cores.js');
    $.getScript('/assets/js/scripts.dairy.js');
    $.getScript('/assets/js/scripts.custom.js');
}
function loadAdminJS() {
    $.getScript('/assets/addons/custom/jquery/js/jquery.min.js');
    $.getScript('/assets/addons/custom/jquery/jquery-ui/jquery-ui.min.js');
    $.getScript('/assets/addons/custom/bootstrap/js/bootstrap.min.js');
    $.getScript('/assets/addons/custom/mdb/js/mdb.min.js');
    $.getScript('/assets/addons/custom/cropper/js/cropper.min.js');
    $.getScript('/assets/addons/custom/autosize/js/autosize.min.js');
    $.getScript('/assets/addons/custom/biliap/js/biliap.cores.js');
    $.getScript('/assets/js/scripts.adminator.js');
    $.getScript('/assets/js/scripts.custom.js');
}

/**
 * Gest host name
 * 
 * @param string url 
 */
function get_hostname(url) {
    var m = url.match(/^https?:\/\/[^/]+/);
    return m ? m[0] : null;
}

/**
 * Get cookie by name
 * 
 * @param string cname 
 */
function getCookie(cname) {
    let name = cname + '=';
    let decodedCookie = decodeURIComponent(document.cookie);
    let ca = decodedCookie.split(';');

    for(let i = 0; i <ca.length; i++) {
        let c = ca[i];

        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }

        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }

    return '';
}

// Necessary headers for APIs
var currentHost = $(location).attr('port') ? $(location).attr('protocol') + '//' + $(location).attr('hostname') + ':' + $(location).attr('port') : $(location).attr('protocol') + '//' + $(location).attr('hostname')
var headers = {'Authorization': 'Bearer ' + $('[name="acr-devref"]').attr('content'), 'Accept': 'application/json', 'X-localization': navigator.language};
// CSS files to toggle app theme
const MDB_LIGHT = currentHost + '/assets/addons/mdb/css/mdb.min.css';
const MDB_DARK = currentHost + '/assets/addons/mdb/css/mdb.dark.min.css';
const CUST_LIGHT = currentHost + '/assets/css/style.custom.css';

// ==================================== JQUERY CUSTOM PLUGIN ====================================
(function ($) {
    /**
     * MAKE A BLOCK CLOSABLE
     * 
     * @param _wrapper
     */
    $.fn.closable = function (_wrapper) {
        this.each(function () {
            $(this).on('click', function () {
                $(_wrapper).fadeOut(200);
            });
        });

        return this;
    };

    /**
     * UPLOAD NEWS CROPPED IMAGE
     * 
     * @param _modal
     * @param _inputFile
     * @param _apiUrl
     * @param _entity_id
     */
    $.fn.uploadNewsImage = function (_modal, _inputFile, _apiUrl, _entity_id) {
        this.each(function () {
            var retrievedImage = document.getElementById('retrieved_image1');
            var cropper;

            $(_inputFile).on('change', function (e) {
                var files = e.target.files;
                var done = function (url) {
                    retrievedImage.src = url;
                    var modal = new bootstrap.Modal(document.querySelector(_modal), {
                        keyboard: false
                    });

                    modal.show();
                };

                if (files && files.length > 0) {
                    var reader = new FileReader();

                    reader.onload = function () {
                        done(reader.result);
                    };
                    reader.readAsDataURL(files[0]);
                }
            });

            $(modal).on('shown.bs.modal', function () {
                cropper = new Cropper(retrievedImage, {
                    aspectRatio: 1,
                    viewMode: 3,
                    preview: _modal + ' .preview'
                });

            }).on('hidden.bs.modal', function () {
                cropper.destroy();

                cropper = null;
            });

            $(_modal + ' #crop').click(function () {
                // Ajax loading image to tell user to wait
                $(this).attr('src', '/assets/img/ajax-loading.gif');

                var canvas = cropper.getCroppedCanvas({
                    width: 700,
                    height: 700
                });

                canvas.toBlob(function (blob) {
                    URL.createObjectURL(blob);
                    var reader = new FileReader();

                    reader.readAsDataURL(blob);
                    reader.onloadend = function () {
                        var base64_data = reader.result;
                        var entity_id = document.getElementById(_entity_id).value;
                        var apiUrl = _apiUrl;
                        var datas = JSON.stringify({ 'news_id' : entity_id, 'image_64' : base64_data });

                        modal.hide();

                        $.ajax({
                            type: 'PUT',
                            url: apiUrl,
                            data: datas,
                            success: function (res) {
                                $(this).attr('src', res);
                                window.location.reload();
                            },
                            error: function (xhr, error, status_description) {
                                console.log(xhr.responseJSON);
                                console.log(xhr.status);
                                console.log(error);
                                console.log(status_description);
                            }
                        });
                    };
                });
            });        
        });

        return this;
    };

    /**
     * UPLOAD USER CROPPED IMAGE
     * 
     * @param _retrievedImage
     * @param _modalId
     * @param _modal
     * @param _inputFile
     * @param _api_token
     * @param _apiUrl
     * @param _entity_id
     */
    $.fn.uploadUserImage = function (_retrievedImage, _modalId, _modal, _inputFile, _apiUrl, _entity_id) {
        this.each(function () {
            var retrievedImage = document.getElementById(_retrievedImage);
            var _this = this;
            var cropper;

            $(_inputFile).on('change', function (e) {
                var files = e.target.files;
                var done = function (url) {
                    retrievedImage.src = url;
                    var modal = new bootstrap.Modal(document.getElementById(_modalId), {
                        keyboard: false
                    });

                    modal.show();
                };

                if (files && files.length > 0) {
                    var reader = new FileReader();

                    reader.onload = function () {
                        done(reader.result);
                    };
                    reader.readAsDataURL(files[0]);
                }
            });

            $(_modal).on('shown.bs.modal', function () {
                cropper = new Cropper(retrievedImage, {
                    aspectRatio: 1,
                    viewMode: 3,
                    preview: _modal + ' .preview'
                });

            }).on('hidden.bs.modal', function () {
                cropper.destroy();

                cropper = null;
            });

            $(_modal + ' #crop').click(function () {
                // Ajax loading image to tell user to wait
                $(_this).attr('src', currentHost + '/assets/img/ajax-loading.gif');

                var canvas = cropper.getCroppedCanvas({
                    width: 700,
                    height: 700
                });

                canvas.toBlob(function (blob) {
                    URL.createObjectURL(blob);
                    var reader = new FileReader();

                    reader.readAsDataURL(blob);
                    reader.onloadend = function () {
                        var base64_data = reader.result;
                        var entity_id = document.getElementById(_entity_id).value;
                        var apiUrl = _apiUrl;
                        var datas = JSON.stringify({ 'user_id' : entity_id, 'image_64' : base64_data });

                        modal.hide();

                        $.ajax({
                            type: 'PUT',
                            contentType: 'application/json',
                            url: apiUrl,
                            dataType: 'json',
                            data: datas,
                            success: function (res) {
                                $(this).attr('src', res);
                                window.location.reload();
                            },
                            error: function (xhr, error, status_description) {
                                console.log(xhr.responseJSON);
                                console.log(xhr.status);
                                console.log(error);
                                console.log(status_description);
                            }
                        });
                    };
                });
            });        
        });

        return this;
    };

    /**
     * LOAD OTHER USER IMAGE
     * 
     * @param _modal
     * @param _inputFile
     * @param _api_token
     * @param _apiUrl
     * @param _entity_id
     */
    $.fn.loadOtherUserImage = function (_modal, _inputFile, _loadedImage, _dataToSend) {
        this.each(function () {
            var loadedImage = document.querySelector(_loadedImage);
            var retrievedImage = document.getElementById('retrieved_image2');

            $(_inputFile).on('change', function (e) {
                var files = e.target.files;
                var done = function (url) {
                    retrievedImage.src = url;
                    var modal = new bootstrap.Modal(document.getElementById(_modal), {
                        keyboard: false
                    });

                    modal.show();
                };

                if (files && files.length > 0) {
                    var reader = new FileReader();

                    reader.onload = function () {
                        done(reader.result);
                    };
                    reader.readAsDataURL(files[0]);
                }
            });

            $(modal).on('shown.bs.modal', function () {
                cropper = new Cropper(retrievedImage, {
                    aspectRatio: 1,
                    viewMode: 3,
                    preview: _modal + ' .preview'
                });

            }).on('hidden.bs.modal', function () {
                cropper.destroy();

                cropper = null;
            });

            $(_modal + ' .ratio').click(function () {
                cropper = new Cropper(retrievedImage, {
                    aspectRatio: $(thi_values).attr('acr-img-ratio'),
                    viewMode: 3,
                    preview: _modal + ' .preview'
                });
            });

            $(_modal + ' #crop').click(function () {
                var canvas; 

                if ($(_modal + ' .ratio_value').attr('acr-img-ratio-value') == '1') {
                    canvas = cropper.getCroppedCanvas({
                        width: 700,
                        height: 700
                    });
                }

                if ($(_modal + ' .ratio_value').attr('acr-img-ratio-value') == '4 / 3') {
                    canvas = cropper.getCroppedCanvas({
                        width: 1280,
                        height: 960
                    });
                }

                if ($(_modal + ' .ratio_value').attr('acr-img-ratio-value') == '4 / 5') {
                    canvas = cropper.getCroppedCanvas({
                        width: 1134,
                        height: 1417
                    });
                }

                if ($(_modal + ' .ratio_value').attr('acr-img-ratio-value') == '16 / 9') {
                    canvas = cropper.getCroppedCanvas({
                        width: 1280,
                        height: 720
                    });
                }

                canvas.toBlob(function (blob) {
                    URL.createObjectURL(blob);
                    var reader = new FileReader();

                    reader.readAsDataURL(blob);
                    reader.onloadend = function () {
                        var base64_data = reader.result;

                        $(loadedImage).attr('src', base64_data);
                        $(_dataToSend).attr('value', base64_data);
                    };
                });
            });
        });

        return this;
    };

    /**
     * MULTILINE TRUNCATION
     * 
     * @param _paragraph
     * @param _numberOfLines
     * @param _rollButton
     */
    $.fn.multilineTruncation = function (_paragraph, _numberOfLines, _rollButton) {
        this.each(function () {
            $(this).find(_paragraph).ellipsis({
                lines: _numberOfLines,             // force ellipsis after a certain number of lines. Default is 'auto'
                ellipClass: 'ellip',  // class used for ellipsis wrapper and to namespace ellip line
                responsive: true      // set to true if you want ellipsis to update on window resize. Default is false
            });

            var _this = $(this).find(_paragraph).get(0);

            $(this).find(_rollButton).on('click', function () {
                $(_this).ellipsis({ellipClass: '_'});

                return false;
            });
        });

        return this;
    };

    /**
     * ANIMATE COUNTER
     * 
     * @param _duration
     * 
     * Duration is in milliseconds (E.g. : 4000)
     */
    $.fn.animateCounter = function (_duration) {
        this.each(function () {
            $(this).prop('Counter', 0).animate({Counter: $(this).text()}, {
                duration: _duration,
                easing: 'swing',
                step: function (now) {
                    $(this).text(Math.ceil(now));
                }    
            });    
        });

        return this;
    };

    /**
     * FORMAT NUMBERS THAT HAVE MORE THAN 3 DIGITS
     */
    $.fn.numberFormatter = function () {
        this.each(function () {
            if (this !== null) {
                var num = parseInt($(this).html());
                var si = [{ value: 1, symbol: '' },
                { value: 1E3, symbol: 'k' },
                { value: 1E6, symbol: 'M' },
                { value: 1E9, symbol: 'G' },
                { value: 1E12, symbol: 'T' },
                { value: 1E15, symbol: 'P' },
                { value: 1E18, symbol: 'E' }];
                var rx = /\.0+$|(\.[0-9]*[1-9])0+$/;
                var i;

                for (i = si.length - 1; i > 0; i--) {
                    if (num >= si[i].value) {
                        break;
                    }
                }

                $(this).html((num / si[i].value).toFixed(1).replace(rx, '$1') + si[i].symbol);
            }
        });

        return this;
    };
})(jQuery);