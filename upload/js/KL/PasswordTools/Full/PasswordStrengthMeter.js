$(document).ready(function() {
    var pwd_classes = ['', 'easy', 'medium', 'hard', 'brutal'];
    var pwd_pass = false;
    var pwd_rejected = false;
    var pwd_length = false;

    var $passwordCompareBox = $('#passwordCompareBox');
    var $passwordReadBox = $('#passwordReadyBox');
    var $passwordStrength = $('#passwordStrengthPhrase');
    var $passwordMeter = $('#passwordStrengthMeter');
    var $passwordInput = $('input[data-password-check="1"]');
    var $passwordConfirm = $('input[data-password-compare="1"]');

    if ($passwordInput.length && $passwordConfirm.length) {
        var comparePasswordSame = function () {
            var pwd0 = $passwordConfirm.val();
            var pwd1 = $passwordInput.val();
            if (pwd0 && pwd0 === pwd1) {
                $passwordCompareBox.addClass('valid');
            } else {
                $passwordCompareBox.removeClass('valid');
            }
        };
        $passwordInput.on('input', comparePasswordSame);
        $passwordConfirm.on('input', comparePasswordSame);
    }


    $passwordInput.on('input', function (event) {
        if (typeof zxcvbn !== "undefined") {
            var pwd = $(this).val();
            if (pwd) {
                $passwordStrength.show();
            } else {
                $passwordStrength.hide();
            }
            $passwordReadBox.removeClass('valid');
            pwd_rejected = false;
            pwd_length = false;

            // Check if the required password length is met
            if (pwd.length && pwd.length >= pwd_minlength) {
                pwd_length = true;

                // Eval the password
                var pwd_strength = zxcvbn(pwd, pwd_checklist);

                if (pwd_forcepwd) {
                    // Reject password if we have a hit on the blacklist
                    $.each(pwd_strength.match_sequence, function (key, data) {
                        if (data.dictionary_name === "user_inputs") {
                            pwd_rejected = true;
                        }
                    });
                }
                if (pwd_rejected) {
                    pwd_pass = false;
                    $passwordMeter.removeClass();
                    $passwordStrength.removeClass();
                    $passwordStrength.empty().append(pwd_strings[6]);
                }
                else {
                    $passwordMeter.removeClass().addClass(pwd_classes[pwd_strength.score]);
                    $passwordStrength.removeClass().addClass(pwd_classes[pwd_strength.score]);
                    $passwordStrength.empty().append(pwd_strings[pwd_strength.score]);

                    // Check for minimum required strength
                    if (pwd_strength.score >= pwd_minstrength) {
                        pwd_pass = true;
                        $passwordReadBox.addClass('valid');
                    }
                    else {
                        pwd_pass = false;
                    }
                }
            }
            else {
                $passwordMeter.removeClass();
                $passwordStrength.removeClass();
                $passwordStrength.empty().append(pwd_strings[5]);
                pwd_pass = false;
            }
        }
    });

    var createErrorPanel = function($form, errors) {
        $form.find('.errorPanel').remove();


        var $list = $('<ol/>');
        $.each(errors, function(i, errorText) { $list.append($('<li/>', {text: errorText } )); });

        var $errorPanel = $('<div/>', {'class': 'errorPanel'}).append(
            $('<h3/>', {'class': 'errorHeading', text: pwd_errorstrings[3] + ':'}),
            $('<div/>', {'class': 'baseHtml errors'}).append($list)
        );
        $form.prepend($errorPanel).xfActivate();
        //$('html, body').animate({scrollTop: $errorPanel.offset().top - 40}, 'fast');
    };


    var $form = $passwordInput.closest('form');
    /*
    $form.bind('AutoValidationBeforeSubmit', function(e)
    {
        // called during form-validation stage
        if (!$passwordInput.prop('disabled') && !pwd_pass && typeof zxcvbn !== "undefined") {
            e.preventSubmit = true;
            e.preventDefault();

            createErrorPanel($form, [
                (!pwd_length ? pwd_errorstrings[0] : (pwd_rejected ? pwd_errorstrings[1] : pwd_errorstrings[2]))
            ]);
        }
    });
    */
    $form.bind('AutoValidationError', function(e)
    {
        // called after results come back
        if (('ajaxData' in e) &&
            ('error' in e.ajaxData) &&
            e.ajaxData.error)
        {
            createErrorPanel($form, e.ajaxData.error);
            e.preventDefault();
        }
    });
});