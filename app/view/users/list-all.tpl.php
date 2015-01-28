<h1><?=$title?></h1>
<div>
<?php foreach ($users as $user) : ?>
<span class="user-list">
<?=$this->di->UsersController->get_gravatar($user->getProperties()['email']);?>
AV <a href="<?=$this->url->create('users/view/' . $user->id) ?>"><?=$user->getProperties()["acronym"]?></a><br/>
medlem sedan: <?=$user->getProperties()["created"]?>
<?php //echo $user->getProperties()["popularUsers"]?>
</span>
<?php endforeach; ?>
</div>