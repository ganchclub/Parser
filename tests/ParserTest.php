<?php

  namespace Xparse\Parser\Test;

  use GuzzleHttp\Client;
  use GuzzleHttp\Ring\Client\MockHandler;

  /**
   *
   * @package Xparse\Parser\Test
   */
  class ParserTest extends \PHPUnit_Framework_TestCase {

    public function testInit() {

      $parser = new \Xparse\Parser\Parser();
      $this->assertEquals(new Client(), $parser->getClient());
    }

    public function testGet() {

      $client = $this->getDemoClient();
      $parser = new \Xparse\Parser\Parser($client);

      $this->assertEquals($client, $parser->getClient());

      $page = $parser->get('http://test.com');

      $this->assertInstanceOf(get_class(new \Xparse\Parser\Page("<html><a href='1'>1</a></html>")), $page);
      $this->assertEquals($page, $parser->getLastPage());
      $this->assertEquals($parser, $page->getParser());
    }

    public function testPost() {

      $client = $this->getDemoClient();
      $parser = new \Xparse\Parser\Parser($client);

      $this->assertEquals($client, $parser->getClient());

      $page = $parser->post('http://test.com/info', array('data' => '123'));

      $this->assertInstanceOf(get_class(new \Xparse\Parser\Page("<html></html>")), $page);
      $this->assertEquals($page, $parser->getLastPage());
      $this->assertEquals($parser, $page->getParser());

    }


    /**
     * @param $url
     * @return string
     */
    protected function getHtmlData($url) {
      $html = file_get_contents(__DIR__ . '/data' . $url);
      return $html;
    }

    /**
     * @return Client
     */
    protected function getDemoClient() {
      $mock = new MockHandler(array(
        'status' => 200,
        'headers' => array(),
        'body' => $this->getHtmlData('/test-get.html')
      ));

      $client = new Client(['handler' => $mock]);
      return $client;
    }

    public function testEffectedUrl() {
      $parser = new \Xparse\Parser\Parser($this->getDemoClient());

      $url = 'http://test.com/df';
      $page = $parser->get($url);
      $this->assertEquals($url, $page->getEffectedUrl());

    }

    public function testGetLastResponse() {
      $parser = new \Xparse\Parser\Parser($this->getDemoClient());
      $url = 'http://test.com/url/';
      $parser->get($url);
      $this->assertEquals($url, $parser->getLastResponse()->getEffectiveUrl());

    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetInvalidUrl() {
      $parser = new \Xparse\Parser\Parser($this->getDemoClient());
      $parser->get(null);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testPostWithInvalidParams() {
      $parser = new \Xparse\Parser\Parser($this->getDemoClient());
      $parser->post(new \stdClass(), null);
    }

  }
