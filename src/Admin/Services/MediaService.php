<?php

namespace Aparlay\Core\Admin\Services;

use Aparlay\Core\Admin\Models\Media;
use Aparlay\Core\Admin\Repositories\MediaRepository;
use Aparlay\Core\Helpers\Cdn;

class MediaService extends AdminBaseService
{
    protected MediaRepository $mediaRepository;

    public function __construct()
    {
        $this->mediaRepository = new MediaRepository(new Media());
        $this->filterableField = ['creator.username', 'status'];
        $this->sorterableField = ['creator.username', 'status', 'created_at'];
        $this->sortDefault = ['created_by'=>'desc'];
    }

    public function getStatuses(): array
    {
        return $this->mediaRepository->getStatuses();
    }

    public function fillListAttributes($mediaCollection)
    {
        foreach ($mediaCollection['data'] as &$collect) {
            $collect['status'] = ! empty($collect['status']) ? '<span class="badge bg-'.$collect['status']['color'].'">'.$collect['status']['text'].'</span>' : '';
            $collect['file'] = '<img src="'.Cdn::cover(! empty($collect['file']) ? $collect['file'].'.jpg?width=100' : 'default.jpg?width=100').'" />';
            $collect['creator_username'] = $collect['creator.username'];
            $collect['detail_url'] = '<a class="btn btn-primary btn-sm" href="'.'/media/view/'.$collect['id'].'" title="View"><i class="fas fa-eye"></i> View</a>';
        }

        return $mediaCollection;
    }

    public function list()
    {
        $result = [];
        $this->fillTableColumns();
        $sort = $this->tableSort();

        $MediaSearch = request()->MediaSearch ?? [];

        if (! empty($MediaSearch)) {
            $filter = $this->cleanFilterFields($MediaSearch);
        }

        if (! empty($filter)) {
            //return $this->fillListAttributes($this->mediaRepository->getFilteredMedia($filter, $sort));
        } else {
            $result = $this->fillListAttributes($this->buildData($this->mediaRepository->all($sort)));
        }

        return $result;
    }
}
