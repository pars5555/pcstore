/**
 * This file contains all utility functions necessary for input filed validation
 * and text transformation.
 */


/**
 * This method should be called on getting focus.
 * If text field contains default text, it will be removed.
 * Text field style also will be changed.
 * 
 * @param textField - text filed which gain focus
 * @param defaultText - text to be displayed when nothing is entered
 */
function onFieldFocus(textField, defaultText){
    if (textField.value == defaultText) {
        textField.value = '';
        textField.style.color = "black";
    }
}

/**
 * This method should be called on focus lost.
 * If text field is empty, default text will be displayed with different color.
 * 
 * @param textField - text filed which gain focus
 * @param defaultText - text to be displayed when nothing is entered
 */
function onFieldBlur(textField, defaultText){
    if (textField.value == '') {
        textField.value = defaultText;
        textField.style.color = "gray";
    }
}

/**
 * Limits characters count for text inputs.
 * This method is more useful for text areas, 
 * because they havn't maxlength property. 
 * 
 * @param {Object} limitField
 * @param {Object} limitNum
 */
function limitText(limitField, limitNum) {
	if (limitField.value.length > limitNum) {
		limitField.value = limitField.value.substring(0, limitNum);
	}
}
