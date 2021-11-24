<?php

namespace Aparlay\Core\Admin\Services;

use Aparlay\Core\Admin\Models\Setting;
use Aparlay\Core\Admin\Repositories\SettingRepository;
use Aparlay\Core\Helpers\ActionButtonBladeComponent;
use Illuminate\Support\Arr;

class SettingService extends AdminBaseService
{
    protected SettingRepository $settingRepository;

    public function __construct()
    {
        $this->settingRepository = new SettingRepository(new Setting());
        $this->filterableField = ['group', 'title', 'created_at'];
        $this->sorterableField = ['group', 'title', 'created_at'];
    }

    /**
     * @return mixed
     */
    public function getFilteredSettings(): mixed
    {
        $offset = (int) request()->get('start');
        $limit = (int) request()->get('length');

        $filters = $this->getFilters();
        $sort = $this->tableSort();
        $dateRangeFilter = null;

        if (! empty($filters)) {
            if (isset($filters['created_at'])) {
                $dateRangeFilter = $this->getDateRangeFilter($filters['created_at']);
                unset($filters['created_at']);
            }
            $settings = $this->settingRepository->getFilteredSettings($offset, $limit, $sort, $filters, $dateRangeFilter);
        } else {
            $settings = $this->settingRepository->all($offset, $limit, $sort);
        }

        $this->appendAttributes($settings, $filters, $dateRangeFilter);

        return $settings;
    }

    /**
    * @param $settings
    * @param $filters
    * @param $dateRangeFilter
    */
    public function appendAttributes($settings, $filters, $dateRangeFilter)
    {
        $settings->total_settings = $this->settingRepository->countCollection();
        $settings->total_filtered_settings = ! empty($filters) || $dateRangeFilter ? $this->settingRepository->countFilteredSettings($filters, $dateRangeFilter) : $settings->total_settings;

        foreach ($settings as $setting) {
            $setting->value = Arr::accessible($setting->value) ? 'array' : $setting->value;
            $setting->date_formatted = $setting->created_at->toDateTimeString();
            $setting->action = ActionButtonBladeComponent::getViewActionButton($setting->_id, 'setting');
        }
    }

    /**
     * @param $id
     * @return mixed
     */
    public function find($id): mixed
    {
        return $this->settingRepository->find($id);
    }

    public function getSettingGroups()
    {
        return Arr::pluck($this->settingRepository->getSettingGroup(), 'group');
    }

    public function update($id)
    {
        $data = request()->only(['group', 'title']);

        $data['value'] = request()->input('type') === 'json' ?
                        json_decode(request()->input('value'), JSON_PRETTY_PRINT) :
                        request()->input('value');
        return $this->settingRepository->update($data, $id);
    }
}
