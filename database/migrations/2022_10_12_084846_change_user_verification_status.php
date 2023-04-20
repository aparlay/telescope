<?php

use Aparlay\Core\Models\Enums\UserVerificationStatus;
use Aparlay\Core\Models\User;
use Illuminate\Database\Migrations\Migration;
use MongoDB\BSON\ObjectId;

return new class() extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $pendingIds = $verifiedIds = $unverifiedIds = $underReviewIds = [];
        foreach (User::where('verification_status', 1)->get() as $item) {
            $pendingIds[] = new ObjectId($item->_id);
        }
        foreach (User::where('verification_status', 2)->get() as $item) {
            $verifiedIds[] = new ObjectId($item->_id);
        }
        foreach (User::where('verification_status', 3)->get() as $item) {
            $unverifiedIds[] = new ObjectId($item->_id);
        }
        foreach (User::where('verification_status', 4)->get() as $item) {
            $underReviewIds[] = new ObjectId($item->_id);
        }

        User::whereIn('_id', $pendingIds)->update(['verification_status' => UserVerificationStatus::PENDING->value]);
        User::whereIn('_id', $verifiedIds)->update(['verification_status' => UserVerificationStatus::VERIFIED->value]);
        User::whereIn('_id', $unverifiedIds)->update(['verification_status' => UserVerificationStatus::UNVERIFIED->value]);
        User::whereIn('_id', $underReviewIds)->update(['verification_status' => UserVerificationStatus::UNDER_REVIEW->value]);

        User::where('verification_status', null)->update(['verification_status' => UserVerificationStatus::UNVERIFIED->value]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        return true;
    }
};
