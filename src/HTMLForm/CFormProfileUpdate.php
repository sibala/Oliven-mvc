<?php

namespace Anax\HTMLForm;

/**
 * Anax base class for wrapping sessions.
 *
 */
class CFormProfileUpdate extends \Mos\HTMLForm\CForm
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
                'label'       => 'Användarnamn: ',
                'required'    => true,
                'validation'  => ['not_empty'],
				'value'       => $userInfo["acronym"], 
            ],
            'email' => [
                'type'        => 'text',
                'label'       => 'Epost: ',
                'required'    => true,
                'validation'  => ['not_empty', 'email_adress'],
				'value'       => $userInfo["email"], 
				
            ],
            'name' => [
                'type'        => 'text',
                'label'       => 'Namn: ',
                'required'    => true,
                'validation'  => ['not_empty'],
				'value'       => $userInfo["name"], 
            ],
            'password' => [
                'type'        => 'password',
                'label'       => 'Lösenord: ',
            ],
			'id' => [
                'type'        => 'hidden',
				'value'       => $userInfo["id"], 
            ],
            // 'phone' => [
                // 'type'        => 'text',
                // 'required'    => true,
                // 'validation'  => ['not_empty', 'numeric'],
            // ],
            'submit' => [
                'type'      => 'submit',
                'value'       => 'Uppdatera: ',
                'callback'  => [$this, 'callbackSubmit'],
            ],
            'submit-fail' => [
                'type'      => 'reset',
                'value'       => 'Reset: ',
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
		
		
		//$now = date(DATE_RFC2822);
		$now = date("Y-m-d H:i:s");
		$user = new \Anax\Users\User();
		$user->setDI($this->di);
		
		if($this->Value('password') !== ""){ 
			$user->save([
				'id' => $this->Value('id'),
				'acronym' => $this->Value('acronym'),
				'email' => $this->Value('email'),
				'name' =>  $this->Value('name'),
				'updated' => $now,
				'password' => password_hash( $this->Value('password'), PASSWORD_BCRYPT)
			]);
		} else {
			$user->save([
				'id' => $this->Value('id'),
				'acronym' => $this->Value('acronym'),
				'email' => $this->Value('email'),
				'name' =>  $this->Value('name'),
				'updated' => $now
			]);
		}
		
		$url = $user->url->create('users/profile/' . $user->id);
		$user->response->redirect($url);
		
		

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
