<?php
class ArticleModel extends CommonModel{
    protected $pk   = 'article_id';
    protected $tableName =  'article';
    
	
	
	public function rands(){
		$news = $this->order(array('article_id' => 'desc'))->limit(0, 45)->select();
		shuffle($news);
		if (empty($news)) {
			return array();
		}
		$num = (3 < count($news) ? 3 : count($news));
		$keys = array_rand($news, $num);
		$return = array();
		foreach ($news as $k => $val ) {
			if (in_array($k, $keys)) {
				$return[] = $val;
			}
		}
		return $return;
	}
}