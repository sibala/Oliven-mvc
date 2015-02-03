<?php

namespace Anax\HTMLForm;

/**
 * Anax base class for wrapping sessions.
 *
 */
class CFormQuestion extends \Mos\HTMLForm\CForm
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
            'headline' => [
                'type'        => 'text',
                'label'       => 'Rubrik',
                'required'    => true,
                'validation'  => ['not_empty'],
            ],
			'tags' => [
                'type'        => 'text',
                'label'       => 'Taggar Obs! Separera flera taggar med kommatecken ","',
                'required'    => true,
                'validation'  => ['not_empty'],
            ],
            'content' => [
                'type'        => 'textarea',
				'label'       => 'Fråga?',
                'required'    => true,
                'validation'  => ['not_empty'],
				
            ],
            'submit' => [
                'type'      => 'submit',
				'value'		=> 'Skicka',
                'callback'  => [$this, 'callbackSubmit'],
            ],
            'submit-fail' => [
                'type'      => 'reset',
                'value'      => 'Reset',
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
		$question->save([
			'headline' => $this->Value('headline'),
			'content' => $content,
			'thread_type' => 0,
			'user_id' => $this->di->session->get("userID", []),
			'timestamp' => date( 'Y-m-d H:i:s' , time() ),
		]);
			
		$result = $question->query("id")
					->orderby("id DESC")
					->limit("1")
					->execute();
					
		$thread_id = $result[0]->getProperties()['id'];
		
		
		
		$tags = explode(',', $this->Value('tags'));

		
		foreach($tags as $t){
			$tag = new \Anax\Tags\Tag();
			$tag->setDI($this->di);
			$params = [trim($t)];
			$result = $tag->query('id')
						->where("tag = ?")
						->execute($params);
						
						
				
			if(empty($result)){

				$tag->save([
					'tag' => trim($t),
				]);
				
				$result  = $tag->query("id")
						->orderby("id DESC")
						->limit("1")
						->execute();
				$tag_id = $result[0]->getProperties()['id'];
						
			} else {
				$tag_id = $result[0]->getProperties()['id'];	
			}
			
			$tagged = new \Anax\Tagged\TaggedThread();
			$tagged->setDI($this->di);
			$tagged->save([
				'thread_id' => $thread_id,
				'tag_id' => $tag_id,
			]);
		}
		
		
		$url = $question->url->create('threads');
		$question->response->redirect($url);
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
