<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
session_start();
require $_SERVER['DOCUMENT_ROOT'] . "/php/mysql.php";
if (!isset($_POST["id"]) || !isset($_POST["trace"]) || !isset($_POST["norm"])) {
    die(0);
}
$stmt = $mysql->prepare("SELECT `id` FROM `mouli` WHERE `id` = ? AND `status` = 0");
$stmt->bind_param("s", $_POST["id"]);
$stmt->execute();
$res = $stmt->get_result();
$array = $res->fetch_array(MYSQLI_ASSOC);
if (!$array) {
    die(0);
}

$trace = $_POST["trace"];
$norm_data = $_POST["norm"];
$trace = json_decode($trace, true);
$result = [
    "build" => $trace["build"],
    "coverage" => ["total" => 0, "files" => []],
    "branches" => ["total" => 0, "files" => []],
    "percent" => 100,
    "tests" => $trace["tests"],
];

function parse_coverage($res, $type)
{
    global $result;

    $lines = explode("\n", $res);
    $i = 6;
    while ($i < count($lines) && $lines[$i][0] != "-") {
        $file = array_values(array_filter(explode(" ", $lines[$i])));
        if (count($file) == 4) {
            $file[] = "";
        }
        if (count($file) == 5) {
            $result[$type]["files"][] = ["file" => $file[0], "lines" => intval($file[1]), "exec" => intval($file[2]), "cover" => intval($file[3]), "missing" => array_values(array_filter(explode(",", $file[4])))];
        }
        $i++;
    }
    $i++;
    $result[$type]["total"] = intval(array_values(array_filter(explode(" ", $lines[$i])))[3]);
}

if ($trace["coverage"] && $trace["branches"]) {
    parse_coverage($trace["coverage"], "coverage");
    parse_coverage($trace["branches"], "branches");
}

$total = 0;
$total_failed = 0;
foreach ($result["tests"] as $k => $test) {
    $result["tests"][$k]["total"] = count($test["sequences"]);
    $result["tests"][$k]["failed"] = count(array_filter($test["sequences"], function ($e) {
        return $e["passed"] == false;
    }));
    if (!$test["info"]) {
        $total += $result["tests"][$k]["total"];
        $total_failed += $result["tests"][$k]["failed"];
    }
    if ($result["tests"][$k]["total"] == 0) {
        $result["tests"][$k]["percent"] = 100;
    } else {
        $result["tests"][$k]["percent"] = ($result["tests"][$k]["total"] - $result["tests"][$k]["failed"]) * 100 / $result["tests"][$k]["total"];
    }
}
if ($total == 0) {
    $result["percent"] = ($result["build"] ? 100 : 0);
} else {
    $result["percent"] = ($total - $total_failed) * 100 / $total;
}

$norm_sum = [0, 0, 0];
$norm = json_decode($norm_data, true);
if ($norm) {
    $norm_sum[0] = $norm["major"]["count"];
    $norm_sum[1] = $norm["minor"]["count"];
    $norm_sum[2] = $norm["info"]["count"];
} else {
    $norm = [];
}

// Human trace
$human_trace = "";
include $_SERVER["DOCUMENT_ROOT"]."/php/human_trace.php";

$norm = json_encode($norm);
$norm_sum = json_encode($norm_sum);
$result["coverage"]["files"] = json_encode($result["coverage"]["files"]);
$result["branches"]["files"] = json_encode($result["branches"]["files"]);
$result["tests"] = json_encode($result["tests"]);
$stmt = $mysql->prepare("UPDATE `mouli` SET `status`= 1, `build`= ?,`percentage`= ?,`coverage`= ?,`branches`= ?,`cover_data`= ?,`branches_data`= ?,`trace`= ?,`tests`=?, `cs_sum`=?, `cs`=? WHERE `id` = ?");
$stmt->bind_param("sssssssssss", $result["build"], $result["percent"], $result["coverage"]["total"], $result["branches"]["total"], $result["coverage"]["files"], $result["branches"]["files"], $human_trace, $result["tests"], $norm_sum, $norm, $_POST["id"]);
$stmt->execute();
