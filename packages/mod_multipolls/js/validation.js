jQuery(document).ready(function($)
{	
    $('.cb-answers.required').each(function( index ) {
        let checkboxes = $('input[type=checkbox]', this);
        let checkboxLength = checkboxes.length;
        let firstCheckbox = checkboxLength > 0 ? checkboxes[0] : null;
        var error = $('.valid-error', this).val();
        
        if (firstCheckbox) {
            for (let i = 0; i < checkboxLength; i++) {
                checkboxes[i].addEventListener('change', checkValidity);
            }

            checkValidity();
        }

        function isChecked() {
            for (let i = 0; i < checkboxLength; i++) {
                if (checkboxes[i].checked) return true;
            }

            return false;
        }

        function checkValidity() {
            let errorMessage = !isChecked() ? error : '';
            firstCheckbox.setCustomValidity(errorMessage);
        }
    });   

    $('.cbo-answers.required').each(function( index ) {
        let checkboxes = $('input[type=checkbox]', this);
        let checkboxLength = checkboxes.length;
        let firstCheckbox = checkboxLength > 0 ? checkboxes[0] : null;
        var error = $('.valid-error', this).val();

        if (firstCheckbox) {
            for (let i = 0; i < checkboxLength; i++) {
                checkboxes[i].addEventListener('change', checkValidity);                   
            }

            $(".own-input", this).focusin(checkValidity);

            checkValidity();
        }

        function isChecked() {
            for (let i = 0; i < checkboxLength; i++) {
                if (checkboxes[i].checked) return true;
            }

            return false;
        }

        function checkValidity() {
            let errorMessage = !isChecked() ? error : '';
            firstCheckbox.setCustomValidity(errorMessage);
        }
    });  
});