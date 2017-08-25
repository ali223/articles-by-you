<?php
namespace App\Utilities;

use App\Validators\FilterInputTrait;

class InputUtility 
{
    use FilterInputTrait;

    protected $inputs = [
        'get' => [],
        'post' => []
    ];


    public static function createFromGlobals()
    {
        return new static(['get' => $_GET, 'post' => $_POST]);
    }


    public function __construct($inputs)
    {
        foreach($inputs as $key => $input) {
            if(isset($input)) {
                $this->inputs[$key] = $input;
            }
        }
    }

    public function get($key)
    {
        return $this->fetch('get', $key);
    }


    public function post($key)
    {
        return $this->fetch('post', $key);
    }

    public function posts()
    {
        if(isset($this->inputs['post'])) {
            foreach($this->inputs['post'] as &$postData) {
                $postData = $this->filterInput($postData);
            }

            return $this->inputs['post'];
        }
    }


    protected function fetch($type, $key)
    {
        $result = null;

        if(isset($this->input[$type][$key])) {

            $result =  $this->input[$type][$key];

            $result = $this->filterInput($result);
        }

        return $result;

    }

}