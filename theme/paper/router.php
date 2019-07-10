<?php
return [
    [
        "file"   => "index",
        "regexp" => "^/$",
    ],
    [
        "file"   => "tag",
        "regexp" => "^/tag/((?!-).).html$",
        "params" => ["id"],
    ],
    [
        "file"   => "tag",
        "regexp" => "^/tag/(.*)-(.*).html$",
        "params" => ["id", "page"],
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