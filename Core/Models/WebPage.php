<?php

namespace Core\Models;

class WebPage
{
     public $title;
     public $description;
     public $author;
     public $charset;
     public $additionalHeadContent;
     public $content;
     public $language;

     public function __construct()
     {
          $this->title = "Default Page";
          $this->description = "Default Description";
          $this->charset = "UTF-8";
          $this->language = "en";
          $this->additionalHeadContent = null;
          $this->author = null;
          $this->content = "<h1>Default Page</h1>";
     }

     public function setTitle(string $value) : WebPage
     {
          $this->title = $value;
          return $this;
     }
     
     public function setDescription(string $value) : WebPage
     {
          $this->description = $value;
          return $this;
     }
     
     public function setCharset(string $value) : WebPage
     {
          $this->charset = $value;
          return $this;
     }
     
     public function setLanguage(string $value) : WebPage
     {
          $this->language = $value;
          return $this;
     }
     
     public function setAuthor(string $value) : WebPage
     {
          $this->author = $value;
          return $this;
     }
     
     public function setAdditionalHeadContent(string $value) : WebPage
     {
          $this->additionalHeadContent = $value;
          return $this;
     }
}