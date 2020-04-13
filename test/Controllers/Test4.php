<?php
namespace Controllers;
use Router\AbstractController;
use Router\Response\Response;


class Test4 extends AbstractController {
    /**
     * @Route("/testawkdjlawkjd")
     */
    public function indeex(){
        return new Response(__DIR__ . DIRECTORY_SEPARATOR . "index.tpl");
    }



    /**
     * @Route("/test332awdlawjdklawjdkawjdkl")
     */
    public function indewadawdex(){
        return new Response("<h1>Test</h1>");
    }
}