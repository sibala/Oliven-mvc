<h1><?=$title?></h1>
	<div class="user-info">
	
		<?=$this->di->UsersController->get_gravatar($user->getProperties()['email']);?><br />
		<table>
		<tr>
			<td><b>Användare: </b> </td><td><?=$user->getProperties()["acronym"]?></td>
		</tr>
		<tr>
			<td><b>Epost: </b> </td><td><?=$user->getProperties()["email"]?><br /></td>
		</tr>
		<tr>
			<td><b>Namn: </b> </td><td><?=$user->getProperties()["name"]?><br /></td>
		</tr>
		<tr>
			<td><b>Medlem: </b> </td><td><?=$user->getProperties()["created"]?></td>
		</tr>
		</table>
	</div>
	<h1>Frågor ställda</h1>
<?php if(!empty($questions)): ?>
	<?php foreach ($questions as $question) : ?>

			<?=$this->di->UsersController->get_gravatar($question->email, 20);?>
			<a href="<?=$this->url->create('threads/view/' . $question->id) ?>"><?=$question->getProperties()["headline"]?></a>
			<span class="time"><?=$question->getProperties()["timestamp"]?></span>
			<br /><br />
	<?php endforeach; ?>
<?php else: ?>
	Användaren har inga ställda frågor
<?php endif; ?>
	<h1>Frågor Besvarade</h1>
	<?php if(!empty($answers)): ?>
		<?php foreach($answers as $answer): ?>
			<?=$this->di->UsersController->get_gravatar($question->email, 20);?>
			<a href="<?=$this->url->create('threads/view/' . $answer->id) ?>"><?=$answer->getProperties()["headline"]?></a>
			<span class="time"><?=$answer->getProperties()["timestamp"]?></span>
			<br /><br />

		<?php endforeach; ?>
	<?php else: ?>
		Användaren har inte besvarat frågor
	<?php endif; ?>
	
</table>