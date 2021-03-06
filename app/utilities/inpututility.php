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
        return new static([
                'get' => $_GET, 
                'post' => $_POST, 
                'files' => $_FILES
            ]);
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

    public function file($key)
    {
        return $this->fetch('files', $key);
    }

    public function files()
    {
        return $this->inputs['files'];
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

        if(isset($this->inputs[$type][$key])) {

            $result =  $this->inputs[$type][$key];

            $result = $this->filterInput($result);
        }

        return $result;

    }

}