<?php
/*
 * @ PHP 7.2
 * @ Decoder version : 1.0.0.2
 * @ Release on : 05/03/2021
 * @ Website    : http://EasyToYou.eu
 */

Illuminate\Support\Facades\Route::get("/avatar/{hash}", function ($hash) {
    $size = 100;
    $icon = new Jdenticon\Identicon();
    $icon->setValue($hash);
    $icon->setSize($size);
    $style = new Jdenticon\IdenticonStyle();
    $style->setBackgroundColor("#21232a");
    $icon->setStyle($style);
    $icon->displayImage("png");
    return response("")->header("Content-Type", "image/png");
});

Illuminate\Support\Facades\Route::get("/{vue_capture?}", function () {
    return view("layouts.app");
})->where("vue_capture", "[\\/\\w\\:.-]*");

?>