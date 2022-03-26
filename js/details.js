const fail_reson = {
    'retvalue': "<i class=\"fas fa-arrow-turn-down-left\"></i> Mauvaise valeur de retour.",
    'badoutput': "<i class=\"fas fa-terminal\"></i> Mauvaise sortie standard.",
    'timeout': "<i class=\"fas fa-timer\"></i> Délai dépassé.",
    'other': "<i class=\"fas fa-question\"></i> Motif inconnu.",
}
function showTests(category, index) {
    if (tests = data_tests[category]["sequences"][index]) {
        $("#mainmodal .modal-body").first().html("").removeClass("d-none");
        $("#mainmodal .modal-body").last().addClass("d-none")
        $("#mainmodal .modal-title").text(tests["name"].charAt(0).toUpperCase() + tests["name"].slice(1));
        for (i in tests["list"]) {
            let test = tests["list"][i],
                expected_got = ""
            if (test["expected"] && test["got"]) {
                expected_got = " <i role=\"button\" onclick=\"showExpectedGot(" + category + "," + index + "," + i + ")\" class=\"fas fa-circle-info\"></i>"
            }
            $("#mainmodal .modal-body").first().append("<p class=\"" + (test["passed"] ? "text-success" : "text-danger") + "\"><i class=\"fas fa-" + (test["passed"] ? "check" : "times") + "\"></i> " + test["name"] + expected_got + "</p>")
        }
        $("#mainmodal").modal('show');
    }
}

function showExpectedGot(category, index, test) {
    if (test = data_tests[category]["sequences"][index]["list"][test]) {
        if (test["expected"] && test["got"]) {
            if (test["reason"] && fail_reson[test["reason"]])
                reason = fail_reson[test["reason"]]
            else
                reason = fail_reson["other"]
            $("#mainmodal .modal-body").last().html("<p class=\"text-primary\" onclick=\"swapModal()\" role=\"button\"><i class=\"fas fa-chevron-left\"></i> Retour</p><h5>Motif:</h5><p class=\"text-muted\">" + reason + "</p><hr><h5>Attendu:</h5><pre class=\"p-2\">" + test["expected"] + "</pre><h5>Obtenu:</h5><pre class=\"p-2\">" + test["got"] + "</pre>");
            $("#mainmodal .modal-body").toggleClass("d-none");
        }
    }
}

function showNorm(type, code) {
    if (data_coding_style[type]) {
		 let data = data_coding_style[type]["list"][code];
		if (!data)
			return;
        $("#mainmodal .modal-body").first().html("").removeClass("d-none");
        $("#mainmodal .modal-body").last().addClass("d-none")
        $("#mainmodal .modal-title").text(code);
		$("#mainmodal .modal-body").first().append("<p><b>"+data["description"]+"</b></p>")
        for (file of data["list"]) {
            $("#mainmodal .modal-body").first().append("<p>"+file["file"]+(file["line"].length ? "<span class=\"text-muted\">:"+file["line"]+"</span>" : "")+"</p>")
        }
        $("#mainmodal").modal('show');
    }
}

function swapModal() {
    $("#mainmodal .modal-body").toggleClass("d-none");
}