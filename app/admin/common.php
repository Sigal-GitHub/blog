<?php
// 这是系统自动生成的公共文件

/**
 * 返回数据模板
 *
 * @param integer $code
 * @param string $msg
 * @return json
 */
function show(int $code, string $msg)
{
    $res = [
        'code' => $code,
        'msg' => $msg
    ];

    return json($res);
}

/**
 * 返回 layui 表格所需要的数据格式
 *
 * @param int $code
 * @param string $msg
 * @param int $count
 * @param array $data
 * @return json
 */
function layuiTable($code, $msg, $count, $data)
{
    $res = [
        "code" => $code,
        "msg"  => $msg,
        "count" => $count,
        "data" => $data
    ];

    return json($res);
}

function layuiImage($status, $msg, $url = null)
{
    $res = [
        'status' => $status,
        'msg' => $msg,
        'url' => $url
    ];

    return json($res);
}
