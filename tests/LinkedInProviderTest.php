<?php

use Mockery as M;
use SocialNorm\LinkedIn\LinkedInProvider;
use SocialNorm\Request;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Subscriber\Mock as SubscriberMock;

class LinkedInProviderTest extends TestCase
{
    private function getStubbedHttpClient($responses = [])
    {
        $client = new HttpClient;
        $mockSubscriber = new SubscriberMock($responses);
        $client->getEmitter()->attach($mockSubscriber);
        return $client;
    }

    /** @test */
    public function it_can_retrieve_a_normalized_user()
    {
        $client = $this->getStubbedHttpClient([
            __DIR__ . '/_fixtures/linkedin_accesstoken.txt',
            __DIR__ . '/_fixtures/linkedin_user.txt',
        ]);

        $provider = new LinkedInProvider([
            'client_id' => 'abcdefgh',
            'client_secret' => '12345678',
            'redirect_uri' => 'http://example.com/login',
        ], $client, new Request(['code' => 'abc123']));

        $user = $provider->getUser();

        $this->assertEquals('8nfMFuscJM', $user->id);
        $this->assertEquals('adam.wathan@example.com', $user->nickname);
        $this->assertEquals('Adam Wathan', $user->full_name);
        $this->assertEquals('adam.wathan@example.com', $user->email);
        $this->assertEquals('https://media.licdn.com/mpr/mprx/0_0-CI4P6O_58pxM28lv_44Ab-i8NSPVg8gznN4AXfp58yuYEhPqtFnlK3DaqPrjyuOA8zcnnIzV5T', $user->avatar);
        $this->assertEquals('AlI7sWwtEINHw6-y_EcX_QWCOXll-1XOY_j8SjYiJp-N-GLp-MtpGAfCiaJVQhj7F7DnfiIcvSUGYTwJA45jzkO87MCCkNzQ57z9-DqCpI_7uE2rMJFXqgKb3U-t5ntDZrB1RC_Cr_S_DYsI3-KOpUnJOFibY_58W6Ii6ljW34fE0uXx9MN', $user->access_token);
    }

    /**
     * @test
     * @expectedException SocialNorm\Exceptions\ApplicationRejectedException
     */
    public function it_fails_to_retrieve_a_user_when_the_authorization_code_is_omitted()
    {
        $client = $this->getStubbedHttpClient([
            __DIR__ . '/_fixtures/linkedin_accesstoken.txt',
            __DIR__ . '/_fixtures/linkedin_user.txt',
        ]);

        $provider = new LinkedInProvider([
            'client_id' => 'abcdefgh',
            'client_secret' => '12345678',
            'redirect_uri' => 'http://example.com/login',
        ], $client, new Request([]));

        $user = $provider->getUser();
    }
}
