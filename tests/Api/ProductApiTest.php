<?php

namespace Swader\Diffbot\Test\Api;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;

class ProductApiTest extends \PHPUnit_Framework_TestCase
{
    use setterUpper;

    protected $validMock;

    /**
     * @var \Swader\Diffbot\Api\Product
     */
    protected $apiWithMock;

    protected function setUp()
    {
        $diffbot = $this->preSetUp();

        $this->apiWithMock = $diffbot->createProductAPI('https://dogbrush-mock.com');
    }

    protected function getValidMock()
    {
        if (!$this->validMock) {
            $this->validMock = new MockHandler([
                new Response(200, [],
                    file_get_contents(__DIR__ . '/../Mocks/Products/dogbrush.json'))
            ]);
        }

        return $this->validMock;
    }

    public function testCall()
    {
        $products = $this->apiWithMock->call();

        foreach ($products as $product) {
            $this->assertInstanceOf('Swader\Diffbot\Entity\Product', $product);
        }
    }

    public function testBuildUrlNoCustomFields()
    {
        $url = $this
            ->apiWithMock
            ->buildUrl();
        $expectedUrl = 'https://api.diffbot.com/v3/product?token=demo&url=https%3A%2F%2Fdogbrush-mock.com&timeout=30000';
        $this->assertEquals($expectedUrl, $url);
    }

    public function testBuildUrlMultipleCustomFields()
    {
        $url = $this
            ->apiWithMock
            ->setColors(true)
            ->setSize(true)
            ->setAvailability(true)
            ->buildUrl();
        $expectedUrl = 'https://api.diffbot.com/v3/product?token=demo&url=https%3A%2F%2Fdogbrush-mock.com&timeout=30000&fields=colors,size,availability';
        $this->assertEquals($expectedUrl, $url);
    }

    public function testBuildUrlMultipleCustomFieldsAndOtherOptions()
    {
        $url = $this
            ->apiWithMock
            ->setColors(true)
            ->setSize(true)
            ->setAvailability(true)
            ->setDiscussion(false)
            ->buildUrl();
        $expectedUrl = 'https://api.diffbot.com/v3/product?token=demo&url=https%3A%2F%2Fdogbrush-mock.com&timeout=30000&fields=colors,size,availability&discussion=false';
        $this->assertEquals($expectedUrl, $url);
    }
}
