<?php

namespace Aparlay\Core\Admin\Services;

use Aparlay\Core\Admin\Models\Setting;
use Aparlay\Core\Admin\Repositories\SettingRepository;
use Aparlay\Core\Helpers\ActionButtonBladeComponent;
use Aparlay\Core\Helpers\DT;
use Illuminate\Support\Arr;
use MongoDB\BSON\UTCDateTime;

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
            $setting->value = ActionButtonBladeComponent::castDisplayValue($setting->value);
            $setting->date_formatted = $setting->created_at->toDateTimeString();
            $setting->action = ActionButtonBladeComponent::getViewDeleteActionButton($setting->_id, 'setting');
        }
    }

    /**
     * @param $id
     * @return mixed
     */
    public function find($id): mixed
    {
        $setting = $this->settingRepository->find($id);
        $setting->type = $this->getValueDataType($setting->value);

        return $setting;
    }

    public function getSettingGroups()
    {
        return Arr::pluck($this->settingRepository->getSettingGroup(), 'group');
    }

    public function update($id)
    {
        $data = request()->only(['group', 'title']);

        $data['value'] = $this->castValue(request()->input('value'));

        return $this->settingRepository->update($data, $id);
    }

    public function create()
    {
        $setting = $this->settingRepository->findSettingByTitleByGroup(request()->input('title'), request()->input('group'));

        if (! $setting) {
            $data = request()->only(['group', 'title']);

            $data['value'] = $this->castValue(request()->input('value'));

            return $this->settingRepository->store($data);
        } else {
            return false;
        }
    }

    public function castValue($value)
    {
        return match ((int) request()->input('type')) {
            Setting::VALUE_TYPE_STRING => (string) $value,
            Setting::VALUE_TYPE_BOOLEAN => (bool) $value,
            Setting::VALUE_TYPE_INTEGER => (int) $value,
            Setting::VALUE_TYPE_DATETIME => DT::utcDateTime($value),
            Setting::VALUE_TYPE_JSON => json_decode($value, JSON_PRETTY_PRINT),
            default => null,
        };
    }

    public function getValueDataType($value)
    {
        if ($value instanceof UTCDateTime) {
            return Setting::VALUE_TYPE_DATETIME;
        } elseif (gettype($value) == 'array') {
            return Setting::VALUE_TYPE_JSON;
        } else {
            return array_search(ucfirst(gettype($value)), Setting::getValueTypes());
        }
    }

    public function delete($id)
    {
        return $this->settingRepository->delete($id);
    }
}
