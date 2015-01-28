<h1><?=$title?></h1>

<?php foreach ($tags as $tag) : ?>
		
		<a class="tag-list" href="<?=$this->url->create('threads/tagged/' . $tag->id) ?>"><span ><?=$tag->getProperties()["tag"]?> (<?=$tag->getProperties()["tagCount"]?>)</span></a> 
		
<?php endforeach; ?>