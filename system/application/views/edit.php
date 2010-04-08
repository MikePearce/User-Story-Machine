

<h1>Edit a story</h1>
<ul>
    <li><a href="/">Home</a></li>
    <li><a href="/welcome/allstories">Show all stories</a>
</ul>
<?
if (isset($message))
{
    print $message;
}
?>
<div id="full">
    <div id="form" class="col">
        <?= $form; ?>
    </div>

</div>
</body>
</html>