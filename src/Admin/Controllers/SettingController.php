<?php

namespace Aparlay\Core\Admin\Controllers;

use Aparlay\Core\Admin\Models\Setting;
use Aparlay\Core\Admin\Requests\SettingRequest;
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

    public function index(): View|Factory|Application
    {
        return view('default_view::admin.pages.setting.index');
    }

    public function view(Setting $setting)
    {
        $setting = $this->settingService->find($setting->_id);
        $groups  = $this->settingService->getSettingGroups();

        return view('default_view::admin.pages.setting.view', compact('setting', 'groups'));
    }

    public function update(SettingRequest $request, Setting $setting)
    {
        $this->settingService->update($setting->_id);

        return back()->with('success', 'Setting updated successfully.');
    }

    public function create()
    {
        $groups = $this->settingService->getSettingGroups();

        return view('default_view::admin.pages.setting.create', compact('groups'));
    }

    public function store(SettingRequest $request)
    {
        $create = $this->settingService->create();

        if (!$create) {
            return back()->withErrors(['error' => 'Setting already exist. Please update value for that specific setting.']);
        }

        return redirect()->route('core.admin.setting.view', ['setting' => $create->_id])->with([
            'success' => 'Successfully added setting.',
        ]);
    }

    public function delete(Setting $setting)
    {
        if ($this->settingService->delete($setting->_id)) {
            return back()->with(['success' => 'Successfully deleted setting']);
        }

        return back()->with(['error' => 'Delete setting failed']);
    }
}
