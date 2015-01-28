<h1><?=$title?></h1>
<?php echo isset($message) ? $message : null;?>
<?=$form?>

<?php if (isset($links)) : ?>
<ul>
<?php foreach ($links as $link) : ?>
<li><a href="<?=$link['href']?>"><?=$link['text']?></li>
<?php endforeach; ?>
</ul>
<?php endif; ?>