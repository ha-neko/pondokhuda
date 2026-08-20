function validate() {
    var validate = true;
    $("#form input[type=text], #form textarea, #form input[type=password], #form input[type=number], #form input[type=email]").each(function(){
        var input = $(this); // This is the jquery object of the input, do what you will
        // console.log(input.attr('id'));
        // console.log(input.val());
        var a = document.getElementById(input.attr('id')).value;
        var a_trim = a.trim();
        if (a_trim == "" || a_trim == null || document.getElementById(input.attr('id')).value == "NaN") {
            document.getElementById(input.attr('id')).value = "";
            // console.log(a);
            // console.log("ea");
            document.getElementById("loading").style.zIndex = "-999";
            document.getElementById("loading").style.opacity = "0";
            validate = false;
        }
    });

    return validate;
}