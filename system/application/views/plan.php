<?
    // Build the querystring:
    if ($dir)
    {
        $origDir = $dir;
        if (!$dirmod)
        {
            $dir = ($dir == 'ASC' ? 'DESC' : 'ASC');
        }
    }
    else {
        $dir = 'DESC';
        $origDir = $dir;
    }

?>
<link rel="stylesheet" type="text/css" href="/css/smoothness/jquery-ui-1.8.custom.css" />

<script type="text/javascript" src="/js/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="/js/jquery-ui-1.8rc3.custom.min.js"></script>
<script type="text/javascript" src="/js/jquery-ui-1.8.custom.min.js"></script>
<script type="text/javascript" src="/js/jquery.simpletip-1.3.1.min.js"></script>
<script type="text/javascript" src="/js/jquery.jeditable.mini.js"></script>
<style type="text/css">

    span.editinplace {
        width: 40px !important;
        display: inline;
}
span.editinplace form {
    width: 80px !important;
}
 span.editinplace input {
    width: 40px !important;
    height: 30px !important;
    font-family: verdana !important;
        font-size: 1em !important;
        padding: 3px !important;
        display: inline !important;
        clear: none !important;

}
    </style>

<script type="text/javascript">
$(document).ready(function(){

	$(function() {
		$(".sortable").sortable({ opacity: 0.6, cursor: 'move', update: function() {
			var order = $(this).sortable("serialize") + '&themeId=<?= $theme; ?>';
			$.post("/prioritise/save", order, function(theResponse){
                                $("#contentRight").fadeIn().html(theResponse).delay(2000).fadeOut();
			});
		}
		});
          $('.editinplace').editable('/stories/saveremaining', {
                 indicator : 'Saving...',
                 submit    : 'ok',
                callback : function(value, settings) {
                         $("#contentRight").fadeIn().html('Updated').delay(2000).fadeOut();
                     }
             });
             $("#datepicker").datepicker( { dateFormat: 'dd/mm/yy' } );
	});

        

   


});
</script>
<h1 class="hidePrint">Show all stories (filtered by Storyname/Theme)</h1>
<div id="contentRight" style="top: 45%; left: 35%; padding: 25px; font-weight: bold; z-index: 999; position: absolute; background: green; color: #fff; display: none;"></div>
    <div id="selector" class="hidePrint" style="float: left;">
        
        <?= form_open('stories/plan'); ?>
        Select stories/theme to show:
        <?= form_dropdown('themes', $themes, $theme, "onchange='this.form.submit();'"); ?>
        <?= form_close(); ?>
    </div>&nbsp;&nbsp;&nbsp;<br /><br />
    <div style="float: left;">
                <?= form_open('stories/plan', array('method'=>'GET')); ?>
            <input name="resourceHours" style="width: 40px;" value="<?= $resourceHours; ?>" />
            <input type="hidden" name="orderby" value="<?= $orderby; ?>" />
            <input type="hidden" name="dir" value="<?= $origDir; ?>" />
            <input type="hidden" name="theme" value="<?= $theme; ?>" />
            <input type="submit" value="set hours" />
        <?= form_close(); ?>
    </div>
     <div style="float: left; margin-left: 10px;">
    OR
     </div>
    <div style="float: left; margin-left: 10px;" id="delivery_date_estimate">
           <?= form_open('stories/plan', array('method'=>'GET')); ?>
            Peeps:<input name="peeps" style="width: 40px;" value="<?= $peeps; ?>" />
            Hours-a-day:<input name="hoursaday" style="width: 40px;" value="<?= $hoursaday; ?>" />
            Delivery Date:<input name="delivery" id="datepicker" style="width: 70px;" value="<?= $delivery; ?>" />
            <input type="hidden" name="orderby" value="<?= $orderby; ?>" />
            <input type="hidden" name="dir" value="<?= $origDir; ?>" />
            <input type="hidden" name="theme" value="<?= $theme; ?>" />
            Est. or Remaining: <select name="estOrRem" id="estOrRem"><option value="est"<?= ($estOrRem == 'est' ? ' selected' : ''); ?>>Est.</option><option value="rem"<?= ($estOrRem == 'rem' ? ' selected' : ''); ?>>Remaining</option></select>
            <input type="submit" value="set delivery" />
        <?= form_close(); ?>
    </div>

<br /><br />
<table cellpadding="3" cellspacing="4" border="1">
    <tr>
        <th>&nbsp;</th>
        <th><a nohref="/stories/plan/?orderby=themes.themeName&dir=<?= $dir; ?>&theme=<?= $theme; ?>">Theme</a></th>
        <th><a nohref="/stories/plan/?orderby=priorityOrder&dir=ASC&theme=<?= $theme; ?>">Priority</a></th>
        <th><a nohref="/stories/plan/?orderby=nickname&dir=<?= $dir; ?>&theme=<?= $theme; ?>">Story Nickname</a></th>
        <th><a nohref="/stories/plan/?orderby=estimate&dir=<?= $dir; ?>&theme=<?= $theme; ?>">Est.</a></th>
        <th><a nohref="/stories/plan/?orderby=estimate&dir=<?= $dir; ?>&theme=<?= $theme; ?>">Rem.</a></th>
        <th><a nohref="/stories/plan/?orderby=date_added&dir=<?= $dir; ?>&theme=<?= $theme; ?>">Date Added</a></th>
        <th><a nohref="/stories/plan/?orderby=date_modified&dir=<?= $dir; ?>&theme=<?= $theme; ?>">Date Modded</a></th>
         <?
            if ($workHours AND 1 == 0 OR $resourceHours AND 1 == 0):
        ?>
        <th>Delivered By</th>
        <? endif; ?>
        <th>&nbsp;</th>
        <th>&nbsp;</th>
    </tr>
    <?= /** This is for the select boxes **/form_open('stories/multiaction', array('method' => 'GET', 'id' => 'multiactionform')); ?>
    <tbody class="sortable">
        
<?
    // Reset some values
    $total = $cum = $cumRem = 0;
    $featureRelease = $dateRelease = FALSE;
    // Now, for each story, loop through
    foreach ($stories AS $story):
        
        // Set the acceptance
        $acceptance = "<br /><br />";

        // Let's start adding up the total
	$total = $total + $story->estimate;

        // Start the cumulative estimage
        $cum = $cum + $story->estimate;

        // Start the cumulative estimage
        $cumRem = $cumRem + $story->remaining;

        // Create the acceptance criteria blocks
        if (isset($story->acceptanceCriteria))
        {
            // For each decoded acceptance
            foreach( json_decode($story->acceptanceCriteria) AS $acc):
                if ($acc == '') continue;
                $acceptance .= " -". $acc ."<br /> ";
            endforeach;
        }

        // Do we need to add the date release
        if ($estOrRem != 'est')
        {
            $hours = $story->remaining;
            $cumulative = $cumRem;
        }
        else {
            $hours = $story->estimate;
            $cumulative = $cum;
        }

        // Do we need to add the feature release?
        if($resourceHours AND ($cumulative + $story->estimate) > $resourceHours AND !$featureRelease)
        {
            $featureRelease = TRUE;
            print '<tr><td style="cursor:move;" id="monkeh">X</td><td colspan="10">Feature Release</td></tr>';
        }

        if($workHours AND $cumulative > $workHours AND !$dateRelease)
        {
            $dateRelease = TRUE;
            print '<tr><td style="cursor:move;" id="monkeh">X</td><td colspan="10">'. ($estOrRem == 'est' ? 'Estimated Date' : 'Remaining Date') .' Release: '. $workHours .'hrs</td></tr>';
        }
 ?>

        <tr id="recordsArray_<?= $story->id; ?>">
        <td style="cursor:move;">X</td>
        <td><?= $story->themeName; ?></td>
        <td><?= $story->priorityOrder; ?></td>
        <td><label class="small" for="stories<?= $story->id; ?>" id="x<?= $cum; ?>"><?= $story->nickname; ?></label>
            <script type="text/javascript">
            // Selects one or more elements to assign a simpletip to
            $("#x<?= $cum; ?>").simpletip({

               // Configuration properties
               content: "<strong>As a</strong> <?= str_replace("\"", "'", $story->asA); ?>, <strong>I need</strong> <?= str_replace("\"", "'", $story->iNeed); ?>, <strong>so that</strong> <?= str_replace("\"", "'", $story->soThat); ?>.<?= str_replace("\"", "'", $acceptance); ?>",
               fixed: false

            });
            </script>
        </td>
        <td>
               <?= $story->estimate; ?> <sub style="font-size: 0.5em; float: right;">(<?= $cum; ?>)</sub>
        </td>
        <td><span class="editinplace" id="rem_<?= $story->id; ?>"><?= $story->remaining; ?></span> <sub style="font-size: 0.5em; float: right;">(<?= $cumRem; ?>)</sub></td>
        <? $date = date('d/m/Y', strtotime($story->date_added)); ?>
        <td><?= ($date == '1970/01/01' ? '-' : $date) ?></td>
        <? $date = date('d/m/Y', strtotime($story->date_modified)); ?>
        <td><?= ($date == '1970/01/01' ? '-' : $date) ?></td>
        <? if ($workHours AND 1 == 0 OR $resourceHours AND 1 == 0):?>
           <td><?= date('d/m/Y', strtotime("today + ". $cum ." hours")); ?></td>
        <? endif; ?>
        <td>
            <? $this->load->view('snippets/edit_options', $story); ?>
        </td>
        <td><input type="checkbox" name="stories[]" id="stories<?= $story->id; ?>" value="<?= $story->id; ?>" /></td>
    </tr>


<? endforeach; ?>
    </tbody>
    <tr>
	<td colspan="4" align="right">Total:</td>
	<td><?= $total; ?></td>
        <td align="right" colspan="5">
            
            Select action for multiple:
            <?= form_dropdown('action',
                        array(
                            '' => '--',
                            'del' => 'Delete',
                            'print' => 'Print',
                            'done' => 'Mark as complete',
                            'export' => 'Export as .csv file'
                            )
                    , '$selected_action', "onchange='checkAndSubmit();'"); ?>
        </td>
    </tr>
    <? form_close(); ?>
</table>
</div>

<script language="javascript">
    function checkAndSubmit() {
        if (confirm('Are you sure you want to do this?'))
        {
            document.getElementById('multiactionform').submit();
        }
        else {
            return false;
        }

    }

</script>
</body>
</html>
