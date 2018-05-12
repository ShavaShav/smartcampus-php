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
    private $eventTimeStart = null; // timestamp for event
    private $eventTimeEnd = null;

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

        $this->eventTimeStart = Carbon::now()->addDays(1)->toDateTimeString();

        $this->eventTimeEnd = Carbon::now()->addDays(1)
            ->addHours(1)
            ->toDateTimeString();

        $event = Event::create([
            'title' => "Test Event",
            'start_time' => $this->eventTimeStart,
            'end_time' => $this->eventTimeEnd,  
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
        $this->seeJsonStructure([
            'event' => [
                'id',
                'title',
                'location',
                'start_time',
                'end_time',
                'link',
                'body',
                'created_at',
                'updated_at',
                'author' => [
                    'id',
                    'username',
                    'email'
                ]
            ]
        ]);
        
        // Assert on actual event details
        $this->seeJsonContains([
            'id' => $this->eventId,
            'title' => "Test Event",
            'start_time' => $this->eventTimeStart,
            'end_time' => $this->eventTimeEnd,   
            'location' => "Erie Hall",
            'link' => "http://uwindsor.ca/",
            'body' => "A super fun test event.",
            'author' => [
                'id' => $this->userId,
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

        // Assert on JSON structure
        $this->shouldReturnJson();
        $this->seeJsonStructure([
            'events' => [
                '*' => [
                    'id',
                    'title',
                    'location',
                    'start_time',
                    'end_time',
                    'link',
                    'body',
                    'created_at',
                    'updated_at',
                    'author' => [
                        'id',
                        'username',
                        'email'
                    ]
                ]
            ]
        ]);

        // should see the created event in list
        $this->seeJsonContains([
            'id' => $this->eventId,
            'title' => "Test Event",
            'start_time' => $this->eventTimeStart,
            'end_time' => $this->eventTimeEnd, 
            'location' => "Erie Hall",
            'link' => "http://uwindsor.ca/",
            'body' => "A super fun test event.",
            'author' => [
                'id' => $this->userId,
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
            'start_time' => Carbon::now()->addDays(1)->toDateTimeString(),
            'end_time' => Carbon::now()->addDays(1)->addHours(1)->toDateTimeString(),
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
        $this->seeJsonStructure([
            'event' => [
                'id',
                'title',
                'location',
                'start_time',
                'end_time',
                'link',
                'body',
                'created_at',
                'updated_at',
                'author' => [
                    'id',
                    'username',
                    'email'
                ]         
            ]
        ]);
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
