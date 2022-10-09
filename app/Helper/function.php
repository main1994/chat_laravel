<?php
//自动以公共方法
function show_res($status, $message, $data) {
    $result = [
        'status'  => $status,
        'message' => $message,
        'result'  => $data
    ];
    return json_encode($result);
}
