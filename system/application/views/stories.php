<p>
    Here you can write user stories and then print them onto the correct sized cards!
</p>
<div id="full">
    <div id="form" class="col">
        <?= $form; ?>
    </div>
    <div id="list" class="col">
        <h2>Last 5 Stories</h2>
        <?
            foreach ($stories AS $story):
        ?>
        <div class="story">
            <h3><?= $story->themeName; ?></h3>
            <p><strong>As a ...</strong> <?= $story->asA; ?></p>
            <P><strong>I need ...</strong> <?= $story->iNeed; ?></P>
            <p><strong>So that ...</strong> <?= $story->soThat; ?>
                <? $this->load->view('snippets/edit_options', $story); ?>
            
        </div>
        <?
            endforeach;
        ?>
    </div>
    <div id="themes" class="col">
        <h2>Themes</h2>
        <table cellpadding="2" cellspacing="2">
        <? foreach ($themes AS $theme_id => $name) : ?>
            <tr><td><a href="/stories/allstories/<?= $theme_id; ?>"><?= $name; ?></a></td><td><a href="/prioritise/index/<?= $theme_id; ?>">Prioritise</a></td></tr>
        <? endforeach; ?>
        </table>
        

    </div>
</div>
</body>
</html>