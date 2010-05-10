

<h1 class="hidePrint">Show all stories (filtered by Storyname/Theme)</h1>
<br /><br />
    <div id="selector" class="hidePrint">
        
        <?= form_open('stories/allstories'); ?>
        Select theme to show:
        <?= form_dropdown('themes', $themes, $theme); ?>
        Show 'done' stories?: <?= form_checkbox(array(
                                            'name'      => 'showDone',
                                            'id'        => 'showDone',
                                            'value'     => '1',
                                            'checked'   => $showDoneChecked
                                            )); ?>
        <input type="submit" name="submit" value="Go" />
        <? form_close(); ?>
    </div>
<br /><br />
<?
    $i = 1;
    foreach ($stories AS $story):
?>
    
<div id="indexCard" class="eggs">
    <h1><?= $story->themeName .": ". $story->nickname; ?> - Estimate <sub>(Rem/Est) [<?= $story->remaining; ?>/<?= $story->estimate; ?>]</sub></h1>
    <table cellpadding="3" cellspacing="9">
        <tr>
            <td nowrap><strong>As a ...</strong></td>
            <td><?= $story->asA; ?></td>
        </tr>
        <tr>
            <td nowrap><strong>I need ...</strong></td>
            <td><?= $story->iNeed; ?></td>
        </tr>
        <tr>
            <td nowrap><strong>So that ...</strong></td>
            <td><?= $story->soThat; ?></td>
        </tr>
        <tr>
            <td colspan="2"><strong>Acceptance criteria</strong></td>
        </tr>
            <tr><td colspan="2">
                    <table>
                <?
                foreach( json_decode($story->acceptanceCriteria) AS $acc):
                        if ($acc == '') continue;
                ?>
                        <tr>
                            <td><input type="checkbox" /></td>
                            <td> <?= $acc ?></td>
                        </tr>
                <? endforeach; ?>
                        </table>
            </td></tr>
                <tr><td colspan="2">
                        <? $this->load->view('snippets/edit_options', $story); ?>
                    </td></tr>
    </table>

</div>
<br />
<? if ($i%2 == 0) : ?>
<div class="page-break"></div>
<? endif; ?>
<? $i ++; endforeach; ?>
</div>
</body>
</html>