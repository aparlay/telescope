@php
    use App\Models\Media;
    use Aparlay\Core\Models\Enums\MediaStatus;
    use \Illuminate\Support\Arr;

@endphp

<div class="medias-table">
    <div class="filters pb-3">
        <div class="row">
            <div class="col-md-6 pt-4">
                <h4>
                    Media

                    <button @class([
                    'btn btn-sm',
                    'btn-info' => in_array(Arr::get($sort, 'sort_scores.guest'), [1, -1]),
                    'btn-outline-info' => !in_array(Arr::get($sort, 'sort_scores.guest'), [1, -1])])
                            wire:model="sort.sort_scores.guest"
                            wire:click="sort('sort_scores.guest', -1)">
                        Ordered By New Guest {{ Arr::get($sort, 'sort_scores.guest') == 1 ? '↑' : ''}}{{ Arr::get($sort, 'sort_scores.guest') == -1 ? '↓' : ''}}
                    </button>
                    <button @class([
                        'btn btn-sm',
                        'btn-dark' => in_array(Arr::get($sort, 'sort_scores.returned'), [1, -1]),
                        'btn-outline-dark' => !in_array(Arr::get($sort, 'sort_scores.returned'), [1, -1])])
                            wire:model="sort.sort_scores.returned"
                            wire:click="sort('sort_scores.returned', -1)">
                        Ordered By Returned {{ Arr::get($sort, 'sort_scores.returned') == 1 ? '↑' : ''}}{{ Arr::get($sort, 'sort_scores.returned') == -1 ? '↓' : ''}}
                    </button>
                    <button @class([
                        'btn btn-sm',
                        'btn-primary' => in_array(Arr::get($sort, 'sort_scores.registered'), [1, -1]),
                        'btn-outline-primary' => !in_array(Arr::get($sort, 'sort_scores.registered'), [1, -1])])
                            wire:model="sort.sort_scores.registered"
                            wire:click="sort('sort_scores.registered', -1)">
                        Ordered By Login {{ Arr::get($sort, 'sort_scores.registered') == 1 ? '↑' : ''}}{{ Arr::get($sort, 'sort_scores.registered') == -1 ? '↓' : ''}}
                    </button>
                </h4>
            </div>

            <div class="col-md-2">
                <label for="">Start Date</label>
                <x-date-picker
                        wire:model.lazy="filter.created_at.start"
                        autocomplete="off"
                        placeholder="Start"
                />
            </div>
            <div class="col-md-2">
                <label for="">End Date</label>
                <x-date-picker
                        wire:model.lazy="filter.created_at.end"
                        autocomplete="off"
                        placeholder="End"
                />
            </div>
            <div class="col-md-2 ml-auto">
                <label for="">Per Page</label>
                <x-wire-dropdown-list :wire-model="'perPage'" :show-any="false"
                                      :options="[5 => 5, 10 => 10, 15 => 15]"/>
            </div>
        </div>
    </div>

    <table class="table table-striped">
        <tbody>
        <tr>
            <th class="col-md-2">
                <div>
                    <x-sortable-column-header :sort="$sort" :fieldName="'file'" :fieldLabel="'Cover'"/>
                </div>
            </th>
            <td @class(['col-md-2', 'd-none' => $hiddenFields['creator_username']])>
                <div>
                    <x-sortable-column-header :sort="$sort" :fieldName="'creator.username'" :fieldLabel="'Creator'"/>
                    <input class="form-control" type="text" wire:model="filter.creator_username"/>
                </div>
            </td>

            <td class="col-md-1">
                <div>
                    <x-sortable-column-header :sort="$sort" :fieldName="'status'" :fieldLabel="'Status'"/>
                    <select class="form-control" wire:model="filter.status">
                        <option value="">Any</option>
                        @foreach(Media::getStatuses() as $value => $label)
                            <option value="{{$value}}">{{$label}}</option>
                        @endforeach
                    </select>
                </div>
            </td>
            <td class="col-md-1">
                <div>
                    <label for="">Stats</label>
                </div>
            </td>
            <td class="col-md-1">
                <div>
                    <label for="">Score</label>
                </div>
            </td>
            <td class="col-md-2">
                <div>
                    <x-sortable-column-header :sort="$sort" :fieldName="'created_at'" :fieldLabel="'Created At'"/>
                </div>
            </td>
            <td class="col-md-1">
                <div>
                    <label for="">Details</label>
                </div>
            </td>

        </tr>

        @foreach($medias as $media)
            <tr>
                <td>
                    <x-media-cover :media="$media"/>
                </td>
                <td @class(['col-md-2', 'd-none' => $hiddenFields['creator_username']])>
                    <x-username-avatar :user="$media->creatorObj"/>
                </td>
                <td>
                    <span class="badge bg-{{ MediaStatus::from($media->status)->badgeColor() }}">
                        {{ MediaStatus::from($media->status)->label() }}
                    </span>
                </td>
                <td>
                    <i class="fa fa-heart fa-fw text-danger" title="Number of Likes"></i> {{$media->like_count}} <br>
                    <i class="fa fa-eye fa-fw text-primary" title="Number of Visits"></i> {{$media->visit_count}} <br>
                    <i class="fa fa-play fa-fw text-primary" title="Number of Play"></i> {{$media->watched_count}} <br>
                    <i class="fa fa-clock fa-fw text-primary" title="Watch Duration"></i> {{ round(\Carbon\CarbonInterval::seconds($media->length_watched)->cascade()->totalHours) }}H. <br>
                    <i class="fa fa-comments fa-fw text-warning" title="Number of Comments"></i> {{$media->comment_count}} <br>
                    <i class="fa fa-money-bill-wave fa-fw text-info" title="Total Tips"></i> {{(int)$media->tips/100}}
                </td>
                <td>
                    <i class="fa fa-user-shield fa-fw text-primary" title="Score for Registered"></i> {{$media->sort_scores['registered']}} <br>
                    <i class="fa fa-user-minus fa-fw text-info" title="Score for New Guest"></i> {{$media->sort_scores['guest']}} <br>
                    <i class="fa fa-user-minus fa-fw text-dark" title="Score for Returned Guest"></i> {{$media->sort_scores['returned']}} <br>
                    <hr class="my-1">
                    <div class="text-sm-left">
                        @if (is_array($media->scores))
                            @foreach($media->scores as $score)
                                {{$score['type']}}: {{$score['score']}} <br>
                            @endforeach
                        @endif
                    </div>
                </td>
                <td>
                    {{$media->created_at}}
                </td>
                <td>
                    <div class="col-md-6">
                        <div>
                            <a class="btn btn-primary btn-sm" href="{{$media->admin_url}}" title="View"><i
                                        class="fas fa-eye"></i></a>
                        </div>
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <div class="d-flex justify-content-center">
        {{ $medias->links() }}
    </div>
</div>
