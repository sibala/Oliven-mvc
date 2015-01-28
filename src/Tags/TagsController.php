<?php

namespace Anax\Tags;
 
/**
 * A controller for users and admin related events.
 *
 */
class TagsController implements \Anax\DI\IInjectionAware
{
    use \Anax\DI\TInjectable;
	
	/**
    * Initialize the controller.
    *
    * @return void
    */
    public function initialize()
    {
        $this->tags = new \Anax\Tags\Tag();
        $this->tags->setDI($this->di);
    }
	
	/**
    * List all users.
    *
    * @return void
    */
    public function listAction()
    {
        //$tags = $this->tags->findAll();
		$tags = $this->tags->query("test_tag.id,test_tag.tag AS tag, COUNT(test_taggedthread.tag_id) AS tagCount")
			->join("taggedthread","test_tag.id = test_taggedthread.tag_id")
			->groupby("test_taggedthread.tag_id")
			->orderby("COUNT(test_taggedthread.tag_id) DESC")
			->execute();
        $this->theme->setTitle("Taggar");
        $this->views->add('threads/tags-list', [
            'tags' => $tags,
            'title' => "Taggar",
        ]);
    }
	
	public function popularTagsAction()
    {
		/*SELECT t.tag, COUNT(tt.tag_id) AS numberOftagged FROM `test_tag` AS t 
		LEFT JOIN test_taggedthread AS tt ON t.id = tt.tag_id
		group by tt.tag_id
		order by COUNT(tt.tag_id) DESC
		*/
        $tags = $this->tags->query("test_tag.id,test_tag.tag AS tag, COUNT(test_taggedthread.tag_id) AS tagCount")
			->join("taggedthread","test_tag.id = test_taggedthread.tag_id")
			->groupby("test_taggedthread.tag_id")
			->orderby("COUNT(test_taggedthread.tag_id) DESC")
			->limit(3)
			->execute();

        $this->theme->setTitle("Populära taggar");
        $this->views->add('threads/populartags-list', [
            'tags' => $tags,
            'title' => "Populära taggar",
        ]);
    }
	
	public function viewAction($id = null)
    {
		$question = $this->thread->find($id);
		$parent_id = $question->getProperties()['id'];
		
		$answers = $this->thread->query()
			->where("parent_id = {$parent_id}")
			->execute();
			
		session_name('create_answer');
		$this->di->session();

        $form = new \Anax\HTMLForm\CFormAnswer($parent_id);
        $form->setDI($this->di);
        $form->check();
 
        $this->theme->setTitle("Användar information");
        $this->views->add('questions/view', [
			'title' => 'Användar information',
            'question' => $question,
            'answers' => $answers,
			'form' => $form->getHTML()
        ]);
    }
	
	public function createAction(){
		session_name('create_tag');
		$this->di->session();

        $form = new \Anax\HTMLForm\CFormQuestion();
        $form->setDI($this->di);
        $form->check();

        $this->di->theme->setTitle("Ställ en fråga");
        $this->di->views->add('questions/question-form', [
            'title' => "Ställ en fråga",
            'form' => $form->getHTML()
        ]);
	}
	
	/**
	 * List all active and not deleted users.
	 *
	 * @return void
	 */
	public function listActiveAction()
	{
		$all = $this->users->query()
			->where('active IS NOT NULL')
			->andWhere('deleted IS NULL')
			->execute();
	 
		$this->theme->setTitle("Aktiva");
		$this->views->add('users/list-all', [
			'users' => $all,
			'title' => "Lista aktiva användare",
		]);
	}
	
	/**
	 * List all active and not deleted users.
	 *
	 * @return void
	 */
	public function listUnActiveAction()
	{
		$all = $this->users->query()
			->where('active IS NULL')
			->execute();
	 
		$this->theme->setTitle("Oaktiva");
		$this->views->add('users/list-all', [
			'users' => $all,
			'title' => "Lista oaktiva användare",
		]);
	}
		
	/**
	 * List all softdeleted users.
	 *
	 * @return void
	 */
	public function listSoftDeletedAction()
	{
		$all = $this->users->query()
			->Where('deleted IS NOT NULL')
			->execute();
	 
		$this->theme->setTitle("Preliminärt borttagna");
		$this->views->add('users/list-all', [
			'users' => $all,
			'title' => "Lista preliminärt borttagna användare",
		]);
	}
    
	/**
    * List user with id.
    *
    * @param int $id of user to display
    *
    * @return void
    */
    public function idAction($id = null)
    {
        $user = $this->users->find($id);
 
        $this->theme->setTitle("Användar information");
        $this->views->add('users/view', [
			'title' => 'Användar information',
            'user' => $user,
        ]);
    }
	
	/**
	 * Add new user.
	 *
	 * @param string $acronym of user to add.
	 *
	 * @return void
	 */

	
	/**
	 * Delete user.
	 *
	 * @param integer $id of user to delete.
	 *
	 * @return void
	 */
	public function deleteAction($id = null)
	{
		if (!isset($id)) {
			die("Missing id");
		}
	 
		$res = $this->users->delete($id);
	 
		$url = $this->url->create('');
		$this->response->redirect($url);
	}
	
	/**
	 * Delete (soft) user.
	 *
	 * @param integer $id of user to delete.
	 *
	 * @return void
	 */
	public function softDeleteAction($id = null)
	{
		if (!isset($id)) {
			die("Missing id");
		}
	 
		$now = date(DATE_RFC2822);
	 
		$user = $this->users->find($id);
	 
		$user->deleted = $now;
		$user->save();
	 
		$url = $this->url->create('users/id/' . $id);
		$this->response->redirect($url);
	}
	
	/**
	 * Undo deleted (soft) user.
	 *
	 * @param integer $id of user to undo delete.
	 *
	 * @return void
	 */
	public function undoSoftDeleteAction($id = null)
	{
		if (!isset($id)) {
			die("Missing id");
		}
	 
		$user = $this->users->find($id);
	 
		$user->deleted = null;
		$user->save();
	 
		$url = $this->url->create('users/id/' . $id);
		$this->response->redirect($url);
	}
	
	/**
	 * Activate user.
	 *
	 * @param integer $id of user to activate.
	 *
	 * @return void
	 */
	public function activateAction($id = null)
	{
		if (!isset($id)) {
			die("Missing id");
		}
	 
		$now = date(DATE_RFC2822);
	 
		$user = $this->users->find($id);
	 
		$user->active = $now;
		$user->save();
	 
		$url = $this->url->create('users/id/' . $id);
		$this->response->redirect($url);
	}
	
		/**
	 * Unactivate user.
	 *
	 * @param integer $id of user to unactivate.
	 *
	 * @return void
	 */
	public function deActivateAction($id = null)
	{
		if (!isset($id)) {
			die("Missing id");
		}
	 
		$user = $this->users->find($id);
	 
		$user->active = null;
		$user->save();
	 
		$url = $this->url->create('users/id/' . $id);
		$this->response->redirect($url);
	}

	public function profileAction($id = null){
	
		if (!isset($id)) {
			die("Missing id");
		}
	 
		$user = $this->users->find($id);
		$user = $user->getProperties();
	 
		session_name('update_user');
		$this->di->session();

        $form = new \Anax\HTMLForm\CFormProfileUpdate($user);
        $form->setDI($this->di);
        $form->check();

        $this->di->theme->setTitle("Uppdatera användare");
        $this->di->views->add('users/user-form', [
            'title' => "Uppdatera användare",
            'content' => $form->getHTML()
        ]);
	}
	

	public function loginAction(){
		session_name('login_user');
		$this->di->session();

        $form = new \Anax\HTMLForm\CFormLogin();
        $form->setDI($this->di);
        $form->check();

        $this->di->theme->setTitle("Logga in");
        $this->di->views->add('users/user-form', [
            'title' => "Logga in",
            'content' => $form->getHTML()
        ]);
	}
	public function logoutAction(){
		$this->session->set('userID', null);
		/*$session = $this->di->session->get('userID');
		unset($session);*/
		$url = $this->url->create('');
		$this->response->redirect($url);
	}
}