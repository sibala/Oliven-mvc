<?php

namespace Anax\HTMLForm;

/**
 * Anax base class for wrapping sessions.
 *
 */
class CFormComment extends \Mos\HTMLForm\CForm
{
    use \Anax\DI\TInjectionaware,
        \Anax\MVC\TRedirectHelpers;



    /**
     * Constructor
     *
     */
    public function __construct($thread_id = null)
    { 
		 parent::__construct([], [
            'content' => [
                'type'        => 'textarea',
				'label'       => 'Kommentera',
                'required'    => true,
                'validation'  => ['not_empty'],
            ],
			'thread_id' => [
                'type'        => 'hidden',
				'value'       => $thread_id, 
            ],
            'submit' => [
                'type'      => 'submit',
                'callback'  => [$this, 'callbackSubmit'],
            ],
            'submit-fail' => [
                'type'      => 'submit',
                'callback'  => [$this, 'callbackSubmitFail'],
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
		
		$now = date( 'Y-m-d H:i:s' , time());
		
		$filter = new \Anax\Content\CTextFilter();
		$filter->setDI($this->di);
		$content = $filter->doFilter($this->Value('content'), 'markdown');
		
		$comment = new \Phpmvc\Comment\Comment();
		$comment->setDI($this->di);
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
			$comment->save([
				'content' => $content,
				'thread_id' => $this->Value('thread_id'),
				'timestamp' => $now,
			]);
			$url = $comment->url->create('threads/view/' . $this->Value('thread_id'));
			$comment->response->redirect($url);
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
