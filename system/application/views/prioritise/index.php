<style type="text/css">

#contentLeft {
	float: left;
	width: 400px;
}
#contentLeft ul {
    display: block;
}
#contentLeft li {
        cursor: pointer;
        cursor: hand;
        display: block;
	list-style: none;
	margin: 0 0 4px 0;
	padding: 10px;
	background-color:#00CCCC;
	border: #CCCCCC solid 1px;
	color:#fff;
}

#contentRight {
    float: right;
}

#contentLeft li.divider {
        cursor: pointer;
        cursor: hand;
        display: block;
	list-style: none;
	background-color:red;
	color:#fff;
}
</style>
<script type="text/javascript" src="/js/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="/js/jquery-ui-1.8rc3.custom.min.js"></script>

<script type="text/javascript">
$(document).ready(function(){

	$(function() {
		$("#contentLeft ul").sortable({ opacity: 0.6, cursor: 'move', update: function() {
			var order = $(this).sortable("serialize") + '&themeId=<?= $id; ?>';
			$.post("/prioritise/save", order, function(theResponse){
				$("#contentRight").html(theResponse);
			});
		}
		});
	});

        // On click, add a bar which has a label

});
</script>
<br /><br />
<div id="contentLeft">
    <ul>
        <li class="divider"></li>
        <?
            $i = 1;
        foreach ($stories AS $story): ?>
            <li id="recordsArray_<?= $story->id; ?>">
                <?= $story->estimate; ?> :: <?= $story->nickname; ?>
            </li>
        <? $i++; endforeach; ?>
    </ul>
</div>

<div id="contentRight">
    <p>&nbsp; </p>
</div>