<?php

namespace Anax\HTMLForm;

/**
 * Anax base class for wrapping sessions.
 *
 */
class CFormRegister extends \Mos\HTMLForm\CForm
{
    use \Anax\DI\TInjectionaware,
        \Anax\MVC\TRedirectHelpers;



    /**
     * Constructor
     *
     */
    public function __construct()
    { 
		 parent::__construct([], [
            'acronym' => [
                'type'        => 'text',
                'label'       => 'Användarnamn:',
                'required'    => true,
                'validation'  => ['not_empty'],
            ],
            'email' => [
                'type'        => 'text',
				'label'       => 'Epost:',
                'required'    => true,
                'validation'  => ['not_empty', 'email_adress'],
				
            ],
            'name' => [
                'type'        => 'text',
                'label'       => 'Namn:',
                'required'    => true,
                'validation'  => ['not_empty'],
            ],
            'password' => [
                'type'        => 'password',
                'label'       => 'Lösenord: ',
                'required'    => true,
                'validation'  => ['not_empty'],
            ],
            'submit' => [
                'type'      => 'submit',
                'value'      => 'Registrera',
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
     * Creates new user in DB
     */
    public function callbackSubmit()
    {
		$now = date('Y-m-d H:i:s');
		$user = new \Anax\Users\User();
		$user->setDI($this->di);
		
		$user->save([
			'acronym' => $this->Value('acronym'),
			'email' => $this->Value('email'),
			'name' => $this->Value('name'),
			'password' => password_hash( $this->Value('password'), PASSWORD_BCRYPT),
			'created' => $now,
			'active' => $now,
		]);
		$url = $user->url->create('users/login');
		$user->response->redirect($url);
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
        $this->AddOutput("<p><i>Registreringen misslyckades.</i></p>");
        $this->redirectTo();
    }
}
