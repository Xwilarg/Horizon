<?php
    $options = [
        "http" => [
            "method" => "GET",
            "header" => "User-Agent: Mozilla/5.0 commits.zirk.eu\r\n"
        ]
    ];
    $context = stream_context_create($options);
    $content = file_get_contents("../php/shipInfo.php?name=fumizuki", false, $context);
    PHPUnit\Framework\Assert::assertTrue($options[0] !== null);
    PHPUnit\Framework\Assert::assertTrue($options[1] !== null);
    PHPUnit\Framework\Assert::assertTrue($options[2] !== null);
    PHPUnit\Framework\Assert::assertTrue($options[3] !== null);
    PHPUnit\Framework\Assert::assertTrue($options[4] !== null);
    PHPUnit\Framework\Assert::assertTrue($options[5] !== null);
?>