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
    //alert(province_option+'MYYYYYYYYYYYY');
    document.getElementById("province").innerHTML = province_option;
}

function changeProvince() {
     // get the folder name
    var country;
    country = document.getElementById('country').value;
    //alert(country);
    x_phpchangeProvince(country, 'main', changeProvince_cb);
}
var request_processed = 0;
function changeProvince_cb_ship(province_option) {
    //alert(province_option+'MYYYYYYYYYYYY');

    document.getElementById("shipprovince").innerHTML = province_option;
    if (request_processed == 1) {
        idx = document.getElementById('sel_province').selectedIndex;
        document.getElementById('shipsel_province').selectedIndex = idx;
    }
    request_processed = 0;
}

function changeProvince_ship() {
     // get the folder name
    var country;
    country = document.getElementById('shipcountry').value;
    //alert(country);
    x_phpchangeProvince(country, 'ship', changeProvince_cb_ship);
}

</script>
