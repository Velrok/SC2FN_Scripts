function send(){
    // for debugging!
    if (validate()) {
        return true;
    } else {
        alert("Einige Felder wurden nicht oder nicht korrekt ausgefüllt. Bitte korrigiere die rot markierten Felder.");
        return false;
    }
}

function update_playername(player_class, new_name){
	$('.'+player_class).each(function(i, element){
		$(element).html(new_name)
	})
}

/*
validates all select elements. Calling validateWinnerSelect
for each one.

@retuns true if and only if all are valid
*/
function validate(){
    // player name validation
    $('.playerName').each(function(i, tf){
        if( !validatePlayerName( tf ) ) {
            isValid = false;
        }
    })

    // winner validation
    isValid = true;
    $('select').each(function(i,select){
        if( !validateWinnerSelect(select) ) {
            isValid = false;
        }
    })
    
    return isValid;
}


/*
validate a single select element
assigns the invalid calss to each select element that is invalid.

@return true if it is valid else retuns false
*/
function validateWinnerSelect(select){
    select = $(select); // cast to j query obj
    // validate stuff
    if(select.val() == '-- bitte auswählen --'){
        select.addClass('invalid');
        return false;
    } else {
        select.removeClass('invalid');
        return true;
    }
}

function validatePlayerName( text_field ){
    text_field = $(text_field);
    if(text_field.val() == ''){
        text_field.addClass('invalid');
    } else {
        text_field.removeClass('invalid');
    }
}