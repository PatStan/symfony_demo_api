<?php

namespace App\Tests\Feature;

use App\Entity\Subscriber;
use App\Tests\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Faker\Factory as FakerFactory;

class SubscriberTest extends WebTestCase
{
    public function test_create_subscriber_successfully()
    {
        $client = $this->client;

        $faker = FakerFactory::create();

        $data = [
            'emailAddress' => $faker->unique()->safeEmail(),
            'firstName' => 'Patrick',
            'lastName' => 'Stanton',
            'dateOfBirth' => '1990-01-01',
            'marketingConsent' => true,
            'lists' => [
                '01JZJ0NYE3NYKGC326KS6VGKEV',
                '01JZJ0NYE3NYKGC326KS6VGKEX',
            ],
        ];

        $client->request('POST', '/api/subscribers', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($data));

        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('message', $responseData);
        $this->assertArrayHasKey('ulid', $responseData['subscriber']);
        $this->assertArrayHasKey('emailAddress', $responseData['subscriber']);
        $this->assertArrayHasKey('firstName', $responseData['subscriber']);
        $this->assertArrayHasKey('lastName', $responseData['subscriber']);
        $this->assertArrayHasKey('dateOfBirth', $responseData['subscriber']);
        $this->assertArrayHasKey('marketingConsent', $responseData['subscriber']);

        $this->assertEquals('Subscriber created successfully', $responseData['message']);

        $this->assertEquals($data['emailAddress'], $responseData['subscriber']['emailAddress']);
        $this->assertEquals($data['firstName'], $responseData['subscriber']['firstName']);
        $this->assertEquals($data['lastName'], $responseData['subscriber']['lastName']);
        $this->assertEquals('1990-01-01T00:00:00+00:00', $responseData['subscriber']['dateOfBirth']);
        $this->assertEquals($data['marketingConsent'], $responseData['subscriber']['marketingConsent']);
        $this->assertNotEmpty($responseData['subscriber']['ulid']);

        // Check if the subscriber was saved in the database
        $entityManager = static::getContainer()->get('doctrine')->getManager();
        $subscriber = $entityManager->getRepository(Subscriber::class)->findOneBy(['emailAddress' => $data['emailAddress']]);
        $this->assertNotNull($subscriber, 'Subscriber should be saved in the database');
        $this->assertEquals($data['emailAddress'], $subscriber->getEmailAddress());
        $this->assertEquals($data['firstName'], $subscriber->getFirstName());
        $this->assertEquals($data['lastName'], $subscriber->getLastName());
        $this->assertEquals(new \DateTimeImmutable($data['dateOfBirth']), $subscriber->getDateOfBirth());
        $this->assertEquals($data['marketingConsent'], $subscriber->isMarketingConsent());
        $this->assertNotNull($subscriber->getUlid());

        $this->assertInstanceOf(\Symfony\Component\Uid\Ulid::class, $subscriber->getUlid());
    }

    public function test_create_subscriber_validation()
    {
        $client = $this->client;

        // Test with missing required fields
        $data = [
            'emailAddress' => '',
            'firstName' => 'Patrick',
            'lastName' => 'Stanton',
            'dateOfBirth' => ' ',
            'marketingConsent' => true,
        ];

        $client->request('POST', '/api/subscribers', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($data));

        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('errors', $responseData);
        $this->assertNotEmpty($responseData['errors']);

        $this->assertEquals('This value should not be blank.', $responseData['errors']['children']['emailAddress']['errors'][0]['message']);
        $this->assertEquals('This value should not be blank.', $responseData['errors']['children']['dateOfBirth']['errors'][0]['message']);

        // Test with invalid email format
        $data['emailAddress'] = 'invalid-email-format';
        $client->request('POST', '/api/subscribers', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($data));

        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('errors', $responseData);
        $this->assertNotEmpty($responseData['errors']);

        $this->assertEquals('This value is not a valid email address.', $responseData['errors']['children']['emailAddress']['errors'][0]['message']);

        // Test with date of birth in the future

        $data['dateOfBirth'] = (new \DateTimeImmutable())->modify('+1 day')->format('Y-m-d');

        $client->request('POST', '/api/subscribers', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($data));

        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('errors', $responseData);
        $this->assertNotEmpty($responseData['errors']);
        $this->assertEquals('Date of birth must be in the past.', $responseData['errors']['children']['dateOfBirth']['errors'][0]['message']);

        // Test with underage date of birth

        $data['dateOfBirth'] = (new \DateTimeImmutable())->modify('-17 years')->format('Y-m-d');

        $client->request('POST', '/api/subscribers', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($data));
        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('errors', $responseData);
        $this->assertNotEmpty($responseData['errors']);
        $this->assertEquals('You must be at least 18 years old.', $responseData['errors']['children']['dateOfBirth']['errors'][0]['message']);
    }

    public function test_create_subscriber_with_missing_marketing_consent()
    {
        $client = $this->client;

        $faker = FakerFactory::create();

        $data = [
            'emailAddress' => $faker->unique()->safeEmail(),
            'dateOfBirth' => '1990-01-01',
        ];

        $client->request('POST', '/api/subscribers', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($data));

        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());


        $responseData = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('message', $responseData);
        $this->assertArrayHasKey('subscriber', $responseData);
        $this->assertArrayHasKey('marketingConsent', $responseData['subscriber']);

        $this->assertFalse($responseData['subscriber']['marketingConsent']);
    }
}



