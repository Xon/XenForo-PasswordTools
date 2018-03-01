/**
 * KL_PasswordTools_PasswordStrengthMeter
 *
 *  @author: Katsulynx
 *  @last_edit: 15.08.2015
 */

$(document).ready(function() {
    var pwd_classes = ['','easy', 'medium', 'hard', 'brutal'];
    var pwd_pass = false;
    var pwd_rejected = false;
    var pwd_length = false;

    $('#passwordStrengthPhrase').prev().on('input', function(event) {
        var pwd = $(this).val();
        $('#passwordReadyBox').removeClass('valid');
            pwd_rejected = false;
            pwd_length = false;

        // Check if the required password length is met
        if(pwd.length && pwd.length >= pwd_minlength) {
            pwd_length = true;

            // Eval the password
            var pwd_strength = zxcvbn(pwd,pwd_checklist);

            if(pwd_forcepwd) {
                // Reject password if we have a hit on the blacklist
                $.each(pwd_strength.match_sequence, function(key,data) {
                    if(data.dictionary_name === "user_inputs") {
                        pwd_rejected = true;
                    }
                });
            }
            if(pwd_rejected) {
                pwd_pass = false;
                $('#passwordStrengthMeter, #passwordStrengthPhrase').removeClass();
                $('#passwordStrengthPhrase').empty().append(pwd_strings[6]);
            }
            else {
                $('#passwordStrengthMeter, #passwordStrengthPhrase').removeClass().addClass(pwd_classes[pwd_strength.score]);
                $('#passwordStrengthPhrase').empty().append(pwd_strings[pwd_strength.score]);

                // Check for minimum required strength
                if(pwd_strength.score >= pwd_minstrength) {
                    pwd_pass = true;
                    $('#passwordReadyBox').addClass('valid');
                }
                else {
                    pwd_pass = false;
                }
            }
        }
        else {
            $('#passwordStrengthMeter, #passwordStrengthPhrase').removeClass();
            $('#passwordStrengthPhrase').empty().append(pwd_strings[5]);
            pwd_pass = false;
        }
    });

    $('#passwordStrengthPhrase, #passwordCompareBox').prev().on('input', function() {
        var pwd = [
                $('#passwordCompareBox').prev().val(),
                $('#passwordStrengthPhrase').prev().val()
            ]
        if(pwd[0] && pwd[0] === pwd[1]) {
            $('#passwordCompareBox').addClass('valid');
        }
        else {
            $('#passwordCompareBox').removeClass('valid');
        }
    });

    $('input[type=submit]').click(function(event) {
        if(!pwd_pass) {
            event.preventDefault();
            $('.errorPanel').remove();
            $('.pageContent form').prepend(
                jQuery('<div/>', {'class': 'errorPanel', id: 'errorPanel'}).append(
                    jQuery('<h3/>', {'class': 'errorHeading', text: pwd_errorstrings[3]+':'}),
                    jQuery('<div/>', {'class': 'baseHtml errors'}).append(
                        jQuery('<ol/>').append(
                            jQuery('<li/>', {text: (!pwd_length ? pwd_errorstrings[0] : (pwd_rejected ? pwd_errorstrings[1] : pwd_errorstrings[2]))})
                        )
                    )
                )
            );
            $('html, body').animate({ scrollTop: $("#errorPanel").offset().top - 40}, 'fast');
        }
    });
});