<?php

use Fixtures\Hello\Actions\World\Handler;
use Fixtures\Hello\Actions\World\UserHandler;
use Fixtures\Hello\Models\User;
use LaravelUi5\Core\Exceptions\InvalidPathException;
use LaravelUi5\Core\Services\ParameterResolver;

describe('ParameterResolver', function () {

    it('resolves a model path parameter', function () {
        $user = new User(['id' => 1]);

        Route::post('/api/user/{uri?}', function () {
            return response()->noContent();
        });

        $this->post('/api/user/1');

        $request = request();

        $resolver = new ParameterResolver($request);

        $handler = new UserHandler();

        $resolved = $resolver->resolve($handler);

        expect($resolved)
            ->toBeArray()
            ->toHaveKey('user')
            ->and($resolved['user'])
            ->toBeInstanceOf(User::class)
            ->and($user->id)
            ->toBe(1);
    });

    it('throws when path segments are missing', function () {
        Route::post('/api/users/toggle-lock/{uri?}', function () {
            return response()->noContent();
        });

        $this->post('/api/users/toggle-lock');

        $request = request();

        $resolver = new ParameterResolver($request);

        $handler = new UserHandler();

        $resolver->resolve($handler);
    })->throws(InvalidPathException::class);

    it('rejects empty path segments', function () {
        Route::post('/api/users/toggle-lock/{uri?}', function () {
            return response()->noContent();
        });

        $this->post('/api/users//toggle-lock');

        $request = request();

        $resolver = new ParameterResolver($request);

        $handler = new UserHandler();

        $resolver->resolve($handler);
    })->throws(InvalidPathException::class);

    it('throws when no matching parameter attribute exists', function () {
        Route::post('/api/user/{uri?}', function () {
            return response()->noContent();
        });

        $this->post('/api/user/1');

        $request = request();

        $resolver = new ParameterResolver($request);

        $handler = new Handler();

        $resolver->resolve($handler);
    })->throws(InvalidPathException::class);

});

