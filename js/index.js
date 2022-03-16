function addProject() {
    $.post("/checkers/check_project.php", $("#add_project form").serializeArray()).done(function () {
        $("#add_project form").find("[name]").each(function () {
            this.setCustomValidity('')
        })
        $("#add_project form").addClass('was-validated')
        window.location.href = '/'
    }).fail(function (data) {
        $("#add_project form").find("[name]").each(function () {
            if (error_message = data.responseJSON[$(this).attr("name")]) {
                $(this).parent().find(".invalid-feedback").text(error_message);
                this.setCustomValidity(error_message)
            } else {
                this.setCustomValidity('')
            }
        })
        $("#add_project form").addClass('was-validated')
    })
}

function runMouli(button, project) {
    $(button).parents(".card-body").addClass("loading")
    $(button).parents(".card").find(".loading-info").html('<div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div>').css("display", "flex").hide().fadeIn(1000);
    window.onbeforeunload = function() {
        return "Ta mouli ne se lancera pas si tu recharges la page maintenant...";
    }
    $.post("/php/endpoints/mouli.php", { project: project }).done(function () {
        $(button).parents(".card").find(".loading-info").html('<div class="text-muted text-center" role="status"><div class="spinner-grow" role="status"><span class="sr-only">Loading...</span></div><p>Moulinette en cours...</p></div>')
        window.onbeforeunload = undefined;
    }).fail(function (data) {
        window.onbeforeunload = undefined;
        $(button).parents(".card").find(".loading-info").html('<div class="text-danger text-center" role="status"><i style="font-size: 30px" class="fas fa-times"></i><p>' + data.responseJSON["error"] + '</p></div>')
        setTimeout(() => {
            $(button).parents(".card").find(".loading-info").fadeOut(1000)
            $(button).parents(".card-body").removeClass("loading")
        }, 3000);
    })
}