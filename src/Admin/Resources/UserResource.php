<?php

namespace Aparlay\Core\Admin\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    use SimpleUserTrait;

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $users = [];
        foreach ($this->resource as $user) {
            $users[] = $this->createSimpleUser($user);
        }

        return [
            'aaData' => $users,
            'iTotalDisplayRecords' => $this->total_filtered_users,
            'iTotalRecords' => $this->total_users,
        ];
    }
}
