<div class='comment-form'> 
    <form method=post>
		<?php if($comment->getProperties()['pageId']== 'hem'):?>
        <input type=hidden name="redirect" value="<?=$this->url->create('')."#comment"?>">
        <?php else:?>
		<input type=hidden name="redirect" value="<?=$this->url->create($comment->getProperties()['pageId'])."#comment"?>">
		<?php endif;?>
		<fieldset>
        <legend>Leave a comment</legend>
        <p><label>Comment:<br/><textarea name='content'><?=$comment->getProperties()['content']?></textarea></label></p>
        <p><label>Name:<br/><input type='text' name='name' value='<?=$comment->getProperties()['name']?>'/></label></p>
        <p><label>Homepage:<br/><input type='text' name='web' value='<?=$comment->getProperties()['web']?>'/></label></p>
        <p><label>Email:<br/><input type='text' name='mail' value='<?=$comment->getProperties()['mail']?>'/></label></p>
		<input type='hidden' name='pageId' value='<?=$comment->getProperties()['pageId']?>'/></label>
		<input type='hidden' name='postId' value='<?=$comment->getProperties()['id']?>'/></label>
        <p class=buttons>
            <input type='submit' name='doSave' value='Spara' onClick="this.form.action = '<?=$this->url->create('comment/save')?>'"/>
			<input type='reset' value='Reset'/> 
            <input type='submit' name='doCancel' value='Tillbaka' onClick="this.form.action = '<?=$this->url->create($_SERVER['HTTP_REFERER'].'#comment')?>'"/>
        </p>
        </fieldset>
    </form>
</div>
