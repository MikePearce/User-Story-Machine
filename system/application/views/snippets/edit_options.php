<ul class="hidePrint">
    <li><a href="/stories/printer/<?= $id; ?>"><img src="/img/icons/Info.png" alt="View" /></a></li>
    <li><a href="/stories/delete/<?= $id; ?>" onClick="return confirm('Are you sure you want to delete this?');"><img src="/img/icons/Delete.png" alt="Delete" /></a></li>
    <li><a href="/stories/edit/<?= $id; ?>"><img src="/img/icons/Modify.png" alt="Modify" /></a></li>
    <? if (!$done) : ?>
    <li><a href="/stories/markdone/<?= $id; ?>/TRUE"><img src="/img/icons/Save.png" alt="Save" /></a></li>
    <? else: ?>
    <li><a href="/stories/markdone/<?= $id; ?>/FALSE"><img src="/img/icons/Load.png" alt="Load" /></a></li>
    <? endif; ?>
</ul>
