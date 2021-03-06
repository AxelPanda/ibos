<?php

defined("IN_MODULE_ACTION") || exit("Access Denied");
return array(
    "param"         => array(
        "name"        => "通讯录",
        "category"    => "人力资源",
        "description" => "提供企业员工通讯录",
        "author"      => "gzhzh @ IBOS Team Inc",
        "version"     => "1.0",
        "indexShow"   => array("link" => "contact/default/index")
    ),
    "configure"     => array(
        "modules"    => array("contact"),
        "import"     => array("application.modules.contact.controllers.*", "application.modules.contact.model.*", "application.modules.contact.utils.*"),
        "components" => array(
            "messages" => array(
                "extensionPaths" => array("contact" => "application.modules.contact.language")
            )
        )
    ),
    "authorization" => array(
        "contact" => array(
            "type"          => "node",
            "name"          => "通讯录",
            "group"         => "通讯录",
            "controllerMap" => array(
                "default"  => array("index", "ajaxApi", "export"),
                "constant" => array("index")
            )
        )
    )
);
