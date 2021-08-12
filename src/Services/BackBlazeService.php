<?php

namespace Aparlay\Core\Services;

use Illuminate\Support\Facades\Http;

class BackBlaze
{
    public function generateToken()
    {
        $applicationKeyId = config('filesystems.disks.b2-videos.key'); // Obtained from your B2 account page
        $applicationKey = config('filesystems.disks.b2-videos.secret'); // Obtained from your B2 account page
        $credentials = base64_encode($applicationKeyId.':'.$applicationKey);
        $responseFromGet = Http::withToken($credentials)
            ->get('https://api.backblazeb2.com/b2api/v2/b2_authorize_account');

        $result = [];
        if ($responseFromGet->successful()) {
            $result = [
                'application_key_id' => $applicationKeyId,
                'account_id' => $responseFromGet['accountId'],
                'api_url' => $responseFromGet['apiUrl'],
                'bucket_name' => $responseFromGet['allowed']['bucketName'],
                'bucket_id' => $responseFromGet['allowed']['bucketId'],
                'authorization_token' => $responseFromGet['authorizationToken'],
                'absolute_minimum_part_size' => $responseFromGet['absoluteMinimumPartSize'],
                'recommended_part_size' => $responseFromGet['recommendedPartSize'],
            ];

            $responseFromPost = Http::withToken($responseFromGet['authorizationToken'])
                ->post($responseFromGet['apiUrl'].'/b2api/v2/b2_get_upload_url', [
                    'bucketId' => $responseFromGet['allowed']['bucketId'],
                ]);

            $result['upload_url'] = $responseFromPost['uploadUrl'];
            $result['authorization_token'] = $responseFromPost['authorizationToken'];
        }

        return $result;
    }
}
