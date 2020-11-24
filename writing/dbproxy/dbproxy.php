<?php
class DBProxy {
	function getPoemList($config, $category = null, $auth){
		$db = new medoo($config["db"]);
		$return = new Response();
		$data = null;
		if ($auth){
			if ($category == null){
			$data = $db->select("poem", "*", array(
					"ORDER" => array(
						'date ASC',
						'timestamp ASC',
						'id ASC'
					)
				));
			} else {
				$data = $db->select("poem", "*", array(
						"category" => $category,
						"ORDER" => array(
							'date ASC',
							'timestamp ASC',
							'id ASC'
						)
					));
			}
		} else {
			if ($category == null){
				$data = $db->select("poem", "*", array(
						"public" => '1',
						"ORDER" => array(
							'date ASC',
							'timestamp ASC',
							'id ASC'
						)
					));
			} else {
				$data = $db->select("poem", "*", array(
					"AND" => array(
						"category" => $category,
						"public" => '1',
					),
					"ORDER" => array(
						'date ASC',
						'timestamp ASC',
						'id ASC'
					)
				));
			}
		}
		$return->status = true;
		$return->data = $data;
		return $return;
	}
	
	function getPoemCount($config){
		$db = new medoo($config["db"]);
		$return = new Response();
		$data = $db->max("poem", "id");
		$return->status = true;
		$return->data = $data;
		return $return;
	}
	
	function getPrevNext($config, $id){
		$db = new medoo($config["db"]);
		$return = new Response();
		$data = null;
		if ($auth) {
			$data = $db->select("poem", array(
			'id',
			'date',
			'title'
			), array(
				"ORDER" => array(
					'date ASC',
					'timestamp ASC',
					'id ASC'
				)
			));
		} else {
			$data = $db->select("poem", array(
			'id',
			'date',
			'title'
			), array(
				"public" => '1',
				"ORDER" => array(
					'date ASC',
					'timestamp ASC',
					'id ASC'
				)
			));
		}
		$prev;
		$next;
		$found = false;
		foreach($data as $poem){
			if ($found) {
				$next = $poem;
				break;
			}
			if($poem['id'] == $id){
				$found = true;
			}
			if (!$found){
				$prev = $poem;
			}
		}
		$return->data = array(
			'prev' => $prev,
			'next' => $next
		);
		$return->status = true;
		return $return;
	}

	function getPoem($config, $poemid, $auth){
		$db = new medoo($config["db"]);
		$return = new Response();
		$data = $db->select("poem", "*", array(
				"AND" => array(
					"id" => $poemid,
					"category[!]" => 1
				)
			));
		if($data == null){
			$return->status = false;
			$return->message = "Poem not found.";
			return $return;
		}
		$poem = new Poem($data[0]);
		$lines = $db->select("line", "*", array(
				"poemid" => $poemid,
				"ORDER" => 'line'
			));
		foreach($lines as $line){
			array_push($poem->lines, new Line($line));
		}
		if ($auth) {
			$data = $db->select("poem", array(
			'id',
			'date',
			'title'
			), array(
				"category[!]" => 1,
				"ORDER" => 'date'
			));
		} else {
			$data = $db->select("poem", array(
			'id',
			'date',
			'title'
			), array(
				"AND" => array(
					"public" => '1',
					"category[!]" => 1
					),
 				"ORDER" => 'date'
			));
		}
		$index = 0;
		$prev;
		$next;
		$found = false;
		foreach($data as $p){
			if ($found) {
				$next = $p;
				break;
			}
			if($p['id'] == $poemid){
				$found = true;
			}
			if (!$found){
				$prev = $p;
			}
			$index++;
		}
		$return->status = true;
		$return->data = array(
			'poem' => $poem,
			'prev' => $prev,
			'next' => $next
		);
		return $return;
	}

	function getPost($config, $postid, $auth){
		$db = new medoo($config["db"]);
		$return = new Response();
		$category = 1;
		$data = $db->select("poem", "*", array(
			"AND" => array(
				"category" => $category,
				"id" => $postid
				)
		));
		if($data == null){
			$return->status = false;
			$return->message = "Post not found.";
			return $return;
		}
		$poem = new Poem($data[0]);
		$lines = $db->select("line", "*", array(
				"poemid" => $postid,
				"ORDER" => 'line'
			));
		foreach($lines as $line){
			array_push($poem->lines, new Line($line));
		}
		$data = $db->select("poem", array(
			'id',
			'date',
			'title'
		), array(
			"ORDER" => 'date'
		));
		
		if ($auth) {
			$data = $db->select("poem", array(
			'id',
			'date',
			'title'
			), array(
				"category" => 1,
				"ORDER" => 'date'
			));
		} else {
			$data = $db->select("poem", array(
			'id',
			'date',
			'title'
			), array(
				"AND" => array(
					"public" => '1',
					"category" => 1
					),
 				"ORDER" => 'date'
			));
		}
		$index = 0;
		$prev;
		$next;
		$found = false;
		foreach($data as $p){
			if ($found) {
				$next = $p;
				break;
			}
			if($p['id'] == $postid){
				$found = true;
			}
			if (!$found){
				$prev = $p;
			}
			$index++;
		}
		$return->status = true;
		$return->data = array(
			'poem' => $poem,
			'prev' => $prev,
			'next' => $next
		);

		return $return;
	}

	function postPoem($config, $poem){
		$db = new medoo($config["db"]);
		$return = new Response();
		$poemid = $db->insert("poem", array(
				"title" => $poem->title,
				"date" => $poem->date,
				"firstname" => $poem->firstname,
				"lastname" => $poem->lastname,
				"category" => $poem->category,
				"public" => $poem->public
			));
		foreach($poem->lines as $line){
			$res = $db->insert("line", array(
					"poemid" => $poemid,
					"text" => $line->text,
					"line" => $line->line
				));
		}
		$return->status = true;
		$return->data = $poemid;
		return $return;
	}
	function getCategories($config){
		$db = new medoo($config["db"]);
		$return = new Response();
		$data = $db->select("category", "*");
		$return->status = true;
		$return->data = $data;
		return $return;
	}
}


?>