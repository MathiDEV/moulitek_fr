<?php
session_start();
if (!isset($_SESSION["user"])) {
    header("Location: /signup");
}
if (!isset($_GET["id"])) {
    header("Location: /");
}
require "php/mysql.php";
$stmt = $mysql->prepare("SELECT (SELECT `name` FROM `repos` WHERE `id` = `mouli`.`project`) as name, `date`, `percentage`, `coverage`, `branches`, `cover_data`, `branches_data`, `trace`, `tests`, `cs_sum`, `cs` FROM `mouli` WHERE `id` = ? AND `user` = ? AND `status` = 1");
$stmt->bind_param("ss", $_GET["id"], $_SESSION["user"]);
$stmt->execute();
$res = $stmt->get_result();
$result = $res->fetch_array(MYSQLI_ASSOC);
if (!$result) {
    header("Location: /");
}
$coding_style_sum = [0, 0, 0];
$coding_style = ["major" => ["count" => 0, "list" => []], "minor" => ["count" => 0, "list" => []], "info" => ["count" => 0, "list" => []]];
if ($result["cs_sum"]) {
    $coding_style_sum = json_decode($result["cs_sum"], true);
}
if ($result["cs"]) {
    $coding_style = json_decode($result["cs"], true);
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

?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous">
    </script>
    <script src="https://kit.fontawesome.com/b269430966.js" crossorigin="anonymous"></script>
    <script src="/js/details.js"></script>
    <link rel="stylesheet" href="/css/main.css">
    <title>Moulitek</title>
    <style>
        #mainmodal .modal-body {
            max-height: 70vh;
            height: fit-content;
            overflow-y: scroll;
        }
    </style>
</head>

<body>
    <?php include $_SERVER["DOCUMENT_ROOT"] . "/php/header.php";?>
    <div class="rounded shadow-sm mt-5 mx-4 d-flex bg-white details-header row p-4">
        <div class="project-sum col-4">
            <h2><?php echo htmlspecialchars($result["name"]); ?></h2>
            <p class="text-center mt-3 mb-0 mx-5 pct-info"><?php echo round($result["percentage"], 1); ?>%</p>
            <div class="progress mx-5">
                <div class="progress-bar bg-<?php echo percentageColor($result["percentage"]); ?>" role="progressbar" style="width: <?php echo $result["percentage"]; ?>%" aria-valuenow="<?php echo $result["percentage"]; ?>"
                    aria-valuemin="0" aria-valuemax="100"></div>
            </div>
        </div>
        <div class="col-8">
            <div class="d-flex justify-content-between">
                <h4>Trace</h4>
                <h5><?php echo date("d/m/Y H:i", strtotime($result["date"])); ?></h4>
            </div>
            <pre class="rounded border border p-2"><?php echo $result["trace"]; ?></pre>
        </div>
    </div>
    <div style="gap: 10px" class="rounded shadow-sm mt-3 mx-4 row bg-white p-4">
        <div class="col-3 cs-sum">
            <h3 class="mb-5">Coding style</h3>
            <table class="table">
                <tbody>
                    <tr>
                        <th scope="row">Major</th>
                        <td class="text-end"><?php echo $coding_style_sum[0]; ?></td>
                    </tr>
                    <tr>
                        <th scope="row">Minor</th>
                        <td class="text-end"><?php echo $coding_style_sum[1]; ?></td>
                    </tr>
                    <tr>
                        <th scope="row">Info</th>
                        <td class="text-end"><?php echo $coding_style_sum[2]; ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div style="gap: 30px" class="col row col conding-style-boxes">
            <div class="shadow-sm col p-2">
                <h5 class="text-center">Major</h5>
                <ul class="list-group">
                <?php
foreach ($coding_style["major"]["list"] as $code => $major) {
    echo '<li class="list-group-item border-0"><span onclick="showNorm(\'major\', \'' . $code . '\')" role="button"><i class="fas fa-search me-1" aria-hidden="true"></i> <b>' . $code . '</b></span>
                    <i class="fas fa-chevron-right" aria-hidden="true"></i> ' . count($major["list"]) . '</li>';
}
?>
                </ul>
            </div>
            <div class="shadow-sm col p-2">
                <h5 class="text-center">Minor</h5>
                <ul class="list-group">
                <?php foreach ($coding_style["minor"]["list"] as $code => $minor) {
    echo '<li class="list-group-item border-0"><span onclick="showNorm(\'minor\', \'' . $code . '\')" role="button"><i class="fas fa-search me-1" aria-hidden="true"></i> <b>' . $code . '</b></span>
                    <i class="fas fa-chevron-right" aria-hidden="true"></i> ' . count($minor["list"]) . '</li>';
}
?>
                </ul>
            </div>
            <div class="shadow-sm col p-2">
                <h5 class="text-center">Info</h5>
                <ul class="list-group">
                <?php foreach ($coding_style["info"]["list"] as $code => $info) {
    echo '<li class="list-group-item border-0"><span onclick="showNorm(\'info\', \'' . $code . '\')" role="button"><i class="fas fa-search me-1" aria-hidden="true"></i> <b>' . $code . '</b></span>
                    <i class="fas fa-chevron-right" aria-hidden="true"></i> ' . count($info["list"]) . '</li>';
}
?>
                </ul>
            </div>
        </div>
    </div>
    <div style="gap: 10px" class="rounded shadow-sm mt-3 mb-5 mx-4 row bg-white p-4">
        <div>
            <div style="gap: 20px;" class="d-flex">
            <div>
            <h3 class="mb-5">Coverage</h3>
                <div class="d-flex">
                    <div><svg class="radial-progress" data-percentage="71" viewBox="0 0 80 80">
                            <circle stroke="var(--bs-gray-200)" class="incomplete" cx="40" cy="40" r="35"></circle>
                            <circle stroke="var(--bs-<?php echo percentageColor($result["coverage"]); ?>)" class="complete" cx="40" cy="40" r="35"
                                style="stroke-dashoffset: <?php echo 220 - 2.2 * $result["coverage"]; ?>px;"></circle>
                            <text class="percentage" x="50%" y="57%" transform="matrix(0, 1, -1, 0, 80, 0)"><?php echo $result["coverage"]; ?>%</text>
                        </svg>
                        <p class="text-center">Coverage</p>
                    </div>
                    <div>
                        <svg class="radial-progress" data-percentage="71" viewBox="0 0 80 80">
                            <circle stroke="var(--bs-gray-200)" class="incomplete" cx="40" cy="40" r="35"></circle>
                            <circle stroke="var(--bs-<?php echo percentageColor($result["branches"]); ?>)" class="complete" cx="40" cy="40" r="35"
                                style="stroke-dashoffset: <?php echo 220 - 2.2 * $result["branches"]; ?>px;"></circle>
                            <text class="percentage" x="50%" y="57%" transform="matrix(0, 1, -1, 0, 80, 0)"><?php echo $result["branches"]; ?>%</text>
                        </svg>
                        <p class="text-center">Branches</p>
                    </div>
                </div>
                </div>

                <?php
                    $coverage_data = json_decode($result["cover_data"], true);
                    if (count($coverage_data) > 0) {
                        echo '<div style="max-height: 200px; overflow-y: scroll;"><table class="table">
                        <thead>
                            <tr>
                            <th scope="col">Fichier</th>
                            <th scope="col">Lignes</th>
                            <th scope="col">Exec</th>
                            <th scope="col">Cover</th>
                            <th scope="col">Manquantes</th>
                            </tr>
                        </thead>
                        <tbody>';
                        foreach($coverage_data as $data) {
                            echo '<tr>
                            <th scope="row">'.$data["file"].'</th>
                            <td>'.$data["lines"].'</td>
                            <td>'.$data["exec"].'</td>
                            <td>'.$data["cover"].'</td>
                            <td>';
                            foreach ($data["missing"] as $missing) {
                                echo "<span class=\"badge bg-primary mx-1\">$missing</span>";
                            }
                            echo '</td>
                            </tr>';
                        }
                        echo '</tbody>
                        </table></div>';
                    }
                    ?>
            </div>
        </div>
    </div>
    <?php
$tests = json_decode($result["tests"], true);
$test_cat_box = file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/templates/test_cat.html");

foreach ($tests as $index => $category) {
    $tests_body = "";
    foreach ($category["tests"] as $k => $test) {
        $tests_body .= '<li class="list-group-item border-0"><i class="fas fa-' . ($test["passed"] ? "check" : "times") . ' me-1" aria-hidden="true"></i>
            ' . htmlspecialchars(ucfirst($test["name"])) . '
            <i onclick="showTests(' . $index . ', ' . $k . ')" role="button" class="fas fa-question-circle text-muted" aria-hidden="true"></i></li>';
    }
    echo str_replace(["{name}", "{percentage}", "{percentage_color}", "{total}", "{passed}", "{failed}", "{crashed}", "{tests_body}"], [htmlspecialchars($category["name"]), $category["percent"], percentageColor($category["percent"]), $category["total"], $category["total"] - $category["failed"], $category["failed"], 0, $tests_body], $test_cat_box);
}
?>
    <div class="modal" id="mainmodal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
      </div>
      <div class="modal-body d-none">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
      </div>
    </div>
  </div>
</div>
    <script>
        const data_coding_style = <?php echo json_encode($coding_style); ?>,
        data_tests = <?php echo json_encode($tests); ?>;
    </script>
<?php include $_SERVER["DOCUMENT_ROOT"] . "/php/footer.php";?>
</body>

</html>