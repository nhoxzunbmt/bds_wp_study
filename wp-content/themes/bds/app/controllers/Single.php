<?php

namespace App;

use Sober\Controller\Controller;

class Single extends Controller
{
	public function test(){
		$post = get_post();
		return 'This is test '.__FILE__.json_encode($post);
	}
}
