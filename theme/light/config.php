<?php
return [
    "name"   => "LIGHT",
    "author" => "Zee Kim",
    "date"   => "20190724",
    // 主题需要的全局变量（该变量仅供导入使用）
    "config" => [
        "title"       => [
            "type" => 0,
            "value" => "网站标题",
            "description"=>"网站标题"
        ],
        "keywords"    => [
            "type" => 0,
            "value" => "网站关键字",
            "description"=>"网站关键字"
        ],
        "description" => [
            "type" => 0,
            "value" => "网站描述",
            "description"=>"网站描述"
        ],
        "icp"         => [
            "type" => 0,
            "value" => "蜀ICP备14010229号-3",
            "description"=>"备案号"
        ],
        "copyright"   => [
            "type" => 0,
            "value" => "ZEE.KIM",
            "description"=>"版权"
        ],
        "about-avatar"   => [
            "type" => 2,
            "value" => "",
            "description"=>"关于头像"
        ],
        "about-content"   => [
            "type" => 1,
            "value" => "Hi，I'm Zee Kim, a frontend web developer in Shanghai City, China.My work is to write javascript, ensure browser compatibility, making good user experience.You can contact me by <a href=\"mailto:zee.kim@qq.com\">e-mail</a>",
            "description"=>"关于内容"
        ],
    ],
    // 路由表
     "router" => [
        [
            "file"   => "index",
            "regexp" => "^/$",
        ],
        [
            "file"   => "tag",
            "regexp" => "^/tag/(?!.*-)(.*).html$",
            "params" => ["id"],
        ],
        [
            "file"   => "tag",
            "regexp" => "^/tag/(.*)-(.*).html$",
            "params" => ["id", "page"],
        ],
        [
            "file"   => "search",
            "regexp" => "^/search/(?!.*-)(.*).html$",
            "params" => ["keyword"],
        ],
        [
            "file"   => "search",
            "regexp" => "^/search/(.*)-(.*).html$",
            "params" => ["keyword", "page"],
        ],
        [
            "file"   => "post",
            "regexp" => "^/post/(.*).html$",
            "params" => ["id"],
        ],
        [
            "file"   => "about",
            "regexp" => "^/about$",
        ],
    ],
];