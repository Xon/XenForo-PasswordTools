$(document).ready(function(){var a=["","easy","medium","hard","brutal"],e=!1,r=$("#passwordCompareBox"),s=$("#passwordReadyBox"),n=$("#passwordStrengthPhrase"),t=$("#passwordStrengthMeter"),o=$('input[data-password-check="1"]'),d=$('input[data-password-compare="1"]');if(o.length&&d.length){var i=function(){var a=d.val(),e=o.val();a&&a===e?r.addClass("valid"):r.removeClass("valid")};o.on("input",i),d.on("input",i)}o.on("input",function(r){if("undefined"!=typeof zxcvbn){var o=$(this).val();if(o?n.show():n.hide(),s.removeClass("valid"),e=!1,!1,o.length&&o.length>=pwd_minlength){!0;var d=zxcvbn(o,pwd_checklist);pwd_forcepwd&&$.each(d.match_sequence,function(a,r){"user_inputs"===r.dictionary_name&&(e=!0)}),e?(!1,t.removeClass(),n.removeClass(),n.empty().append(pwd_strings[6])):(t.removeClass().addClass(a[d.score]),n.removeClass().addClass(a[d.score]),n.empty().append(pwd_strings[d.score]),d.score>=pwd_minstrength?(!0,s.addClass("valid")):!1)}else t.removeClass(),n.removeClass(),n.empty().append(pwd_strings[5]),!1}});var p=o.closest("form");p.bind("AutoValidationError",function(a){"ajaxData"in a&&"error"in a.ajaxData&&a.ajaxData.error&&(!function(a,e){a.find(".errorPanel").remove();var r=$("<ol/>");$.each(e,function(a,e){r.append($("<li/>",{text:e}))});var s=$("<div/>",{class:"errorPanel"}).append($("<h3/>",{class:"errorHeading",text:pwd_errorstrings[3]+":"}),$("<div/>",{class:"baseHtml errors"}).append(r));a.prepend(s).xfActivate()}(p,a.ajaxData.error),a.preventDefault())})});