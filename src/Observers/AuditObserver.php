<?php

namespace Aparlay\Core\Observers;

use Aparlay\Core\Api\V1\Notifications\UserDeactivateAccount;
use Aparlay\Core\Casts\SimpleUserCast;
use Aparlay\Core\Events\UserAvatarChangedEvent;
use Aparlay\Core\Helpers\Cdn;
use Aparlay\Core\Helpers\IP;
use Aparlay\Core\Jobs\DeleteUserConnect;
use Aparlay\Core\Jobs\DeleteUserMedia;
use Aparlay\Core\Jobs\UpdateMedia;
use Aparlay\Core\Jobs\UpdateUserCountry;
use Aparlay\Core\Models\Audit;
use Aparlay\Core\Models\Enums\UserGender;
use Aparlay\Core\Models\Enums\UserInterestedIn;
use Aparlay\Core\Models\Enums\UserShowOnlineStatus;
use Aparlay\Core\Models\Enums\UserStatus;
use Aparlay\Core\Models\Enums\UserVerificationStatus;
use Aparlay\Core\Models\Enums\UserVisibility;
use Aparlay\Core\Models\User;
use Exception;
use Illuminate\Support\Facades\Redis;
use MongoDB\BSON\ObjectId;

class AuditObserver
{
    /**
     * Handle the User "creating" event.
     *
     * @param  Audit  $model
     * @return void
     * @throws Exception
     */
    public function saving($model): void
    {
        if (! empty($model->user_id) && is_string($model->user_id)) {
            $model->user_id = new ObjectId($model->user_id);
        }
        if (! empty($model->auditable_id) && is_string($model->auditable_id)) {
            $model->auditable_id = new ObjectId($model->auditable_id);
        }
    }
}
