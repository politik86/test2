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

<script type="text/javascript">
var goComp = {
    scroll : function() {
        var api = ADAG('#adagency_log').jScrollPane({
                        showArrows:true,
                        maintainPosition: false
                    }).data('jsp');
        this.scrollPane = api;
    },
    display : function (msg) {
        var content = ADAG('#loginfo').html();
        ADAG('#loginfo').html(content + "<br /> - " + msg);
    },
    ends: function() {
        var button_style = "style='margin-top: 20px; width: 167px; height: 20px;'";
        var button = "<div id='adag-next' " + button_style + " >";
        button += " >> <?php echo JText::_('ADAG_BACK_TO_REPS'); ?></div>"

        ADAG('#compress_adag').text('<?php echo JText::_('ADAG_COMP_END'); ?>');
        ADAG('#comp_load').hide();

        ADAG('#adag-next').live('click', function () {
            location.href = "<?php
                echo JURI::root() . 'administrator/index.php?option=com_adagency&controller=adagencyReports';
            ?>";
        });

        ADAG(button).insertAfter('#adagency_log');
    },
    ajaxCall: function (step, camp) {
        var that = this, data_type = 'html', to_display = '', campaign_id = 0;
        if (step == '1') {
            data_type = 'json';
        } else {
            if (step == '2c') {
                to_display = '<?php echo JText::_('ADAG_COMP_CLICKS_FOR'); ?> ';
            } else if (step == '2i') {
                to_display = '<?php echo JText::_('ADAG_COMP_IMPR_FOR'); ?> ';
            }
            this.display( to_display + "'" + camp[1] + "'");
            // reinitialize the scrollPane once new content has been added
            this.scrollPane.reinitialise();
            campaign_id = camp[0];
        }
        return ADAG.ajax({
            url: 'index.php',
            data: {
                'option': 'com_adagency',
                'controller': 'adagencyReports',
                'task': 'compdata',
                'step': step,
                'cid': campaign_id,
                'no_html': '1',
                'tmp': 'component'
            },
            type: 'GET',
            dataType: data_type,
            success: function (msg) {
                if (step == '1') {
                    that.camps = msg;
                }
            }
        });
    },
    getCamps: function () {
        return this.ajaxCall(1,0);
    },
    compClicks: function (camp) {
        return this.ajaxCall('2c', camp);
    },
    compImpr: function (camp) {
        return this.ajaxCall('2i', camp);
    },
    each: function (camps) {
        var that = this;
        // if there are no campaigns, means we're finished
        if (camps.length == 0) {
            that.ends();
        // else we continue processing each campaign
        } else {
            ADAG.when(that.compClicks(camps[0])).then(function () {
                window.setTimeout(function () {
                    ADAG.when(that.compImpr(camps[0])).then(function () {
                        window.setTimeout(function () {
                            that.each(camps.slice(1));
                        } ,3000);
                    }).fail(function () {
                        alert('<?php echo JText::_('ADAG_ERROR_OCC_DATA'); ?>');
                    });
                }, 3000);
            }).fail(function () {
                alert('<?php echo JText::_('ADAG_ERROR_OCC_DATA'); ?>');
            });
        }
    },
    init: function () {
        var that = this;
        that.display('<?php echo JText::_('ADAG_GETTING_EXPCAMP'); ?>');
        that.scroll();
        ADAG.when(that.getCamps()).then(function () {
            // console.log(that.camps);
            if (that.camps.length) {
                that.display('<?php echo JText::_('ADAG_FOUND_X_EXP_CAMPS'); ?> ' + that.camps.length);
                that.each(that.camps);
            } else {
                that.display('<?php echo JText::_('ADAG_NO_STATS_COMPR'); ?>');
                that.ends();
            }
        }).fail(function () {
            alert('<?php echo JText::_('ADAG_ERROR_RETR_CAMP'); ?>');
        });
    }
}

ADAG(function() {
    goComp.init();
});
</script>
