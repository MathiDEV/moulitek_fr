<?php
header('Content-type: Application/json');

$trace = file_get_contents("../trace.html");
$lines = explode("\n", $trace);
$current = "none";
$test_cat = -1;
$test_num = -1;
$subtest_num = -1;
$i = -1;
$line_count = count($lines);
$save = [];
$result = [
    "coverage" => ["total" => 0, "files" => []],
    "branches" => ["total" => 0, "files" => []],
    "tests" => [],
];

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

        $line_split = array_values(array_filter(explode(" ", $lines[$i])));
        if (count($line_split) == 0) {
            continue;
        }

        if ($line_split[0] == "[##]") {
            $current = "none";
            $test_num = -1;
            $test_cat++;
            $result["tests"][] = ["name" => get_content($lines[$i], "##")["content"], "tests" => []];
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
echo json_encode($result)
?>
