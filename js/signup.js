function swipeTo(index) {
    console.log("index", index)
    $("#slides").animate({ scrollLeft: $(window).width() * index }, 700)
}

function checkFormAndSwipe(index) {
    let form = $("#slides > div").eq(index).find("form");

    if (form.attr("checker"))
        $.post("/checkers/" + form.attr("checker") + ".php", form.serializeArray()).done(function () {
            form.find("[name]").each(function () {
                this.setCustomValidity('')
            })
            form.addClass('was-validated')
            swipeTo(index + 1)
        }).fail(function (data) {
            form.find("[name]").each(function () {
                if (error_message = data.responseJSON[$(this).attr("name")]) {
                    $(this).parent().find(".invalid-feedback").text(error_message);
                    this.setCustomValidity(error_message)
                } else {
                    this.setCustomValidity('')
                }
            })
            form.addClass('was-validated')
        })
}

function submitAndRedirect() {
    $.post("/php/endpoints/signup.php", $("form").serializeArray()).done(function () {
        window.location.href = "/";
    }).fail(function () {
        window.location.reload();
    })
}