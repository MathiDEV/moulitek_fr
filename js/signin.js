function signIn() {
    $.post("/checkers/check_login.php", $("form").serializeArray()).done(function () {
        $("form").find("[name]").each(function () {
            this.setCustomValidity('')
        })
        $("form").addClass('was-validated')
        window.location.href = "/"
    }).fail(function (data) {
        $("form").find("[name]").each(function () {
            if (error_message = data.responseJSON[$(this).attr("name")]) {
                $(this).parent().find(".invalid-feedback").text(error_message);
                this.setCustomValidity(error_message)
            } else {
                this.setCustomValidity('')
            }
        })
        $("form").addClass('was-validated')
    })
}