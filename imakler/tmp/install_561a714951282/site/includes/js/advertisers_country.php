<?php
/*------------------------------------------------------------------------
# com_adagency
# ------------------------------------------------------------------------
# author    iJoomla
# copyright Copyright (C) 2013 ijoomla.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.ijoomla.com
# Technical Support:  Forum - http://www.ijoomla.com/forum/index/
-------------------------------------------------------------------------*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
?>

<script>
<?php
sajax_show_javascript();
?>

function changeProvince_cb(province_option) {
    document.getElementById("province").innerHTML = province_option;
}

function changeProvince() {
    var country;
    country = document.getElementById('country').value;
    x_phpchangeProvince(country, 'main', changeProvince_cb);
}
var request_processed = 0;
function changeProvince_cb_ship(province_option) {
    document.getElementById("shipprovince").innerHTML = province_option;
    if (request_processed == 1) {
        idx = document.getElementById('sel_province').selectedIndex;
        document.getElementById('shipsel_province').selectedIndex = idx;
    }
    request_processed = 0;
}

function changeProvince_ship() {
    var country;
    country = document.getElementById('shipcountry').value;
    x_phpchangeProvince(country, 'ship', changeProvince_cb_ship);
}
</script>
