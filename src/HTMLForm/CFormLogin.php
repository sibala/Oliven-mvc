<?php

namespace Anax\HTMLForm;

/**
 * Anax base class for wrapping sessions.
 *
 */
class CFormLogin extends \Mos\HTMLForm\CForm 
{
    use \Anax\DI\TInjectionaware,
        \Anax\MVC\TRedirectHelpers;


    /**
     * Constructor
     *
     */
    public function __construct($userInfo = null)
    { 
		 parent::__construct([], [
            'acronym' => [
                'type'        => 'text',
                'label'       => 'Användarnamn:',
                'required'    => true,
                'validation'  => ['not_empty'],
				'value'       => $userInfo["acronym"], 
            ],
            'password' => [
                'type'        => 'password',
                'label'       => 'Lösenord: ',
                'required'    => true,
                'validation'  => ['not_empty'],
            ],
            'submit' => [
                'type'      => 'submit',
				'value'		=> 'Logga in',
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
     * Saves users id in session
     */
    public function callbackSubmit()
    {
		

		$user = new \Anax\Users\User();
		$user->setDI($this->di);
		

		$result = $user->query()
						->where("acronym = ?")
						->execute([$this->Value('acronym')]);
						
		if($result) {
			if(password_verify( $this->Value('password'),$result[0]->getProperties()['password'])) {

				$userID =  $result[0]->getProperties()['id'];
				session_name('userID');
				$this->di->session->set("userID", $userID);
				$url = $user->url->create('users/profile/' . $userID);
				
				$user->response->redirect($url);
				$this->callbackSuccess();
			} else {
				$this->callbackFail();
			}
		} else {
			$this->callbackFail();
		}
		
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

        $this->AddOutput("<p><i>Inloggning misslyckades.</i></p>");
        $this->redirectTo();
    }
}
