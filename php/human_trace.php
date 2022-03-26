<?php
$human_trace .= "## Infos\n\n";
$human_trace .= "- **Build " . ($result["build"] ? "success" : "failed") . "**\n";
$human_trace .= "- Percentage: ".$result["percent"]."%\n";
$human_trace .= "- Coverage: ".$result["coverage"]["total"]."%\n";
$human_trace .= "- Branches: ".$result["branches"]["total"]."%\n\n";
$human_trace .= "## Coding Style\n\n";
foreach (["major", "minor", "info"] as $cstype) {
    $human_trace .= "- " . ucfirst($cstype) . " (" . $norm[$cstype]["count"] . ")\n";
    foreach ($norm[$cstype]["list"] as $type => $info) {
        $human_trace .= "   - $type (" . count($info["list"]) . ")\n";
    }
}
foreach ($result["tests"] as $category) {
    $human_trace .= "\n## " . $category["name"] . " (".$category["percent"]."%)\n";
    if ($category["description"]) {
        $human_trace .= "\n" . $category["description"];
    }
    $human_trace .= "\n";
    foreach ($category["sequences"] as $sequence) {
        $human_trace .= "\n### " . $sequence["name"] . " (".($sequence["passed"] ? "OK" : "KO").")\n";
        if ($sequence["description"]) {
            $human_trace .= "\n" . $sequence["description"];
        }
        foreach ($sequence["tests"] as $test) {
            $human_trace .= "\n- ".($test["passed"] ? "[x]" : "[ ]")." **" . $test["name"] . "**\n";
            if (!$test["passed"]) {
                $human_trace .= "\n";
                if ($test["description"]) {
                    $human_trace .= "  ".$test["description"]."\n\n";
                }
                $human_trace .= "  **REASON:** ".$test["reason"]."\n\n";
                $human_trace .= "  **EXPECTED:**\n\n  ```\n  ". $test["expected"] ."\n  ```\n\n";
                $human_trace .= "  **GOT:**\n\n  ```\n  ". $test["got"] ."\n  ```\n";
            }
        }
    }
}

$human_trace .= "\n\n---\nMoulitek - 2022";

