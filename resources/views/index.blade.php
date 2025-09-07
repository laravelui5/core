@php
    /** @var \LaravelUi5\Core\Ui5\Contracts\Ui5AppInterface $app */
@endphp
    <!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $app->getTitle() }}</title>

    {{-- Optionale Favicon/Manifest-Elemente – zentral verwaltbar --}}
    @includeIf('ui5::meta')

    {{-- UI5 Bootstrap --}}
    <script
        id="sap-ui-bootstrap"
        src="https://sdk.openui5.org/{{ config('ui5.version', '1.136.1') }}/resources/sap-ui-core.js"
        data-sap-ui-resourceroots='@json($roots, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)'
        @foreach($app->getUi5BootstrapAttributes() as $key => $value)
            data-sap-ui-{{ $key }}="{{ $value }}"
        @endforeach
    ></script>

    {{-- Optionales Konfigurationsscript (sap.ui.loader.config etc.) --}}
    @if($app->getAdditionalHeadScript())
        <script>{!! $app->getAdditionalHeadScript() !!}</script>
    @endif

    {{-- Optionaler inline-CSS-Code aus der MicroApp --}}
    @if($app->getAdditionalInlineCss())
        <style>{!! $app->getAdditionalInlineCss() !!}</style>
    @endif
</head>

<body class="sapUiBody">
<div id="content" data-sap-ui-component data-name="{{ $app->getNamespace() }}"></div>
</body>
</html>

