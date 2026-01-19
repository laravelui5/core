{{--
    UI5 Integration Card Manifest – Blade Template

    This file defines the structure and content of a UI5 Card.
    You can use any Blade logic here (e.g., @if, @isset, {{ $variable }}, etc.)
    All dynamic values should come from the associated CardProvider or Ui5Card.

    🔗 SAP UI5 Integration Cards Explorer:
    https://ui5.sap.com/test-resources/sap/ui/integration/demokit/cardExplorer/webapp/index.html
--}}

{
  "sap.card": {
    "type": "Object",
    "header": {
      "title": "{{ $card->getTitle() }}",
      "subTitle": "{{ $card->getDescription() }}"
    },
    "content": {
      "item": {
        "title": "{{ $data['title'] }}",
        "number": "{{ $data['value'] }}",
        "unit": "{{ $data['unit'] }}"
      }
    }
  }
}
