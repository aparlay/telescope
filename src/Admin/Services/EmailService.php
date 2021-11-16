<?php

namespace Aparlay\Core\Admin\Services;

use Aparlay\Core\Admin\Models\Email;
use Aparlay\Core\Admin\Repositories\EmailRepository;
use Aparlay\Core\Helpers\ActionButtonBladeComponent;
use Aparlay\Core\Helpers\Cdn;
use Illuminate\Http\Request;

class EmailService extends AdminBaseService
{
    protected EmailRepository $emailRepository;

    public function __construct()
    {
        $this->emailRepository = new EmailRepository(new Email());

        $this->filterableField = ['user.username', 'to', 'status','created_at'];
        $this->sorterableField = ['user.username', 'to', 'type', 'status', 'created_at'];
    }

    /**
     * @return mixed
     */
    public function getFilteredEmail(): mixed
    {
        $offset = (int) request()->get('start');
        $limit = (int) request()->get('length');
        $dateRangeFilter = null;

        $filters = $this->getFilters();
        $sort = $this->tableSort();

        if (! empty($filters)) {
            if (isset($filters['created_at'])) {
                $dateRangeFilter = $this->getDateRangeFilter($filters['created_at']);

                unset($filters['created_at']);
            }

            $emails = $this->emailRepository->getFilteredEmail($offset, $limit, $sort, $filters,$dateRangeFilter);
        } else {
            $emails = $this->emailRepository->emailAjax($offset, $limit, $sort);
        }

        $this->appendAttributes($emails, $filters, $dateRangeFilter);

        return $emails;
    }

    /**
     * @param $emails
     * @param $filters
     */
    public function appendAttributes($emails, $filters, $dateRangeFilter = null)
    {
        $emails->total_email = $this->emailRepository->countCollection();
        $emails->total_filtered_email = ! empty($filters) || $dateRangeFilter ? $this->emailRepository->countFilteredEmail($filters,$dateRangeFilter) : $emails->total_email;

        foreach ($emails as $email) {
            $email->status_text = $email->status_name;
            $email->type_text = $email->type_name;
            $userInfo = [
                'name' => $email->user['username'],
                'avatar' => $email->user['avatar'] ? $email->user['avatar'] : '',
            ];
            $email->user_info = ActionButtonBladeComponent::getAvatarWithName($userInfo['name'], $userInfo['avatar']);
            $email->formatted_created_at = $email->created_at->toDateTimeString();
        }
    }

    /**
     * @return array
     */
    public function getEmailStatuses(): array
    {
        return $this->emailRepository->getEmailStatuses();
    }

    /**
     * @return array
     */
    public function getEmailTypes(): array
    {
        return $this->emailRepository->getEmailTypes();
    }
}
