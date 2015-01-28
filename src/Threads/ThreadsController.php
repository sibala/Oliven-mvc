<?php

namespace Anax\Threads;
 
/**
 * A controller for users and admin related events.
 *
 */
class ThreadsController implements \Anax\DI\IInjectionAware
{
    use \Anax\DI\TInjectable;
	
	/**
    * Initialize the controller.
    *
    * @return void
    */
    public function initialize()
    {
        $this->threads = new \Anax\Threads\Thread();
        $this->threads->setDI($this->di);
    }
	
	/**
    * List all users.
    *
    * @return void
    */
    public function listQuestionsAction()
    {
		/*
		$questions = $this->threads->query("test_thread.id AS id, test_thread.headline AS headline, test_thread.content AS content, test_thread.timestamp AS timestamp, u.acronym AS acronym")
			->join("user AS u", "u.id = test_thread.user_id ")
			//->join("taggedthread AS tt", "test_thread.id = tt.thread_id")
			//->join("tag AS t", "tt.tag_id = t.id")
			->where("test_thread.parent_id is null")
			//->groupby("tt.thread_id")
			->orderby("test_thread.timestamp DESC")
			->execute();
		
		$answerCount = $this->threads->query("t1.parent_id AS questionID, COUNT(t1.id) AS answerCount", "t1")
			->join("thread AS t2", "t1.id = t2.parent_id ")
			->where("t1.thread_type=1")
			->groupby("t1.parent_id")
			->execute();*/
			
		$tags = $this->threads->query("th.id AS thread_id, t.tag, t.id", "th")
			->join("taggedthread AS tt", "th.id = tt.thread_id")
			->join("tag AS t", "tt.tag_id = t.id")
			->where("th.parent_id is null")
			->execute();
			
		$sql = "SELECT  
					t1.id AS id, 
					t1.headline AS headline, 
					t1.content AS content, 
					t1.timestamp AS timestamp, 
					u.id AS userId,
					u.acronym AS userAcronym,
					u.email AS userEmail,
					query2.answerCount
				FROM `test_thread` AS t1 
				LEFT JOIN test_user AS u ON t1.user_id = u.id
				LEFT JOIN 
					(SELECT t2.parent_id AS id, COUNT(t2.id) AS answerCount FROM test_thread AS t2
					LEFT JOIN test_thread AS t3 ON t2.id = t3.parent_id
					WHERE t2.thread_type = 1
					GROUP BY t2.parent_id) AS query2 ON t1.id = query2.id
				WHERE t1.thread_type = 0
				ORDER BY t1.timestamp DESC";

		$this->db->execute($sql);
		$questions = $this->db->fetchAll();
		
		
        $this->theme->setTitle("Frågor");
        $this->views->add('threads/questions-list', [
            'questions' => $questions,
            'tags' => $tags,
            'title' => "Frågor",
        ]);
    }
	
	public function latestQuestionsAction()
    {
        $questions = $this->threads->query("t.id, t.headline, t.timestamp, u.email", "t")
			->join("user AS u", "u.id = t.user_id")
			->where("parent_id is null")
			->orderby("timestamp DESC")
			->limit(3)
			->execute();
 
        $this->theme->setTitle("Senaste frågorna");
        $this->views->add('threads/popular-questions-list', [
            'questions' => $questions,
            'title' => "Senaste frågorna",
        ]);
    }
	
	public function taggedAction($id = null)
    {
		/*$params = [$id, 0];
		$taggedQuestions = $this->threads->query("test_thread.id AS id, test_thread.headline AS headline, test_thread.content AS content, test_thread.timestamp AS timestamp")
					->join("taggedthread", "test_thread.id = test_taggedthread.thread_id")
					->join("tag", "test_taggedthread.tag_id = test_tag.id")
					->where('test_tag.id = ?')
					->andWhere('test_thread.thread_type = ?')
					->execute($params);*/
					
		$sql = "SELECT  
					t1.id AS id, 
					t1.headline AS headline, 
					t1.content AS content, 
					t1.timestamp AS timestamp, 
					u.id AS userId,
					u.acronym AS userAcronym,
					u.email AS userEmail,
					query2.answerCount
				FROM `test_thread` AS t1 
				LEFT JOIN test_user AS u ON t1.user_id = u.id
				LEFT JOIN test_taggedthread AS tagged ON t1.id = tagged.thread_id 
				LEFT JOIN test_tag AS tag ON tagged.tag_id = tag.id
				LEFT JOIN 
					(SELECT t2.parent_id AS id, COUNT(t2.id) AS answerCount FROM test_thread AS t2
					LEFT JOIN test_thread AS t3 ON t2.id = t3.parent_id
					WHERE t2.thread_type = 1
					GROUP BY t2.parent_id) AS query2 ON t1.id = query2.id
				WHERE t1.thread_type = 0 AND tag.id = ?
				ORDER BY t1.timestamp DESC";
		$params = [$id];
		$this->db->execute($sql, $params);
		$taggedQuestions = $this->db->fetchAll();

        $this->theme->setTitle("Tagg frågor");
        $this->views->add('threads/tagged_questions-list', [
			'title' => 'Användar information',
            'questions' => $taggedQuestions,
        ]);

    }
	
	public function viewAction($id = null)
    {
		$params = [$id];
		$question = $this->threads->query("t1.id AS id, 
					t1.headline AS headline, 
					t1.content AS content, 
					t1.timestamp AS timestamp, 
					u.id AS userId,
					u.acronym AS userAcronym,
					u.email AS userEmail", "t1")
			->join("user AS u", "u.id = t1.user_id ")
			//->join("taggedthread AS tt", "test_thread.id = tt.thread_id")
			//->join("tag AS t", "tt.tag_id = t.id")
			->where("t1.id = ?")
			->execute($params);
			
		//$question = $this->threads->find($id);
		$params = [$id];
		$tags = $this->threads->query("th.id AS thread_id, t.tag, t.id", "th")
			->join("taggedthread AS tt", "th.id = tt.thread_id")
			->join("tag AS t", "tt.tag_id = t.id")
			->where("th.parent_id is null")
			->andWhere("th.id = ?")
			->execute($params);
		
		
		$thread_id =$question[0]->id;
		
		$params = [$thread_id];
		$answers = $this->threads->query("u.id AS userId, u.email AS email, u.acronym AS acronym, t.id AS thread_id, t.timestamp, t.content", "t")
			->join("user AS u","u.id = t.user_id")
			->where("parent_id = ?")
			->execute($params);
			
		session_name('create_answer');
		$this->di->session();

        $form = new \Anax\HTMLForm\CFormAnswer($thread_id);
        $form->setDI($this->di);
        $form->check();
		
		$comment = new \Phpmvc\Comment\Comment();
		$comment->setDI($this->di);
		$params = [$thread_id];
		$questionComments = $comment->query("c.content, c.timestamp, u.acronym","c")
			->join("thread AS t", "t.id = c.thread_id")
			->join("user AS u", "u.id = t.user_id")
			->where("thread_id = ?")
			->execute($params);
			
		$answerComments = $comment->query("c.thread_id, c.content, c.timestamp, u.acronym","c")
			->join("thread AS t", "t.id = c.thread_id")
			->join("user AS u", "u.id = t.user_id")
			->where("t.parent_id = ?")
			->execute($params);
		
		$message = $this->fileContent->get('requireLoginForum.md');
		$message = $this->textFilter->doFilter($message, 'shortcode, markdown');
		
        $this->theme->setTitle("Frågor");
        $this->views->add('threads/view', [
			'title' => 'Fråga',
            'question' => $question,
            'answers' => $answers,
            'tags' => $tags,
            'questionComments' => $questionComments,
            'answerComments' => $answerComments,
			'message' => $message,
			'form' => $form->getHTML(),
        ]);
    }
	
	public function createQuestionAction(){
		session_name('create_question');
		$this->di->session();

        $form = new \Anax\HTMLForm\CFormQuestion();
        $form->setDI($this->di);
        $form->check();

        $this->di->theme->setTitle("Ställ en fråga");
        $this->di->views->add('threads/question-form', [
            'title' => "Ställ en fråga",
            'form' => $form->getHTML()
        ]);
	}
}