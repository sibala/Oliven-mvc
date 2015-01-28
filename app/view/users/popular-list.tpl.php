<h1><?=$title?></h1>
<div>
<?php foreach ($users as $user) : ?>
<div class="active-user-list">
<?=$this->di->UsersController->get_gravatar($user->getProperties()['email']);?>
AV <a href="<?=$this->url->create('users/view/' . $user->id) ?>"><?=$user->getProperties()["acronym"]?></a><br/>
medlem sedan: <?=$user->getProperties()["created"]?>
<?php //echo $user->getProperties()["popularUsers"]?>
</div>
<?php endforeach; ?>
</div>