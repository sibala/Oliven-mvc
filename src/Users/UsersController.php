<?php

namespace Anax\Users;
 
/**
 * A controller for users and admin related events.
 *
 */
class UsersController implements \Anax\DI\IInjectionAware
{
    use \Anax\DI\TInjectable;
	
	/**
    * Initialize the controller.
    *
    * @return void
    */
    public function initialize()
    {
        $this->users = new \Anax\Users\User();
        $this->users->setDI($this->di);
    }
	
	/**
    * List all users.
    *
    * @return void
    */
    public function listAction()
    {
        $users = $this->users->findAll();
        $this->theme->setTitle("Användare");
        $this->views->add('users/list-all', [
            'users' => $users,
            'title' => "Användare",
        ]);
    }
	
	/**
	 * list most active users.
	 *
	 * @return void
	 */
	public function popularAction()
	{
	    $users = $this->users->query("test_user.id, test_user.acronym, test_user.name, test_user.email, test_user.created, COUNT(test_thread.user_id) AS popularUsers")
			->join("thread","test_user.id = test_thread.user_id")
			->groupby("test_thread.user_id")
			->orderby("COUNT(test_thread.user_id) DESC")
			->limit(3)
			->execute();
		
		$this->theme->setTitle("Aktiva användare");
        $this->views->add('users/popular-list', [
            'users' => $users,
            'title' => "Aktiva användare",
        ]);		
	}
	
	/**
	 * view user info and user activities.
	 *
	 * @param integer $id of user to display.
	 *
	 * @return void
	 */
	public function viewAction($id = null)
    {
		$user = $this->users->find($id);
		
		$params = [$id, 0];
		$questions = $this->users->query()
					->join("thread", "test_user.id = test_thread.user_id")
					->where('test_user.id = ?')
					->andWhere('test_thread.thread_type = ?')
					->execute($params);
		
		$params = [$id, 1];			
		$answers = $this->users->query("question.id, question.headline AS headline, answer.content AS content, answer.timestamp AS timestamp")
					->join("thread AS answer", "test_user.id = answer.user_id ")
					->join("thread AS question", "answer.parent_id = question.id")
					->where('test_user.id = ?')
					->andWhere('answer.thread_type = ?')
					->execute($params);

        $this->theme->setTitle("Användar information");
        $this->views->add('users/view', [
			'title' => 'Användar information',
            'user' => $user,
            'questions' => $questions,
            'answers' => $answers,
        ]);
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
	 
	/**
    * Update user with id.
    *
    * @param int $id of user to display and update
    *
    * @return void
    */

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
		 
	/**
    * Create new user.
    *
    * @return void
    */
	public function createAction(){
		session_name('create_user');
		$this->di->session();

        $form = new \Anax\HTMLForm\CFormRegister();
        $form->setDI($this->di);
        $form->check();

        $this->di->theme->setTitle("Skapa ny användare");
        $this->di->views->add('users/user-form', [
            'title' => "Registrera dig här!",
            'content' => $form->getHTML()
        ]);
	}
		 
	/**
    * Login user.
    *
    * @return void
    */
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
	
	/**
    * Logout user.
    *
    * @return void
    */
	public function logoutAction(){
		$this->session->set('userID', null);
		$url = $this->url->create('');
		$this->response->redirect($url);
	}
	
	/**
    * Check if user is logged in.
    *
    * @return void
    */
	public function loginCheckAction(){
		if($this->di->session->get("userID", []) == null){

			$message = $this->fileContent->get('requireLoginQuestion.md');
			$message = $this->textFilter->doFilter($message, 'shortcode, markdown');
			
			$this->di->theme->setTitle("Ställ en fråga");
			$this->di->views->add('threads/question-form', [
				'title' => "Inte inloggad",
				'message' => $message,
				'form' => null
			]);
		} else {
			$this->dispatcher->forward([
				'controller' => 'threads',
				'action'     => 'createQuestion',
			]);
		}
	}
	
	/**
    * User image.
    *
    * @return url
    */
	public function get_gravatar( $email, $s = 40, $d = 'mm', $r = 'g', $img = true, $atts = array() ) 
	{
		$url = 'http://www.gravatar.com/avatar/';
		$url .= md5( strtolower( trim( $email ) ) );
		$url .= "?s=$s&d=$d&r=$r";
		if ( $img ) {
			$url = '<img src="' . $url . '"';
			foreach ( $atts as $key => $val )
				$url .= ' ' . $key . '="' . $val . '"';
			$url .= ' />';
		}
		return $url;
	}
}