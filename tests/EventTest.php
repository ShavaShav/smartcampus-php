<?php

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use Carbon\Carbon;
use Tymon\JWTAuth\JWTAuth;

use App\User;
use App\Event;

class EventTest extends TestCase
{
    private $token = null;     // jwt for test user
    private $userId = null;    // id of test user
    private $eventId = null;   // id of test event
    private $eventTime = null; // timestamp for event

    // TODO: Replace with seeder once sqllite/mysql foreign key check issue resolved   
    public function setUp()
    {
        parent::setUp();

        // Create a single user and event in database
        $user = User::create([
            'username' => 'TestUser',
            'email' => 'test_user@fakemail.com',
            'password' => bcrypt('my_pass_2345')
        ]);

        $this->eventTime = Carbon::now()->addDays(1)->toDateTimeString();

        $event = Event::create([
            'title' => "Test Event",
            'time' => $this->eventTime, 
            'location' => "Erie Hall",
            'link' => "http://uwindsor.ca/",
            'body' => "A super fun test event.",
            'author_id' => $user->id
        ]);

        // Store the user token and event id so we can use in test
        $this->token = \JWTAuth::fromUser($user);
        $this->eventId = $event->id;
        $this->userId = $user->id;
    }

    /**
     * I should be able to see an event without authorization
     *
     * @return void
     */
    public function testGetEvent()
    {        
        $this->get('/api/events/'.$this->eventId)
             ->assertResponseOk();

        $this->shouldReturnJson();

        // Assert on structure of response
        $responseJson = json_decode($this->response->content());
        $this->assertObjectHasAttribute('event', $responseJson);
        $this->assertObjectHasAttribute('id', $responseJson->event);
        $this->assertObjectHasAttribute('title', $responseJson->event);
        $this->assertObjectHasAttribute('location', $responseJson->event);
        $this->assertObjectHasAttribute('link', $responseJson->event);
        $this->assertObjectHasAttribute('body', $responseJson->event);
        $this->assertObjectHasAttribute('created_at', $responseJson->event);
        $this->assertObjectHasAttribute('updated_at', $responseJson->event);
        $this->assertObjectHasAttribute('id', $responseJson->event->author);
        $this->assertObjectHasAttribute('username', $responseJson->event->author);
        $this->assertObjectHasAttribute('email', $responseJson->event->author);
        
        // Assert on actual event details
        $this->seeJsonContains([
            'id' => (string) $this->eventId,
            'title' => "Test Event",
            'time' => $this->eventTime, 
            'location' => "Erie Hall",
            'link' => "http://uwindsor.ca/",
            'body' => "A super fun test event.",
            'author' => [
                'id' => (string) $this->userId,
                'username' => 'TestUser',
                'email' => 'test_user@fakemail.com'
            ]
        ]);
    }

    /**
     * I should be able to get events without authorization
     *
     * @return void
     */
    public function testGetEventFeed()
    {        
        $this->get('/api/events')
             ->assertResponseOk();

        // Assert response contains an array called 'events'
        $responseJson = json_decode($this->response->content());
        $this->assertObjectHasAttribute('events', $responseJson);
        $this->assertTrue(is_array($responseJson->events));
        // Assert on structure of first event
        $this->assertObjectHasAttribute('id', $responseJson->events[0]);
        $this->assertObjectHasAttribute('title', $responseJson->events[0]);
        $this->assertObjectHasAttribute('location', $responseJson->events[0]);
        $this->assertObjectHasAttribute('link', $responseJson->events[0]);
        $this->assertObjectHasAttribute('body', $responseJson->events[0]);
        $this->assertObjectHasAttribute('created_at', $responseJson->events[0]);
        $this->assertObjectHasAttribute('updated_at', $responseJson->events[0]);
        $this->assertObjectHasAttribute('id', $responseJson->events[0]->author);
        $this->assertObjectHasAttribute('username', $responseJson->events[0]->author);
        $this->assertObjectHasAttribute('email', $responseJson->events[0]->author);

        // should see the created event in list
        $this->shouldReturnJson();
        $this->seeJsonContains([
            'id' => (string) $this->eventId,
            'title' => "Test Event",
            'time' => $this->eventTime, 
            'location' => "Erie Hall",
            'link' => "http://uwindsor.ca/",
            'body' => "A super fun test event.",
            'author' => [
                'id' => (string) $this->userId,
                'username' => 'TestUser',
                'email' => 'test_user@fakemail.com'
            ]
        ]);
    }

    /**
     * As an authorized user, I should be able to post an event
     *
     * @return void
     */
    public function testCreateEvent()
    {
        // Make an event request with full details
        $event = [
            'title' => "Test Post Event",
            'time' => Carbon::now()->addDays(1)->toDateTimeString(),
            'location' => "CAW",
            'link' => "http://uwindsor.ca/",
            'body' => "A superer fun test event."
        ];

        $headers = ['HTTP_AUTHORIZATION' => 'Bearer '.$this->token];
        $requestBody = compact('event');

        // Make post request
        $this->post('/api/events', $requestBody, $headers)
             ->assertResponseOk();

        // Response should contain all of the same fields as request
        $this->shouldReturnJson();
        $this->seeJsonContains($event);

        // Assert that the id was also returned in event object
        $responseJson = json_decode($this->response->content());
        $this->assertObjectHasAttribute('id', $responseJson->event);
    }

    /**
     * As an authorized user, I should be able to delete my event
     *
     * @return void
     */
    public function testDeleteEvent()
    {
        $headers = ['HTTP_AUTHORIZATION' => 'Bearer '.$this->token];

        // Make delete request on the event created in setup()
        $this->delete('/api/events/'.$this->eventId, [], $headers)
             ->assertResponseOk();

        $this->shouldReturnJson();
        $this->seeJsonEquals(['message' => 'delete_successful']);
    }

}
