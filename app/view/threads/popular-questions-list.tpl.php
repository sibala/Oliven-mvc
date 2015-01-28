<h1><?=$title?></h1>

<?php foreach ($questions as $question) : ?>

		<?=$this->di->UsersController->get_gravatar($question->email, 20);?>
		<a href="<?=$this->url->create('threads/view/' . $question->id) ?>"><?=$question->getProperties()["headline"]?></a>
		<span class="time"><?=$question->getProperties()["timestamp"]?></span>
		<br /><br />
<?php endforeach; ?>