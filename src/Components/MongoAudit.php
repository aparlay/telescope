<?php

namespace Aparlay\Core\Components;

use Aparlay\Core\Models\BaseModel;

class MongoAudit extends BaseModel implements \OwenIt\Auditing\Contracts\Audit
{
    use \OwenIt\Auditing\Audit;

    /**
     * {@inheritdoc}
     */
    protected $guarded = [];

    /**
     * {@inheritdoc}
     */
    protected $casts = [
        'old_values'   => 'json',
        'new_values'   => 'json',
    ];

    /**
     * {@inheritdoc}
     */
    public function auditable()
    {
        return $this->morphTo('auditable.');
    }

    /**
     * {@inheritdoc}
     */
    public function user()
    {
        return $this->morphTo('user.');
    }
}
