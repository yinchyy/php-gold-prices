<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class GoldControllerTest extends WebTestCase
{

    public function testGoldJanuary2021Single()
    {
        $client = static::createClient();
        $client->jsonRequest('POST', '/api/gold', [
            'from' => '2021-01-04T00:00:00+00:00',
            'to' => '2021-01-04T00:00:00+00:00'
        ]);
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertResponseIsSuccessful();
        $this->assertIsArray($response);
        $this->assertArrayHasKey('from', $response);
        $this->assertEquals('2021-01-04T00:00:00+01:00', $response['from']);
        $this->assertArrayHasKey('to', $response);
        $this->assertEquals('2021-01-04T00:00:00+01:00', $response['to']);
        $this->assertArrayHasKey('avg', $response);
        $this->assertEquals(228.1, $response['avg']);
    }

    public function testGoldJanuary2021Range()
    {
        $client = static::createClient();
        $client->jsonRequest('POST', '/api/gold', [
            'from' => '2021-01-01T00:00:00+00:00',
            'to' => '2021-01-31T00:00:00+00:00'
        ]);
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertResponseIsSuccessful();
        $this->assertIsArray($response);
        $this->assertArrayHasKey('from', $response);
        $this->assertEquals('2021-01-04T00:00:00+01:00', $response['from']);
        $this->assertArrayHasKey('to', $response);
        $this->assertEquals('2021-01-29T00:00:00+01:00', $response['to']);
        $this->assertArrayHasKey('avg', $response);
        $this->assertEquals(223.52, $response['avg']);
    }

    public function testMissingTimezone()
    {
        $client = static::createClient();
        $client->jsonRequest('POST', '/api/gold', [
            'from' => '2001-01-04 00:00:00',
            'to' => '2001-01-04 00:00:00'
        ]);
        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
    }
    public function testNotTrackedDaySingle()
    {
        $client = static::createClient();
        $client->jsonRequest('POST', '/api/gold', [
            'from' => '2021-01-01T00:00:00+01:00',
            'to' => '2021-01-01T00:00:00+01:00'
        ]);
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('message', $response);
        $this->assertEquals("Invalid date range.", $response['message']);
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }
    public function testDateFromTheFuture()
    {
        $client = static::createClient();
        $client->jsonRequest('POST', '/api/gold', [
            'from' => '2181-01-01T00:00:00+01:00',
            'to' => '2181-01-26T00:00:00+01:00'
        ]);
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('message', $response);
        $this->assertEquals("Invalid date range.", $response['message']);
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }
    public function testFromIsLaterThanTo()
    {
        $client = static::createClient();
        $client->jsonRequest('POST', '/api/gold', [
            'from' => '2021-01-21T00:00:00+01:00',
            'to' => '2021-01-02T00:00:00+01:00'
        ]);
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('message', $response);
        $this->assertEquals("Invalid date range.", $response['message']);
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }
    public function testMissingFrom()
    {
        $client = static::createClient();
        $client->jsonRequest('POST', '/api/gold', [
            'from' => '2021-01-01T00:00:00+01:00',
        ]);
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('message', $response);
        $this->assertEquals("Invalid data. Missing 'from' or 'to' property.", $response['message']);
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }
    public function testMissingTo()
    {
        $client = static::createClient();
        $client->jsonRequest('POST', '/api/gold', [
            'to' => '2021-01-01T00:00:00+01:00',
        ]);
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('message', $response);
        $this->assertEquals("Invalid data. Missing 'from' or 'to' property.", $response['message']);
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }
    public function testEmptyJSON()
    {
        $client = static::createClient();
        $client->jsonRequest('POST', '/api/gold', []);
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('message', $response);
        $this->assertEquals("Invalid data. Missing 'from' or 'to' property.", $response['message']);
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }
    public function testStringInsteadOfJSON()
    {
        $client = static::createClient();
        $client->xmlHttpRequest('POST', '/api/gold', ["Nobody expects Spanish Inquisition and string instead of JSON... right?"]);
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('message', $response);
        $this->assertEquals("Invalid format, data should be in JSON.", $response['message']);
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }
    public function testGoldJuly2022Range()
    {
        $client = static::createClient();
        $client->jsonRequest('POST', '/api/gold', [
            'from' => '2022-07-01T00:00:00Z',
            'to' => '2022-07-14T00:00:00+00:00'
        ]);
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertResponseIsSuccessful();
        $this->assertIsArray($response);
        $this->assertArrayHasKey('from', $response);
        $this->assertEquals("2022-07-01T00:00:00+02:00", $response['from']);
        $this->assertArrayHasKey('to', $response);
        $this->assertEquals("2022-07-14T00:00:00+02:00", $response['to']);
        $this->assertArrayHasKey('avg', $response);
        $this->assertEquals(264.07, $response['avg']);
    }
    public function testDifferentTimezoneInput()
    {
        $client = static::createClient();
        $client->jsonRequest('POST', '/api/gold', [
            'from' => '2022-07-01T00:00:00+12:00',
            'to' => '2022-07-14T00:00:00+10:00'
        ]);
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertResponseIsSuccessful();
        $this->assertIsArray($response);
        $this->assertArrayHasKey('from', $response);
        $this->assertEquals("2022-06-30T00:00:00+02:00", $response['from']);
        $this->assertArrayHasKey('to', $response);
        $this->assertEquals("2022-07-13T00:00:00+02:00", $response['to']);
        $this->assertArrayHasKey('avg', $response);
        $this->assertEquals(263.35, $response['avg']);
    }
}