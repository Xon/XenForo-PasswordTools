$(document).ready(function(){var a=["","easy","medium","hard","brutal"],e=!1,r=$("#passwordCompareBox"),s=$("#passwordReadyBox"),n=$("#passwordStrengthPhrase"),t=$("#passwordStrengthMeter"),o=$('input[data-password-check="1"]'),d=$('input[data-password-compare="1"]');if(o.length&&d.length){var i=function(){var a=d.val(),e=o.val();a&&a===e?r.addClass("valid"):r.removeClass("valid")};o.on("input",i),d.on("input",i)}o.on("input",function(o){if("undefined"!=typeof zxcvbn){var d=$(this).val();if(d?(n.show(),r.show(),s.show()):(n.hide(),r.hide(),s.hide()),s.removeClass("valid"),e=!1,!1,d.length&&d.length>=pwd_minlength){!0;var i=zxcvbn(d,pwd_checklist);pwd_forcepwd&&$.each(i.match_sequence,function(a,r){"user_inputs"===r.dictionary_name&&(e=!0)}),e?(!1,t.removeClass(),n.removeClass(),n.empty().append(pwd_strings[6])):(t.removeClass().addClass(a[i.score]),n.removeClass().addClass(a[i.score]),n.empty().append(pwd_strings[i.score]),i.score>=pwd_minstrength?(!0,$passwordReadBox.addClass("valid")):!1)}else t.removeClass(),n.removeClass(),n.empty().append(pwd_strings[5]),!1}});var p=o.closest("form");p.bind("AutoValidationError",function(a){"ajaxData"in a&&"error"in a.ajaxData&&a.ajaxData.error&&(!function(a,e){a.find(".errorPanel").remove();var r=$("<ol/>");$.each(e,function(a,e){r.append($("<li/>",{text:e}))});var s=$("<div/>",{class:"errorPanel"}).append($("<h3/>",{class:"errorHeading",text:pwd_errorstrings[3]+":"}),$("<div/>",{class:"baseHtml errors"}).append(r));a.prepend(s).xfActivate()}(p,a.ajaxData.error),a.preventDefault())})});