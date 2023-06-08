<?php

namespace Tests\Routes;

use App\Models\User;
use App\Models\UserShift;
use Auth;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Xguard\Tasklist\Models\Review;

/**
 * @group supervisor
 */
class ReviewRoutesTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    const CREATE_REVIEW = 'tasklist.create-review';
    const UPDATE_REVIEW = 'tasklist.update-review';
    const DELETE_REVIEW = 'tasklist.delete-review';
    const ID = 'id';
    const TEST_NOTE = 'test note';
    const RATING_3 = 3;
    const UPDATED_REVIEW = 'Updated Review';
    const SA_REVIEWS = 'sa_reviews';

    public function setUp(): void
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
        Auth::setUser($this->user);
    }

    public function testCreateJobSiteVisitFromJobSite()
    {
        $userShift = factory(UserShift::class)->create();
        $apiCall = route(self::CREATE_REVIEW);
        $data = [
            Review::USER_SHIFT_ID => $userShift->id,
            Review::NOTE => self::TEST_NOTE,
            Review::RATING => self::RATING_3,
        ];
        $response = $this->post($apiCall, $data);
        $count = count(Review::all());
        $this->assertTrue($count == 1);
        $this->assertDatabaseHas(self::SA_REVIEWS, [Review::NOTE => self::TEST_NOTE, Review::RATING=> self::RATING_3]);
        $response->assertSuccessful()->assertJson([self::ID => 1]);
    }

    public function testUpdateJobSiteVisit()
    {
        $review = factory(Review::class)->create();
        $apiCall = route(self::UPDATE_REVIEW);
        $data = [
            Review::ID => $review->id,
            Review::NOTE => self::UPDATED_REVIEW,
            Review::RATING => self::RATING_3,
        ];
        $response = $this->patch($apiCall, $data);
        $this->assertDatabaseHas(self::SA_REVIEWS, [Review::ID => $review->id, Review::NOTE => self::UPDATED_REVIEW, Review::RATING=> self::RATING_3]);
        $response->assertOk();
    }

    public function testDeleteJobSiteVisit()
    {
        $review = factory(Review::class)->create();
        $apiCall = route(self::DELETE_REVIEW, ['reviewId' => $review->id]);
        $this->assertDatabaseHas(self::SA_REVIEWS, [Review::ID  => $review->id, Review::DELETED_AT  => null]);
        $response = $this->delete($apiCall);
        $response->assertOk();
        $this->assertDatabaseMissing(self::SA_REVIEWS, [Review::ID  => $review->id, Review::DELETED_AT  => null]);
    }
}
