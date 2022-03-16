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

$lines = explode("\n", $_POST["trace"]);
$current = "none";
$test_cat = -1;
$test_num = -1;
$subtest_num = -1;
$i = -1;
$line_count = count($lines);
$save = [];
$result = [
    "build" => false,
    "coverage" => ["total" => 0, "files" => []],
    "branches" => ["total" => 0, "files" => []],
    "percent" => 100,
    "tests" => [],
];

if (strpos($_POST["trace"], "[BUILD SUCCESS]") !== false) {
    $result["build"] = true;
}
function parse_coverage()
{
    global $lines, $i, $result, $current;
    if ($lines[$i][0] == "-") {
        $file = array_values(array_filter(explode(" ", $lines[$i + 1])));
        $result[$current]["total"] = intval($file[3]);
        $current = "none";
        return (1);
    }
    $file = array_values(array_filter(explode(" ", $lines[$i])));
    if (count($file) == 4) {
        $file[] = "";
    }
    if (count($file) == 5) {
        $result[$current]["files"][] = ["file" => $file[0], "lines" => intval($file[1]), "exec" => intval($file[2]), "cover" => intval($file[3]), "missing" => array_values(array_filter(explode(",", $file[4])))];
    }
    return (0);
}

function parse_expected()
{
    global $lines, $i, $result, $current, $test_cat, $test_num, $save, $subtest_num;
    if ($lines[$i] == "[/$current]") {
        $result["tests"][$test_cat]["tests"][$test_num]["list"][$subtest_num][$current] = implode("\n", $save);
        $current = "none";
        return (1);
    }
    $save[] = $lines[$i];
    return (0);
}

function get_content($line, $delimiter)
{
    $content = trim(str_replace(["[$delimiter]", "[/$delimiter]"], ["", ""], $line));
    preg_match("/(\[\w+\])/", $content, $tags);
    $content = trim(preg_replace("/\[\w+\]/", "", $content));
    foreach ($tags as $i => $tag) {
        $tags[$i] = trim($tags[$i], "[]");
    }
    return ["content" => $content, "tags" => $tags];
}

while ($i < $line_count - 1) {
	if (!$result["build"])
		break;
    $i++;
    if ($current == "none") {
        if ($lines[$i] == "[covr]") {
            $current = "coverage";
            $i += 7;
            continue;
        }

        if ($lines[$i] == "[branch]") {
            $current = "branches";
            $i += 7;
            continue;
        }

        if ($lines[$i] == "[expected]") {
            $current = "expected";
            $save = [];
            continue;
        }

        if ($lines[$i] == "[got]") {
            $current = "got";
            $save = [];
            continue;
        }
        $lines[$i] = strstr($lines[$i], "[");
        $line_split = array_values(array_filter(explode(" ", $lines[$i])));
        if (count($line_split) == 0) {
            continue;
        }

        if ($line_split[0] == "[##]") {
            $current = "none";
            $test_num = -1;
            $test_cat++;
            $result["tests"][] = ["name" => get_content($lines[$i], "##")["content"], "total" => 0, "failed" => 0, "tests" => []];
            continue;
        }

        if ($line_split[0] == "[#]") {
            $test_num++;
            $subtest_num = -1;
            $result["tests"][$test_cat]["tests"][] = ["name" => get_content($lines[$i], "#")["content"], "passed" => true, "list" => []];
            continue;
        }

        if ($line_split[0] == "[OK]") {
            $subtest_num++;
            $result["tests"][$test_cat]["tests"][$test_num]["list"][] = ["name" => get_content($lines[$i], "OK")["content"], "passed" => true];
            continue;
        }
        if ($line_split[0] == "[KO]") {
            $subtest_num++;
            $content = get_content($lines[$i], "KO");
            $result["tests"][$test_cat]["tests"][$test_num]["passed"] = false;
            $result["tests"][$test_cat]["tests"][$test_num]["list"][] = ["name" => $content["content"], "passed" => false, "reason" => $content["tags"][0]];
            continue;
        }
    }

    if ($current == "coverage" || $current == "branches") {
        if (parse_coverage()) {
            continue;
        }
    }

    if ($current == "expected" || $current == "got") {
        if (parse_expected()) {
            continue;
        }
    }

}
$total = 0;
$total_failed = 0;
foreach ($result["tests"] as $k => $test) {
    $result["tests"][$k]["total"] = count($test["tests"]);
    $total += $result["tests"][$k]["total"];
    $result["tests"][$k]["failed"] = count(array_filter($test["tests"], function ($e) {
        return $e["passed"] == false;
    }));
    $total_failed += $result["tests"][$k]["failed"];
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

$norm = json_decode($_POST["norm"], true);
$norm_sum[0] = $norm["major"]["count"];
$norm_sum[1] = $norm["minor"]["count"];
$norm_sum[2] = $norm["info"]["count"];
$norm_sum = json_encode($norm_sum);

$result["coverage"]["files"] = json_encode($result["coverage"]["files"]);
$result["branches"]["files"] = json_encode($result["branches"]["files"]);
$result["tests"] = json_encode($result["tests"]);
$stmt = $mysql->prepare("UPDATE `mouli` SET `status`= 1, `build`= ?,`percentage`= ?,`coverage`= ?,`branches`= ?,`cover_data`= ?,`branches_data`= ?,`trace`= ?,`tests`=?, `cs_sum`=?, `cs`=? WHERE `id` = ?");
$stmt->bind_param("sssssssssss", $result["build"], $result["percent"], $result["coverage"]["total"], $result["branches"]["total"], $result["coverage"]["files"], $result["branches"]["files"], $_POST["trace"], $result["tests"], $norm_sum, $_POST["norm"], $_POST["id"]);
$stmt->execute();
