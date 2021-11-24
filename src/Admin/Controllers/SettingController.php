<?php

namespace Aparlay\Core\Admin\Controllers;

use Aparlay\Core\Admin\Models\Setting;
use Aparlay\Core\Admin\Requests\UpdateSettingRequest;
use Aparlay\Core\Admin\Resources\SettingResource;
use Aparlay\Core\Admin\Services\SettingService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;

class SettingController
{
    protected $settingService;

    public function __construct(SettingService $settingService)
    {
        $this->settingService = $settingService;
    }

    /**
     * @return Application|Factory|View
     */
    public function index(): View|Factory|Application
    {
        return view('default_view::admin.pages.setting.index');
    }

    /**
     * @return SettingResource
     */
    public function indexAjax(): SettingResource
    {
        return new SettingResource($this->settingService->getFilteredSettings());
    }

    public function view(Setting $setting)
    {
        $setting = $this->settingService->find($setting->_id);
        $groups = $this->settingService->getSettingGroups();
        return view('default_view::admin.pages.setting.view', compact('setting', 'groups'));
    }

    public function update(UpdateSettingRequest $request, Setting $setting)
    {
       $this->settingService->update($setting->_id);

        return back()->with('success', 'Setting updated successfully.');
    }
}
