/**
 * KL_PasswordTools_PasswordStrengthMeter
 *
 *	@author: Katsulynx
 *  @last_edit:	15.08.2015
 *  @compiled: 15.08.2015 - Google Closure Compiler
 */
$(document).ready(function(){var e=["","easy","medium","hard","brutal"],b=!1,c=!1,d=!1;$("#passwordStrengthPhrase").prev().on("input",function(a){a=$(this).val();$("#passwordReadyBox").removeClass("valid");d=c=!1;a.length&&a.length>=pwd_minlength?(d=!0,a=zxcvbn(a,pwd_checklist),pwd_forcepwd&&$.each(a.match_sequence,function(a,b){"user_inputs"===b.dictionary_name&&(c=!0)}),c?(b=!1,$("#passwordStrengthMeter, #passwordStrengthPhrase").removeClass(),$("#passwordStrengthPhrase").empty().append(pwd_strings[6])):
($("#passwordStrengthMeter, #passwordStrengthPhrase").removeClass().addClass(e[a.score]),$("#passwordStrengthPhrase").empty().append(pwd_strings[a.score]),a.score>=pwd_minstrength?(b=!0,$("#passwordReadyBox").addClass("valid")):b=!1)):($("#passwordStrengthMeter, #passwordStrengthPhrase").removeClass(),$("#passwordStrengthPhrase").empty().append(pwd_strings[5]),b=!1)});$("#passwordStrengthPhrase, #passwordCompareBox").prev().on("input",function(){var a=[$('#passwordCompareBox').prev().val(),$("#passwordStrengthPhrase").prev().val()];
a[0]&&a[0]===a[1]?$("#passwordCompareBox").addClass("valid"):$("#passwordCompareBox").removeClass("valid")});$("input[type=submit]").click(function(a){b||(a.preventDefault(),$(".errorPanel").remove(),$(".pageContent form").prepend(jQuery("<div/>",{"class":"errorPanel",id:"errorPanel"}).append(jQuery("<h3/>",{"class":"errorHeading",text:pwd_errorstrings[3]+":"}),jQuery("<div/>",{"class":"baseHtml errors"}).append(jQuery("<ol/>").append(jQuery("<li/>",{text:d?c?pwd_errorstrings[1]:pwd_errorstrings[2]:pwd_errorstrings[0]}))))),
$("html, body").animate({scrollTop:$("#errorPanel").offset().top-40},"fast"))})});