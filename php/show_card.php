<?php
$no_mouli = "<div class=\"text-muted text-center\"><i style=\"font-size: 50px\" class=\"fas fa-clock mb-2\"></i><p><big>Vous n'avez pas encore lancé de moulinette sur ce projet...</big></p><p>Lancez en une !</p></div>";
$card = file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/templates/card.html");
$res_body = file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/templates/tests.html");
$has_mouli = false;
function isRunning($traces)
{
    foreach ($traces as $trace) {
        if ($trace["status"] == 0) {
            return true;
        }

    }
    return false;
}

function getResult($traces)
{
    foreach ($traces as $trace) {
        if ($trace["status"] == 1) {
            return $trace;
        }

    }
    return null;
}

function percentageColor($percentage)
{
    if ($percentage < 25) {
        return "danger";
    } else if ($percentage < 75) {
        return "warning";
    } else {
        return "success";
    }

}

function showCards($res)
{
    global $mysql, $no_mouli, $res_body, $card, $has_mouli;
    foreach ($res as $repo) {
        $stmt = $mysql->prepare("SELECT `id`, `date`, `status`,`percentage`,`coverage`,`branches`,`cs_sum` FROM `mouli` WHERE `project` = ? ORDER BY `date` DESC LIMIT 2");
        $stmt->bind_param("s", $repo["id"]);
        $stmt->execute();
        $res = $stmt->get_result();
        $results = $res->fetch_all(MYSQLI_ASSOC);
        $loading_data = ["", "", ""];
        if (isRunning($results)) {
            $loading_data = [' loading', 'style="display: flex;"', '<div class="text-muted text-center" role="status"><div class="spinner-grow" role="status"><span class="sr-only">Loading...</span></div><p>Moulinette en cours...</p></div>'];
            $has_mouli = true;
        }
        if ($result = getResult($results)) {
            $date = date("d/m/Y H:i", strtotime($result["date"]));
            $cs_sum = json_decode($result["cs_sum"], true);
            $body = str_replace(
                ["{percentage}", "{percentage_color}", "{majors}", "{minors}", "{infos}", "{coverage_color}", "{coverage_offset}", "{coverage}", "{branches_color}", "{branches_offset}", "{branches}"],
                [round($result["percentage"], 1), percentageColor($result["percentage"]), $cs_sum[0], $cs_sum[1], $cs_sum[2], percentageColor($result["coverage"]), 220 - 2.2 * $result["coverage"], $result["coverage"], percentageColor($result["branches"]), 220 - 2.2 * $result["branches"], $result["branches"]],
                $res_body);
            $details = "href=\"/details/" . $result["id"] . "\"";
        } else {
            $date = "";
            $body = $no_mouli;
            $details = "";
        }
        echo (str_replace(["{name}", "{date}", "{card_body}", "{project_id}", "{loading_class}", "{display_loading}", "{loading_content}", "{details_href}"], [htmlspecialchars($repo["name"]), $date, $body, $repo["id"], ...$loading_data, $details], $card));
    }
}

function showCardsHistory($repo)
{
    global $mysql, $res_body, $card;
    $stmt = $mysql->prepare("SELECT `id`, `date`, `status`,`percentage`,`coverage`,`branches`,`cs_sum` FROM `mouli` WHERE `project` = ? AND `status` = 1 ORDER BY `date` DESC LIMIT 10");
    $stmt->bind_param("s", $repo["id"]);
    $stmt->execute();
    $res = $stmt->get_result();
    $results = $res->fetch_all(MYSQLI_ASSOC);
    foreach ($results as $result) {
        $loading_data = ["", "", ""];
        $date = date("d/m/Y H:i", strtotime($result["date"]));
        $cs_sum = json_decode($result["cs_sum"], true);
        $body = str_replace(
            ["{percentage}", "{percentage_color}", "{majors}", "{minors}", "{infos}", "{coverage_color}", "{coverage_offset}", "{coverage}", "{branches_color}", "{branches_offset}", "{branches}"],
            [round($result["percentage"], 1), percentageColor($result["percentage"]), $cs_sum[0], $cs_sum[1], $cs_sum[2], percentageColor($result["coverage"]), 220 - 2.2 * $result["coverage"], $result["coverage"], percentageColor($result["branches"]), 220 - 2.2 * $result["branches"], $result["branches"]],
            $res_body);
        $details = "href=\"/details/" . $result["id"] . "\"";
        echo (str_replace(["{name}", "{date}", "{card_body}", "{project_id}", "{loading_class}", "{display_loading}", "{loading_content}", "{details_href}", "{history_id}"], [htmlspecialchars($repo["name"]), $date, $body, $repo["id"], ...$loading_data, $details, $repo["id"]], $card));
    }
}
