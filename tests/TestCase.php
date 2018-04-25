<?php

class TestCase extends Illuminate\Foundation\Testing\TestCase
{
    /**
     * The base URL to use while testing the application.
     *
     * @var string
     */
    protected $baseUrl = 'http://localhost';

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

        return $app;
    }

    public function setUp()
    {
        parent::setUp();
        Artisan::call('migrate');
    }

    public function tearDown()
    {
        Artisan::call('migrate:reset');
        parent::tearDown();
    }

    /**
     * Asserts response json contains given structure
     * Ported from future version of laravel for 5.1 - use with caution
     * @param  array|null $structure    
     * @param  stdObject  $responseJson
     * @return TestCase   $this                  
     */
    protected function assertJsonStructure(array $structure = null, $responseJson = null) {
        if (is_null($structure)) {
            return $this->assertJson($this->json());
        }

        if (is_null($responseJson)) {
            $responseJson = json_decode($this->response->content());
        }

        foreach ($structure as $key => $value) {
            if (is_array($value) && $key === '*') {
                $this->assertInternalType('array', $responseJson);
                foreach ($responseJson as $responseJsonItem) {
                    $this->assertJsonStructure($structure['*'], $responseJsonItem);
                }
            } else if (is_array($value)) {
                $this->assertObjectHasAttribute($key, $responseJson);
                $this->assertJsonStructure($structure[$key], $responseJson->$key);
            } else {
                $this->assertObjectHasAttribute($value, $responseJson);
            }
        }

        return $this;
    }
}
