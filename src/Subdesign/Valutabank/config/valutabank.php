<?php

return [
    'currencies' => ['USD', 'EUR', 'CHF'], // array of currencies or the string "all" if you want all (array/string)
    'returntype' => 'html',                // html OR array (string)
    'curl'       => false,                 // use CURL (bool)
    'cache'      => false,                 // use caching of server data (bool)
    'cache_ttl'  => 60,                    // if caching enabled, set minutes for it (integer)
    'icon_path'  => '/assets/images/',     // relative path (from public/) to the icon images (string)
    'icon_name'  => 'icon',                // icon name prefix. it will be "icon-usd", "icon-eur" etc. (string)
    'icon_ext'   => 'jpg',                  // extension of icon image files (string)
];
