<hr>

<div style="background-color:white; margin-bottom: 30px;">
<h2>Kommentarer</h2>

<?php if (is_array($comments)) : ?>
<div class='comments'>
<?php foreach ($comments as $postId => $comment) : ?>

<div class="item">

<?=$this->di->CommentController->get_gravatar($comment->getProperties()['mail']);?>
<a href="<?= $this->url->create('comment/edit/' . $comment->getProperties()['pageId']. '/' .$comment->getProperties()['id']) ?>">#<?=$postId?> </a> <br /> <br /> 

<section>
<?=nl2br($comment->getProperties()['content'])?> - <span id="name"><?=$comment->getProperties()['name']?></span>. 
<span id="timePosted">postad <?=$comment->getProperties()['timestamp'] ?></span>
<a href="<?= $this->url->create('comment/delete/'. $comment->getProperties()['pageId']. '/' . $comment->getProperties()['id']) ?>">(Ta bort)</a>
<section>
</div>
<?php endforeach; ?>
</div>
<?php endif; ?>
</div>