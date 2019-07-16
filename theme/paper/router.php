<?php
return [
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
];