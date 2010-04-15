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
   
</div>
</body>
</html>