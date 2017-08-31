<?php

  declare(strict_types=1);

  namespace Test\Xparse\Parser\Helper;

  use GuzzleHttp\Psr7\Response;
  use PHPUnit\Framework\TestCase;
  use Xparse\ElementFinder\ElementFinder;
  use Xparse\Parser\Helper\HtmlEncodingConverter;

  class HtmlEncodingConverterTest extends TestCase {

    /**
     * @return array
     */
    public function getDifferentCharsetStylesDataProvider() : array {
      return [
        [
          '<body></body>',
          '',
          ['content-type' => 'df'],
        ],
        [
          iconv('UTF-8', 'WINDOWS-1251', '<meta charset=\' windows-1251 \'><body>Текст текст text</body>'),
          'Текст текст text',
          ['content-type' => 'df'],
        ],
        [
          iconv('UTF-8', 'WINDOWS-1251', '<meta http-equiv="Content-Type" content="text/html; charset=windows-1251" /><body>Текст текст text</body>'
          ),
          'Текст текст text',
          ['content-type' => 'text/html; charset=windows-1251'],
        ],
        [
          iconv('UTF-8', 'WINDOWS-1251', '<meta http-equiv="Content-Type" content=\'text/html; charset=windows-1251\' /><body>Текст текст text</body>'),
          'Текст текст text',
        ],
        [
          iconv('UTF-8', 'WINDOWS-1251', '<meta charset=\' windows-1251    \'><body>Текст текст text</body>'),
          'Текст текст text',
        ],
        [
          iconv('UTF-8', 'WINDOWS-1251', '<meta http-equiv="Content-Type" content="text/html; charset=test-as225" /><body>Текст текст text</body>'),
          '  text',
        ],
        [
          iconv('UTF-8', 'WINDOWS-1251', '<meta http-equiv="Content-Type" content="text/html; charset=windows-1251" /><body>Текст текст text</body>'),
          'Текст текст text',
        ],
        [
          '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><body>Текст текст text</body>',
          'Текст текст text',
        ],
        [
          '<meta http-equiv="Content-Type" content="text/html; charset=utf8" /><body>Текст текст text</body>',
          'Текст текст text',
        ],
        [
          iconv('UTF-8', 'WINDOWS-1251', '<meta charset="windows-1251"><body>Текст текст text</body>'),
          'Текст текст text',
        ],
        [

          '<body></body>',
          '',
        ],
      ];
    }


    /**
     * @dataProvider getDifferentCharsetStylesDataProvider
     * @param string $html
     * @param string $bodyText
     * @param array $headers
     */
    public function testDifferentCharsetStyles(string $html, string $bodyText, array $headers = []) {
      $contentType = (new Response(200, $headers, $html))->getHeaderLine('content-type');
      $html = (new HtmlEncodingConverter())->convertToUtf($html, $contentType);

      $page = new ElementFinder($html);
      $pageBodyText = $page->content('//body')->getFirst();

      self::assertInstanceOf(ElementFinder::class, $page);
      self::assertEquals($bodyText, $pageBodyText);
    }

  }
