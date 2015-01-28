<h1><?=$title?></h1>


<?php foreach ($questions as $question) : ?>
	<div class="question-list" >
		<span class="user-list"style="float:right;">
			<?=$this->di->UsersController->get_gravatar($question->userEmail);?>
			AV <a href="<?=$this->url->create('users/view/' . $question->userId) ?>"><?=$question->userAcronym?></a><br/>
			Frågan skapades: <?=$question->timestamp?>
			<?php //echo $user->getProperties()["popularUsers"]?>
		</span>
		<a href="<?=$this->url->create('threads/view/' . $question->id) ?>"><?=$question->headline?></a>
		<br /><?php echo  $question->answerCount ? $question->answerCount . " svar" : "0 svar";?>
		<br /><br />
	</div>
<?php endforeach; ?>