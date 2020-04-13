<?php
namespace Router\Response;

class JsonResponse implements ResponseEnum {

    protected $object = [];
    protected $http_response_code = 200;

    public function __construct($object = [], $http_response_code = 200)
    {
        $this->object = $object;
        $this->http_response_code = $http_response_code;
    }

    public function display(): void
    {
        http_response_code($this->http_response_code);
        echo json_encode($this->object);
    }

}