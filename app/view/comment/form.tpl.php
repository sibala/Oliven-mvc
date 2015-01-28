<div class='comment-form' id='comment'>
    <form method=post>
        <input type=hidden name="redirect" value="<?=$this->request->getCurrentUrl()."#comment"?>">
        <fieldset>
        <legend>Lämna en kommentar</legend>
        <p><label>Kommentar:<br/><textarea name='content'><?=$content?></textarea></label></p>
        <p><label>Namn:<br/><input type='text' name='name' value='<?=$name?>'/></label></p>
        <p><label>Hemsida:<br/><input type='text' name='web' value='<?=$web?>'/></label></p>
        <p><label>E-post:<br/><input type='text' name='mail' value='<?=$mail?>'/></label></p>
		<input type="hidden" name="pageId" value="<?=$pageId?>" />
        <p class=buttons>
            <input type='submit' name='doCreate' value='Kommentera' onClick="this.form.action = '<?=$this->url->create('comment/add')?>'"/>
            <input type='reset' value='Reset'/>
            <input type='submit' name='doRemoveAll' value='Radera alla' onClick="this.form.action = '<?=$this->url->create('comment/remove-all')?>'"/>
        </p>
        <output><?=$output?></output>
        </fieldset>
    </form>
</div>
