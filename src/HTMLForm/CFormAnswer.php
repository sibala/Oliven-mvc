<?php

namespace Anax\HTMLForm;

/**
 * Anax base class for wrapping sessions.
 *
 */
class CFormAnswer extends \Mos\HTMLForm\CForm
{
    use \Anax\DI\TInjectionaware,
        \Anax\MVC\TRedirectHelpers;



    /**
     * Constructor
     *
     */
    public function __construct($parent_id = null)
    { 
		 parent::__construct([], [
            'content' => [
                'type'        => 'textarea',
				'label'       => 'Svara',
                'required'    => true,
                'validation'  => ['not_empty'],
            ],
			'parent_id' => [
                'type'        => 'hidden',
				'value'       => $parent_id, 
            ],
            'submit' => [
                'type'      => 'submit',
				'value'       => "Svara", 
                'callback'  => [$this, 'callbackSubmit'],
            ],
        ]);
    }



    /**
     * Customise the check() method.
     *
     * @param callable $callIfSuccess handler to call if function returns true.
     * @param callable $callIfFail    handler to call if function returns true.
     */
    public function check($callIfSuccess = null, $callIfFail = null)
    {
        return parent::check([$this, 'callbackSuccess'], [$this, 'callbackFail']);
    }



    /**
     * Callback for submit-button.
     *
     */
    public function callbackSubmit()
    {
		$filter = new \Anax\Content\CTextFilter();
		$filter->setDI($this->di);
		$content = $filter->doFilter($this->Value('content'), 'markdown');
		
		$question = new \Anax\Threads\Thread();
		$question->setDI($this->di);
		/*
		if($this->Value('id')!==''){
			$user->save([
				'id' => $this->Value('id'),
				'acronym' => $this->Value('acronym'),
				'email' => $this->Value('email'),
				'name' =>  $this->Value('name'),
				'password' => password_hash( $this->Value('password'), PASSWORD_BCRYPT),
				'updated' => $now
			]);
			$url = $user->url->create('users/update/' . $user->id);
			$user->response->redirect($url);
		} else {*/
			$question->save([
				'content' => $content,
				'parent_id' => $this->Value('parent_id'),
				'thread_type' => 1,
				'user_id' => $this->di->session->get("userID", []),
			]);
			$url = $question->url->create('threads/view/' . $this->Value('parent_id'));
			$question->response->redirect($url);
		//}
		

        // $this->AddOutput("<p><i>DoSubmit(): Form was submitted. Do stuff (save to database) and return true (success) or false (failed processing form)</i></p>");
        // $this->AddOutput("<p><b>Username: " . $this->Value('username') . "</b></p>");
        // $this->AddOutput("<p><b>Email: " . $this->Value('email') . "</b></p>");
        // $this->AddOutput("<p><b>Name: " . $this->Value('name') . "</b></p>");
        // $this->saveInSession = true;
        // return true;
    }



    /**
     * Callback for submit-button.
     *
     */
    public function callbackSubmitFail()
    {
        $this->AddOutput("<p><i>DoSubmitFail(): Form was submitted but I failed to process/save/validate it</i></p>");
        return false;
    }



    /**
     * Callback What to do if the form was submitted?
     *
     */
    public function callbackSuccess()
    {
        $this->AddOUtput("<p><i>Form was submitted and the callback method returned true.</i></p>");
        $this->redirectTo();
    }



    /**
     * Callback What to do when form could not be processed?
     *
     */
    public function callbackFail()
    {
        $this->AddOutput("<p><i>Form was submitted and the Check() method returned false.</i></p>");
        $this->redirectTo();
    }
}
