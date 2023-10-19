<?php

namespace Laravel\Jetstream\Http\Middleware;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;
use Laravel\Fortify\Features;
use Laravel\Jetstream\Jetstream;

class ShareInertiaData
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  callable  $next
     * @return \Illuminate\Http\Response
     */
    public function handle($request, $next)
    {
        Inertia::share(array_filter([
            'jetstream' => function () use ($request) {
                $user = $request->user();

                return [
                    'canCreateOrganizations' => $user &&
                                        Jetstream::userHasOrganizationFeatures($user) &&
                                        Gate::forUser($user)->check('create', Jetstream::newOrganizationModel()),
                    'canManageTwoFactorAuthentication' => Features::canManageTwoFactorAuthentication(),
                    'canUpdatePassword' => Features::enabled(Features::updatePasswords()),
                    'canUpdateProfileInformation' => Features::canUpdateProfileInformation(),
                    'hasEmailVerification' => Features::enabled(Features::emailVerification()),
                    'flash' => $request->session()->get('flash', []),
                    'hasAccountDeletionFeatures' => Jetstream::hasAccountDeletionFeatures(),
                    'hasApiFeatures' => Jetstream::hasApiFeatures(),
                    'hasOrganizationFeatures' => Jetstream::hasOrganizationFeatures(),
                    'hasTermsAndPrivacyPolicyFeature' => Jetstream::hasTermsAndPrivacyPolicyFeature(),
                    'managesProfilePhotos' => Jetstream::managesProfilePhotos(),
                ];
            },
            'auth' => [
                'user' => function () use ($request) {
                    if (! $user = $request->user()) {
                        return;
                    }

                    $userHasOrganizationFeatures = Jetstream::userHasOrganizationFeatures($user);

                    if ($user && $userHasOrganizationFeatures) {
                        $user->currentOrganization;
                    }

                    return array_merge($user->toArray(), array_filter([
                        'all_organizations' => $userHasOrganizationFeatures ? $user->allOrganizations()->values() : null,
                    ]), [
                        'two_factor_enabled' => Features::enabled(Features::twoFactorAuthentication())
                            && ! is_null($user->two_factor_secret),
                    ]);
                },
            ],
            'errorBags' => function () {
                return collect(optional(Session::get('errors'))->getBags() ?: [])->mapWithKeys(function ($bag, $key) {
                    return [$key => $bag->messages()];
                })->all();
            },
        ]));

        return $next($request);
    }
}
