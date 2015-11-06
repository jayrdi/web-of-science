
$(document).ready(function() {

    // variables for journal form fields
    var max_Jfields  = 3; // maximum number of journal fields user may add
    var wrapperJ     = $(".journal_fields_wrap"); // wrapper for journal search fields
    var add_buttonJ  = $(".add_journal_field_button"); // add button ID for journal form fields
    var x = 1; // initial text box count for journal form fields

    // variables for title form fields
    var max_Tfields  = 3; // maximum number of title fields user may add
    var wrapperT     = $(".title_fields_wrap"); // wrapper for title search fields
    var add_buttonT  = $(".add_title_field_button"); // add button ID for title form fields
    var y = 1; // initial text box count for title form fields

    ///////////////////////////
    /// JOURNAL FORM FIELDS ///
    ///////////////////////////

    // on add journal form field
    add_buttonJ.click(function(e) {

        e.preventDefault();
        if (x < max_Jfields) {
            x++; // text box increment

            // add input box
            wrapperJ.append('<div><input class="form-control" type="text" name="journal' + x + '"><a href="#" class="remove_field"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></a></div>');
        }
    });

    // on remove journal form field
    wrapperJ.on("click", ".remove_field", function(e) {

        e.preventDefault();
        $(this).parent('').remove(); // remove parent form field
        x--; // text box decrement
    });

    ///////////////////////////
    //// TITLE FORM FIELDS ////
    ///////////////////////////

    // on add title form field
    add_buttonT.click(function(e) {

        e.preventDefault();
        if (y < max_Tfields) {
            y++; // text box increment

            // add input box
            wrapperT.append('<div><input class="form-control" type="text" name="title' + y + '"><a href="#" class="remove_field"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></a></div>');
        }
    });

    // on remove title form field
    wrapperT.on("click", ".remove_field", function(e) {

        e.preventDefault();
        $(this).parent('').remove(); // remove parent form field
        y--; // text box decrement
    });

    ////////////////////
    ///// TOOLTIPS /////
    ////////////////////

    $('[data-toggle="tooltip-right"]').tooltip({
        'placement': 'right'
    });

    $('[data-toggle="tooltip-down"]').tooltip({
        'placement': 'bottom'
    });

    ////////////////////
    //// NO RECORDS ////
    ////  BUTTON    ////
    ////////////////////

    // var goBack = $(".back");

    // $(goBack).click(function(e) {
    //     window.location.href='index.php';
    // });

})

///////////////////////////
//// VARIOUS FUNCTIONS ////
///////////////////////////

// function that will scroll to the bottom of the window when called
function scrollToBottom() {
    window.scrollTo(0, document.body.scrollHeight);
};

// function that will overwrite current record in info panel
function setRecord(current) {
    document.getElementById('progressPanel').innerHTML = "<strong>Loading record " + current + "</strong>";
};

// function to redirect to index page
function goBack() {
    window.location.href = 'index.php';
};

// function to remove the info panel once page has loaded
function removePanel() {
    var rem = document.getElementById('alertBox');
    rem.remove();
};

// function to display 'processing' loadbar
function showLoadBar() {
    $('#processing').show();
};
