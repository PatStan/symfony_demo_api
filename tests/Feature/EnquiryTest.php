<?php

namespace App\Tests\Feature;

use App\Tests\WebTestCase;
use Faker\Factory as FakerFactory;

class EnquiryTest extends WebTestCase
{
    public function test_create_enquiry_successfully()
    {
        $client = $this->client;

        $faker = FakerFactory::create();

        $data = [
            'message' => $faker->sentence(),
        ];

        $subscriberId = '01K021HTJQ075EZA6MKMFT75DJ';

        $client->request('POST', '/api/subscribers/' . $subscriberId . '/enquiries', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($data));

        $response = $client->getResponse();
        $this->assertEquals(201, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('message', $responseData);
        $this->assertArrayHasKey('enquiry', $responseData);
        $this->assertArrayHasKey('id', $responseData['enquiry']);
        $this->assertArrayHasKey('message', $responseData['enquiry']);
        $this->assertEquals('Enquiry created successfully', $responseData['message']);
    }
}
