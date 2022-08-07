<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\API\CQC;
use App\Models\Provider;

class ProvidersController extends Controller
{
    // Retrieve all the providers and pagination from the CQC API. 
    public function index(Request $request)
    {
        $response = CQC::request('providers', $request->except(['localOnly', 'orderBy']));

        if($request->has('localOnly') && $request->localOnly == 'true')
        {
            $orderBy = $request->has('orderBy') ? $request->orderBy : 'name';
            $order = 'asc';
            if(strpos($orderBy, '_desc') !== false)
            {
                $order = 'desc';
                $orderBy = str_replace('_desc', '', $orderBy);
            }

            $response = Provider::orderBy($orderBy, $order)->paginate($request->perPage ?? 30);
            
            $response->getCollection()->transform(function ($provider) {
                $provider['providerId'] = $provider->uuid;
                $provider['providerName'] = $provider->name;
                $provider['local'] = Provider::where('uuid', $provider['uuid'])->first();

                return $provider;
            });

            return $response;
        }

        $response['providers'] = collect($response['providers'])->map(function ($provider) {
            $provider['local'] = Provider::where('uuid', $provider['providerId'])->first();
            
            return $provider;
        });

        $response['localProviders'] = Provider::count();
        return $response;
    }

    // Retrieve a provider from the CQC API.
    // - if it doesn't exist locally, it will be stored.
    // - if it does exist locally and it's older than a week, it will be updated.
    public function show($UUID, Request $request)
    {
        $provider = CQC::request('providers/' . $UUID);

        if(!$provider){
            return response()->json(['error' => 'Provider not found'], 404);
        }

        $localProvider = $this->checkIfExists($UUID);

        if($localProvider)
        {
            if(strtotime($localProvider->updated_at) < strtotime('-1 week') || $localProvider->updated_at == null)
            {
                $localProvider->updateFromAPI($provider);

            } else if($request->has('forceUpdate') && $request->forceUpdate == 'true')
            {
                $localProvider->updateFromAPI($provider);
            }

            return $localProvider;
        }

        $newProvider = new Provider();
        $newProvider->uuid = $UUID;
        $newProvider->name = $provider['name'];
        $newProvider->type = $provider['type'];
        $newProvider->postcode = $provider['postalCode'];
        $newProvider->status = ($provider['registrationStatus'] == 'Registered') ? 'Registered' : 'Deregistered';
        $newProvider->info = $provider;
        $newProvider->save();

        return Provider::where('uuid', $UUID)->first();
    }

    // Search the locally available providers.
    public function searchProviders($query)
    {
        $providers = Provider::where('name', 'like', '%' . $query . '%')->get();

        return $providers;
    }

    function checkIfExists($UUID)
    {
        return Provider::where('uuid', $UUID)->first();
    }
}