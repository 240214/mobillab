<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 17.04.2018
 * Time: 23:53
 */

do_settings_sections($this->slug);

$country = WcPinLoader::instance()->geo->get_country();
WcPinLoader::instance()->_debug($country->country->isoCode);
