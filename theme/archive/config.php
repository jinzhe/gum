<?php
return [
    "name"   => "Archive",
    "author" => "Zee Kim",
    "date"   => "20210414",
    // 主题需要的全局变量（该变量仅供导入使用）
     "config" => [
        "title"       => [
            "type"        => 0,
            "value"       => "网站标题",
            "description" => "网站标题",
        ],
        "keywords"    => [
            "type"        => 0,
            "value"       => "网站关键字",
            "description" => "网站关键字",
        ],
        "description" => [
            "type"        => 0,
            "value"       => "网站描述",
            "description" => "网站描述",
        ],
        "icp"         => [
            "type"        => 0,
            "value"       => "蜀ICP备14010229号-3",
            "description" => "备案号",
        ],
        "copyright"   => [
            "type"        => 0,
            "value"       => "ZEE.KIM",
            "description" => "版权",
        ],
    ],
    // 路由表
     "router" => [
        [
            "file"   => "index",
            "regexp" => "^/$",
        ],
        [
            "file"   => "post",
            "regexp" => "^/post/(.*).html$",
            "params" => ["id"],
        ],
    
    ],
];