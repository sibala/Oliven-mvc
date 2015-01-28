<span class='login'><?php if($this->session->get("userID") !== null):?>
<a href="<?=$this->url->create('users/profile/' . $this->di->session->get("userID"))?>">Profil</a> |
<a href="<?=$this->url->create('users/logout')?>">Logga ut</a>  
<?php else:?>
<a href="<?=$this->url->create('users/create')?>">Registrera dig</a> |
<a href="<?=$this->url->create('users/login')?>">Logga in</a>  
<?php endif;?>

</span>
<img class='sitelogo' src='<?=$this->url->asset("img/olives-logo.png")?>' alt='Anax Logo'/>
<span class='sitetitle'><?=isset($siteTitle) ? $siteTitle : "Anax PHP framework"?></span>
<span class='siteslogan'><?=isset($siteTagline) ? $siteTagline : "Reusable modules for web development"?></span>
