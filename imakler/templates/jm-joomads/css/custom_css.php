<?php

/*--------------------------------------------------------------
# Copyright (C) joomla-monster.com
# License: http://www.joomla-monster.com/license.html Joomla-Monster Proprietary Use License
# Website: http://www.joomla-monster.com
# Support: info@joomla-monster.com
---------------------------------------------------------------*/

//get direction
$direction = $this->params->get('direction', 'ltr');

// template layout
$templatewidthtype = $this->params->get('templateWidthType', '0');
$fluidwidth = $this->params->get('templateFluidWidth', '1221px');
$responsivelayout = $this->params->get('responsiveLayout', '1');

// font settings
$bodyfontcolor = $this->params->get('bodyFontColor', '#808080'); 
$bodyfontsize = $this->params->get('bodyFontSize', '14');
$bodyfonttype = $this->params->get('bodyFontType', '0');
$bodyfontfamily = $this->params->get('bodyFontFamily', 'Arial, Helvetica, sans-serif'); 
$bodycustomfont = $this->params->get('bodyCustomFont', 'Tahoma');  
$bodygooglewebfonturl = htmlspecialchars($this->params->get('bodyGoogleWebFontUrl'));
$bodygooglewebfontfamily = $this->params->get('bodyGoogleWebFontFamily');

$headingsfontsize = $this->params->get('headingsFontSize', '18');
$headingsfonttype = $this->params->get('headingsFontType', '0');
$headingsfontfamily = $this->params->get('headingsFontFamily', 'Arial, Helvetica, sans-serif'); 
$headingscustomfont = $this->params->get('headingsCustomFont', 'Tahoma');   
$headingsgooglewebfonturl = htmlspecialchars($this->params->get('headingsGoogleWebFontUrl'));
$headingsgooglewebfontfamily = $this->params->get('headingsGoogleWebFontFamily');

$djmenufontsize = $this->params->get('djmenuFontSize', '15');
$djmenufonttype = $this->params->get('djmenuFontType', '0');
$djmenufontfamily = $this->params->get('djmenuFontFamily', 'Arial, Helvetica, sans-serif');
$djmenucustomfont = $this->params->get('djmenuCustomFont', 'Tahoma');  
$djmenugooglewebfonturl = htmlspecialchars($this->params->get('djmenuGoogleWebFontUrl'));
$djmenugooglewebfontfamily = $this->params->get('djmenuGoogleWebFontFamily');

$articlesfontsize = $this->params->get('articlesFontSize', '20');
$articlesfonttype = $this->params->get('articlesFontType', '0');
$articlesfontfamily = $this->params->get('articlesFontFamily', 'Arial, Helvetica, sans-serif');
$articlescustomfont = $this->params->get('articlesCustomFont', 'Tahoma'); 
$articlesgooglewebfonturl = htmlspecialchars($this->params->get('articlesGoogleWebFontUrl'));
$articlesgooglewebfontfamily = $this->params->get('articlesGoogleWebFontFamily');

$advancedfontsize = $this->params->get('advancedFontSize', '18');
$advancedfonttype = $this->params->get('advancedFontType', '0');
$advancedfontfamily = $this->params->get('advancedFontFamily', 'Arial, Helvetica, sans-serif');
$advancedcustomfont = $this->params->get('advancedCustomFont', 'Tahoma');   
$advancedgooglewebfonturl = htmlspecialchars($this->params->get('advancedGoogleWebFontUrl'));
$advancedgooglewebfontfamily = $this->params->get('advancedGoogleWebFontFamily');
$advancedselectors = $this->params->get('advancedSelectors');

//grid layout
$gridright = $this->params->get('grid_right');
$gridcontent = $this->params->get('grid_content');

$gridright_fl = $this->params->get('grid_right_fl');
$gridcontent_fl = $this->params->get('grid_content_fl');

$gridright_res = $this->params->get('grid_right_res');
$gridcontent_res = $this->params->get('grid_content_res');

$gridright_fl_res = $this->params->get('grid_right_fl_res');
$gridcontent_fl_res = $this->params->get('grid_content_fl_res');

$gutter = $this->params->get('grid_gutter');
$gutter_fluid = $this->params->get('grid_gutter_fl');

$gutter_768 = $this->params->get('grid_gutter_768');
$gutter_1200 = $this->params->get('grid_gutter_768');

$gutter_fluid_768 = $this->params->get('grid_gutter_fl_res');
$gutter_fluid_1200 = $this->params->get('grid_gutter_fl_res');

//fixed column size
$columnright_tmp = $this->params->get('grid_right_fixed');
$columnright = ($columnright_tmp+$gutter);
$columnleft = $this->params->get('grid_left_fixed');

//fixed column size for 768px - 979px
$columnright_768_tmp = $this->params->get('grid_right_fixed_768');
$columnright_768 = ($columnright_768_tmp+$gutter_768);
$columnleft_768 = $this->params->get('grid_left_fixed_768');

//fixed column size for 1200px
$columnright_1200_tmp = $this->params->get('grid_right_fixed_1200');
$columnright_1200 = ($columnright_1200_tmp+$gutter_1200);
$columnleft_1200 = $this->params->get('grid_left_fixed_1200');

?>
 
<?php
// body font parameters
?>

body {
    color:<?php echo $bodyfontcolor; ?>;
    <?php 
    switch($bodyfonttype) {
        case "0":
            echo "font-family: ".$bodyfontfamily.";";
        break;
        case "1":
            echo "font-family: ".$bodycustomfont.";";
        break;
        case "2":       
            echo "font-family: ".$bodygooglewebfontfamily.";";
        break;
        default: 
            echo "font-family: Tahoma;";
    }
    ?>
    font-size: <?php echo $bodyfontsize; ?>px;
}

<?php
// module font parameters
?>

.jm-module .jm-title {
    <?php 
    switch($headingsfonttype) {
        case "0":
            echo "font-family: ".$headingsfontfamily.";";
        break;
        case "1":
            echo "font-family: ".$headingscustomfont.";";
        break;
        case "2":       
            echo "font-family: ".$headingsgooglewebfontfamily.";";
        break;
        default: 
            echo "font-family: Tahoma;";
    }
    ?>
    font-size: <?php echo $headingsfontsize; ?>px;
}

<?php
// djmegamenu font parameters
?>

.dj-megamenu li a.dj-up_a {
    <?php 
    switch($djmenufonttype) {
        case "0":
            echo "font-family: ".$djmenufontfamily.";";
        break;
        case "1":
            echo "font-family: ".$djmenucustomfont.";";
        break;
        case "2":       
            echo "font-family: ".$djmenugooglewebfontfamily.";";
        break;
        default: 
            echo "font-family: Tahoma;";
    }
    ?>
    font-size: <?php echo $djmenufontsize; ?>px;
}

<?php
// article font parameters
?>

.page-header h2,
h2.item-title,
.cat-children > h3,
.contact-category > h2,
.content-category > h2,
.weblink-category > h2,
.newsfeed > h2,
.newsfeed-category > h2,
.tag-category h2,
.category-list > h2,
.page-header > h1,
.blog > h2,
.blog-featured > h1,
h1.componentheading,
.dj-cat-title,
.view h2 {
    <?php 
    switch($articlesfonttype) {
        case "0":
            echo "font-family: ".$articlesfontfamily.";";
        break;
        case "1": 
            echo "font-family: ".$articlescustomfont.";";
        break;
        case "2":       
            echo "font-family: ".$articlesgooglewebfontfamily.";";
        break;
        default: 
            echo "font-family: Tahoma;";
    }
    ?>
    font-size: <?php echo $articlesfontsize; ?>px;
}

#jm-logo {
    <?php 
    switch($articlesfonttype) {
        case "0":
            echo "font-family: ".$articlesfontfamily.";";
        break;
        case "1": 
            echo "font-family: ".$articlescustomfont.";";
        break;
        case "2":       
            echo "font-family: ".$articlesgooglewebfontfamily.";";
        break;
        default: 
            echo "font-family: Tahoma;";
    }
    ?>
}


<?php
// advanced selectors font parameters
?>

<?php if($advancedselectors != '') {
    echo $advancedselectors; ?> {
    <?php 
    switch($advancedfonttype) {
        case "0":
            echo "font-family: ".$advancedfontfamily.";";
        break;
        case "1": 
            echo "font-family: ".$advancedcustomfont.";";
        break;
        case "2":       
            echo "font-family: ".$advancedgooglewebfontfamily.";";
        break;
        default: 
            echo "font-family: Tahoma;";
    }
    ?>
    font-size: <?php echo $advancedfontsize; ?>px;
}
<?php } ?>

<?php
// custom font
?>
 
.nav-tabs.nav-stacked li,
.nav-tabs.nav-stacked li a {
    <?php 
    switch($bodyfonttype) {
        case "0":
            echo "font-family: ".$bodyfontfamily.";";
        break;
        case "1":
            echo "font-family: ".$bodycustomfont.";";
        break;
        case "2":       
            echo "font-family: ".$bodygooglewebfontfamily.";";
        break;
        default: 
            echo "font-family: Tahoma;";
    }
    ?>
    font-size: <?php echo $bodyfontsize; ?>px!important;
}

<?php
// template fluid width
?>

.container-fluid {
    <?php if ($templatewidthtype != '0') : ?>
    max-width: <?php echo $fluidwidth; ?>;
    <?php endif; ?>
    padding-left: 20px;
    padding-right: 20px;
}

<?php
// min-width if the fixed width and the responsive layout disabled
// bootstrap width + padding ~ 1221 + 40px 
?>

<?php if (($responsivelayout == "0") && ($templatewidthtype == '0')) : ?>
#jm-allpage,
#jm-bar,
#jm-header-wrapper,
#jm-top,
#jm-main,
#jm-bottom,
#jm-footer {
    min-width: 1260px;
}

<?php endif; ?> 

<?php
// content positioning 
?>

#jm-header-in {
    margin-left: <?php echo $columnleft; ?>px;
}

#jm-header + #jm-header-mod {
    width: <?php echo $columnleft; ?>px;
}

<?php
/*
left content right
------------------------------------*/
?>

.lcr #jm-content-wrapper-in {
    margin-left: <?php echo $columnleft; ?>px;
}

.lcr #jm-content {
    margin-right: <?php echo $columnright; ?>px; 
}

.lcr #jm-right {
    margin-left: -<?php echo $columnright; ?>px;
}    

<?php
/*
left right content
------------------------------------*/
?>

.lrc #jm-content-wrapper,
.lrc #jm-middle-page {
    margin-left: -<?php echo ($columnleft+$columnright); ?>px;
}

.lrc #jm-content-wrapper-in {
    margin-left: <?php echo $columnleft; ?>px;
}

.lrc #jm-content {
    margin-left: <?php echo $columnright; ?>px;
}

<?php
/*
content right left
------------------------------------*/
?>

.clr #jm-content-wrapper-in {
    margin-right: <?php echo $columnleft; ?>px;
}

.clr #jm-content {
    margin-right: <?php echo $columnright; ?>px;
}

.clr #jm-left {
    margin-left: -<?php echo $columnleft; ?>px;
}

.clr #jm-right {
    margin-left: -<?php echo $columnright; ?>px;
}

<?php
/*
responsive 768 - 979
------------------------------------*/
?>

<?php if ($responsivelayout != "0") : ?> 
@media (min-width: 768px) and (max-width: 979px) {
    #jm-header-in {
        margin-left: <?php echo $columnleft_768; ?>px;
    }
    #jm-header + #jm-header-mod {
        width: <?php echo $columnleft_768; ?>px;
    }
    
    .lcr #jm-content-wrapper-in {
        margin-left: <?php echo $columnleft_768; ?>px;
    }
    
    .lcr #jm-content {
        margin-right: <?php echo $columnright_768; ?>px; 
    }
    
    .lcr #jm-right {
        margin-left: -<?php echo $columnright_768; ?>px;
    }    
    
    .lrc #jm-content-wrapper,
    .lrc #jm-middle-page {
        margin-left: -<?php echo ($columnleft_768+$columnright_768); ?>px;
    }
    
    .lrc #jm-content-wrapper-in {
        margin-left: <?php echo $columnleft_768; ?>px;
    }
    
    .lrc #jm-content {
        margin-left: <?php echo $columnright_768; ?>px;
    }
    
    .clr #jm-content-wrapper-in {
        margin-right: <?php echo $columnleft_768; ?>px;
    }
    
    .clr #jm-content {
        margin-right: <?php echo $columnright_768; ?>px;
    }
    
    .clr #jm-left {
        margin-left: -<?php echo $columnleft_768; ?>px;
    }
    
    .clr #jm-right {
        margin-left: -<?php echo $columnright_768; ?>px;
    }
} 
@media (min-width: 1240px) {
    #jm-header-in {
        margin-left: <?php echo $columnleft_1200; ?>px;
    }
    #jm-header + #jm-header-mod {
        width: <?php echo $columnleft_1200; ?>px;
    }
    
    .lcr #jm-content-wrapper-in {
        margin-left: <?php echo $columnleft_1200; ?>px;
    }
    
    .lcr #jm-content {
        margin-right: <?php echo $columnright_1200; ?>px; 
    }
    
    .lcr #jm-right {
        margin-left: -<?php echo $columnright_1200; ?>px;
    }    
    
    .lrc #jm-content-wrapper,
    .lrc #jm-middle-page {
        margin-left: -<?php echo ($columnleft_1200+$columnright_1200); ?>px;
    }
    
    .lrc #jm-content-wrapper-in {
        margin-left: <?php echo $columnleft_1200; ?>px;
    }
    
    .lrc #jm-content {
        margin-left: <?php echo $columnright_1200; ?>px;
    }
    
    .clr #jm-content-wrapper-in {
        margin-right: <?php echo $columnleft_1200; ?>px;
    }
    
    .clr #jm-content {
        margin-right: <?php echo $columnright_1200; ?>px;
    }
    
    .clr #jm-left {
        margin-left: -<?php echo $columnleft_1200; ?>px;
    }
    
    .clr #jm-right {
        margin-left: -<?php echo $columnright_1200; ?>px;
    }
} 
<?php endif; ?> 

<?php
/*
1200 grid if responsive off
------------------------------------*/
?>
<?php if ($responsivelayout == "0") : ?> 
.responsiveoff #jm-header-in {
    margin-left: <?php echo $columnleft_1200; ?>px;
}
.responsiveoff #jm-header + #jm-header-mod {
    width: <?php echo $columnleft_1200; ?>px;
}
    
.responsiveoff.lcr #jm-content-wrapper-in {
    margin-left: <?php echo $columnleft_1200; ?>px;
}

.responsiveoff.lcr #jm-content {
    margin-right: <?php echo $columnright_1200; ?>px; 
}

.responsiveoff.lcr #jm-right {
    margin-left: -<?php echo $columnright_1200; ?>px;
}    

.responsiveoff.lrc #jm-content-wrapper,
.responsiveoff.lrc #jm-middle-page {
    margin-left: -<?php echo ($columnleft_1200+$columnright_1200); ?>px;
}

.responsiveoff.lrc #jm-content-wrapper-in {
    margin-left: <?php echo $columnleft_1200; ?>px;
}

.responsiveoff.lrc #jm-content {
    margin-left: <?php echo $columnright_1200; ?>px;
}

.responsiveoff.clr #jm-content-wrapper-in {
    margin-right: <?php echo $columnleft_1200; ?>px;
}

.responsiveoff.clr #jm-content {
    margin-right: <?php echo $columnright_1200; ?>px;
}

.responsiveoff.clr #jm-left {
    margin-left: -<?php echo $columnleft_1200; ?>px;
}

.responsiveoff.clr #jm-right {
    margin-left: -<?php echo $columnright_1200; ?>px;
}
<?php endif; ?> 
