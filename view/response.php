<?php

use Core\LoggerUtility;

function jsonResponse($data, $status = 200,$level='info') {
        http_response_code($status);

    LoggerUtility::logMessage($level, "The jsonResponse: " . json_encode($data) . " with status: " . json_encode($status));


    echo json_encode($data);
        exit();

}
