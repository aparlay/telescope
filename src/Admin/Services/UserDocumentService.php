<?php

namespace Aparlay\Core\Admin\Services;

use Aparlay\Core\Admin\Dto\AdminUserDocumentDTO;
use Aparlay\Core\Admin\Repositories\UserDocumentRepository;
use Aparlay\Core\Models\Enums\UserDocumentStatus;
use Aparlay\Core\Models\Enums\UserDocumentType;
use Aparlay\Core\Models\UserDocument;

class UserDocumentService extends AdminBaseService
{
    /** @var UserDocumentRepository */
    private $repo;

    public  $filterableField = ['status', 'type'];
    public  $sorterableField = ['status', 'type'];


    public function __construct(UserDocumentRepository $userDocumentRepository)
    {
        $this->repo = $userDocumentRepository;
    }


    public function update(UserDocument $userDocument, AdminUserDocumentDTO $dto)
    {
        $userDocument->status = (int) $dto->status;

        if ((int) $dto->status === UserDocumentStatus::REJECTED->value) {
            $userDocument->reject_reason = $dto->reject_reason;
        }
        $userDocument->save();

        $user = $userDocument->creatorObj;

        /** @var UserDocumentRepository $userDocumentRepository */
        $userDocumentRepository = app()->make(UserDocumentRepository::class);
        $userDocumentRepository->updateCounters($user);

        return $userDocument;
    }
    public function getStatuses()
    {
        return [
            UserDocumentStatus::REJECTED->value => UserDocumentStatus::REJECTED->label(),
            UserDocumentStatus::PENDING->value => UserDocumentStatus::PENDING->label(),
            UserDocumentStatus::APPROVED->value => UserDocumentStatus::APPROVED->label(),
        ];
    }

    /**
     * @return mixed
     */
    public function fetchFiltered($offset, $limit): mixed
    {
        $filters = $this->getFilters();

        $sort = $this->tableSort();

        if (! empty($filters)) {
            $documents = $this->repo->getFiltered($offset, $limit, $sort, $filters);
        } else {
            $documents = $this->repo->all($offset, $limit, $sort);
        }

        $this->appendAttributes($documents, $filters);

        return $documents;
    }

    /**
     * @param $users
     * @param $filters
     * @param $dateRangeFilter
     */
    public function appendAttributes($documents, $filters)
    {
        $documents->total_records =  $this->repo->countAll();
        $documents->total_filtered = $this->repo->countFiltered($filters);

        foreach ($documents as $document) {
            $document->status_label = $document->status_label;
            $document->type_label = $document->type_label;
            $document->url = $document->temporaryUrl();
        }

    }

}
