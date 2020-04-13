<?php
namespace Controllers\Test;
use Router\AbstractController;
use Router\Response\Response;


class Test4 extends AbstractController {
    /**
     * @Route("/testawkdawdjlawkdjlawkdjawldjawlkdjalkwjdlkawjjlawkjd")
     */
    public function indeex(){
        return new Response(__DIR__ . DIRECTORY_SEPARATOR . "index.tpl");
    }



    /**
     * @Route("/test332awdlawjdkawdl;awkldkawdk;awdk;lakwdlawjdkawjdkl")
     */
    public function indewadawdex(){
        return new Response("<h1>Test</h1>");
    }
}