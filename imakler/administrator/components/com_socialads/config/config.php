<?php


$socialads_config = array(
'integration' => '2',
'priority_random' => '0',
'priority' => array('0' => '0','1' => '1','2' => '2'),
'geo_target' => '0',
'geo_opt' => array('0' => 'byregion','1' => 'bycity'),
'context_target' => '0',
'context_target_keywordsearch' => '0',
'context_target_param' => 'option=com_search&view=search|searchword
option=com_zoo&view=search&controller=search&task=search|searchtext
option=com_finder&view=search|q
option=com_community&view=search|q',
'context_target_metasearch' => '1',
'context_target_smartsearch' => '0',
'contextual_smartsearch_cron_batchsize' => '20',
'select_campaign' => '0',
'camp_currency_pre' => '10',
'camp_currency_daily' => '5',
'pricing_opt' => array('0' => '2'),
'zone_pricing' => '1',
'clicks_price' => '.50',
'impr_price' => '.05',
'date_price' => '20',
'show_slab' => '0',
'show_per_day_opt' => '1',
'slab' => array('0' => array('label' => 'Week','duration' => '7','price' => '15'),'1' => array('label' => 'Month','duration' => '30','price' => '5')),
'charge' => '1',
'balance' => '10',
'gateways' => array('0' => 'milicart','1' => 'authorizenet','2' => 'paypal','3' => 'payu'),
'currency' => 'USD',
'article' => '0',
'tnc' => '1',
'recure_enforce' => '0',
'ad_type_allow' => array('0' => 'text_img','1' => 'text','2' => 'img'),
'ad_site' => '0',
'own_ad' => '0',
'sa_reg_show' => '1',
'frm_link' => '0',
'display_reach' => '0',
'estimated_reach' => '100',
'image_size' => '1024',
'allow_flash_ads' => '1',
'allow_vid_ads' => '1',
'allow_vid_ads_autoplay' => '0',
'approval' => '1',
'timeimpressions' => '60',
'timeclicks' => '60',
'ignore' => '1',
'feedback' => '1',
'ignore_affiliate' => '0',
'se_jbolo' => '0',
'se_addthis' => '0',
'sa_addthis_pub' => '',
'enable_caching' => '0',
'cache_time' => '3600',
'load_bootstrap' => '0',
'load_jqui' => '0',
'cron_key' => '1234',
'arch_stats' => '1',
'arch_stats_day' => '30',
'week_mail' => '0',
'intro_msg' => 'Hey [SEND_TO_NAME]!,
Thank you for advertising with us. Here is the summary of the performance of all your advertisements on [SITENAME] for the last week!'
)


?>