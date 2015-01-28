<h1><?=$title?>: <?=$question[0]->headline?></h1>
<div class="question-list" >
	<span class="users-question">
		<?=$this->di->UsersController->get_gravatar($question[0]->userEmail);?>
		AV <a href="<?=$this->url->create('users/view/' . $question[0]->userId) ?>"><?=$question[0]->userAcronym?></a><br/>
		Frågan skapades: <?=$question[0]->timestamp?>
		<?php //echo $user->getProperties()["popularUsers"]?>
	</span>
	<p class="question-content">
		<br /><?=$question[0]->content?>
		<br /><br />
		<?php if(isset($tags)): ?>
			<?php foreach($tags as $tag){
					echo "<a class='tag-list' href=". $this->url->create('threads/tagged/' . $tag->id). "><span>" . $tag->tag."</span></a> ";
				}
		?>
	</p>
	<?php endif; ?>
	<div class='comment-form'>
	<?php if($this->session->get("userID") !== null): ?>

		<form method=post>
			<input type="hidden" name="redirect" value="<?=$this->url->create('threads/view/'. $question[0]->getProperties()["id"])?>">
			<input type='hidden' name='thread_id' value='<?=$question[0]->getProperties()["id"]?>'/>
			<input type='text' name='content' placeholder='Skriv en kommentar' />
			<span class=buttons>
				<input type='submit' name='doCreate' value='Skicka' onClick="this.form.action = '<?=$this->url->create('comment/add')?>'"/>
			</span>

		</form>
	<?php endif; ?>
	
	 <table id="user_table">
		<?php foreach($questionComments as $comment){ ?>
		<tr>
			<td><?=$this->di->UsersController->get_gravatar($question[0]->userEmail, 20);?></td>
			<td><?=$comment->getProperties()["content"]?></td>
			<td class="time" > <?=$comment->getProperties()["acronym"]?> sedan <?=$comment->getProperties()["timestamp"]?></td>
		</tr>
		<?php } ?>
	</table>
	</div>
</div>
<h1>Svar: <?=$question[0]->getProperties()["headline"]?></h1>

<?php foreach($answers as $answer){ ?>
<div class="question-list" >
	<p class="question-content">
		<span class="users-question">
			<?=$this->di->UsersController->get_gravatar($answer->email);?>
			AV <a href="<?=$this->url->create('users/view/' . $answer->userId) ?>"><?=$answer->acronym?></a><br/>
			Frågan skapades: <?=$answer->timestamp?>
			<?php //echo $user->getProperties()["popularUsers"]?>
		</span>
		<td><?=$answer->getProperties()["content"]?></td>
	</p>
	
	<div class='comment-form'>
	<?php if($this->session->get("userID") !== null): ?>
		<form method=post>
			<input type="hidden" name="redirect" value="<?=$this->url->create('threads/view/'. $question[0]->getProperties()["id"])?>">
			<input type='hidden' name='thread_id' value='<?=$answer->getProperties()["thread_id"]?>'/>
			<input type='text' name='content' placeholder='Skriv en kommentar' />
			<span class=buttons>
				<input type='submit' name='doCreate' value='Skicka' onClick="this.form.action = '<?=$this->url->create('comment/add')?>'"/>
			</span>
		</form>
	
	<?php endif; ?>
	<?php if(isset($answerComments)){ ?>
		 <table id="user_table">

			<?php foreach($answerComments as $comment){ ?>
				<?php if($comment->thread_id === $answer->thread_id){ ?>
					<tr>
						<td><?=$this->di->UsersController->get_gravatar($question[0]->userEmail, 20);?></td>
						<td><?=$comment->getProperties()["content"]?></td>
						<td class="time" > <?=$comment->getProperties()["acronym"]?> sedan <?=$comment->getProperties()["timestamp"]?></td>
					</tr>
				<?php } ?>
			<?php } ?>
		</table>
	<?php } ?>
	</div>
</div>

<?php } ?>

<?php if($this->session->get("userID") !== null): ?>
	<?=$form ?>
<?php else: ?>
	<?=$message ?> 
<?php endif; ?>