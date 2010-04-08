<?
    // User story form
    echo validation_errors();

    if (isset($edit) AND $edit)
    {
        echo form_open('stories/edit', array('class' => 'form', 'id' => 'storyPost'), array('id' => $story[0]->id));
        $selected_theme = $story[0]->themeId;
    }
    else {
        echo form_open('stories/index', array('class' => 'form', 'id' => 'storyPost'));
        $selected_theme = '';
    }

?>
        <label for="storyName">Theme:</label><br /><br />
        <?= form_dropdown('themes', $themes, $selected_theme); ?>
         or add new:
        <input
            type="text"
            name="themeName"
            id="themeName"
            value=""
        /><br /><br />
        <label for="nickname">Story Nickname</label><br /><br />
        <input
            type="text"
            name="nickname"
            value="<?php echo set_value('nickname', (isset($story[0]->nickname) ? $story[0]->nickname : '')); ?>"
            id="nickname"
        /><br /><br />
        <label for="asA">As a...</label><br />
        <input
            type="text"
            name="asA"
            id="asA"
            value="<?php echo set_value('asA', (isset($story[0]->asA) ? $story[0]->asA : '')); ?>"
        /><br /><br />
        <label for="iNeed">I need ...</label><br />
        <textarea 
            name="iNeed"
            id="iNeed"
            cols="40"
		style="width:350px;height:60px;"
            rows="2"><?php echo set_value('iNeed', (isset($story[0]->iNeed) ? $story[0]->iNeed : '')); ?></textarea><br /><br />
        <label for="soThat">So that ...</label><br />
        <textarea 
            name="soThat"
            id="soThat"
            cols="40"
		style="width:350px;height:60px;"
            rows="2"><?php echo set_value('soThat', (isset($story[0]->soThat) ? $story[0]->soThat : '')); ?></textarea><br /><br />
        <label for="soThat">Acceptance Criteria</label><br />
        <p><em>Enter each criteria on a new line</em></p>
        <textarea 
            name="acceptanceCriteria"
            id="acceptanceCriteria"
            cols="40"
		style="width:350px;height:100px;"
            rows="5"><?
                $acceptance = '';
                if (isset($story[0]->acceptanceCriteria))
                {
                    foreach( json_decode($story[0]->acceptanceCriteria) AS $acc):
                            if ($acc == '') continue;
                            $acceptance .= $acc ."\n";
                    endforeach;
                }
                echo set_value('acceptanceCriteria', $acceptance); ?></textarea><br />
        <label for="nickname">Estimate</label>: 
        <input
            type="text"
            name="estimate"
            value="<?php echo set_value('estimate', (isset($story[0]->estimate) ? $story[0]->estimate : '')); ?>"
            id="estimate"
        /><br /><br />
<?
    if (isset($edit) AND $edit)
    {
        echo form_submit('submit', 'Update Story');
    }
    else {
        echo form_submit('submit', 'Add Story');
        echo form_submit('reset', 'Reset');
    }
    echo form_close();

?>
